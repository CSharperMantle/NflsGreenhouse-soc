#define PT_USE_SEM
#define PT_USE_TIMER

#include <stdlib.h>
#include <Arduino.h>
#include <WString.h>
#include <utility/w5100.h>
#include <Ethernet.h>
#include <EthernetClient.h>
#include <SD.h>
#include <DHT.h>
#include <LiquidCrystal_I2C.h>
#include <pt.h>
#include <packet_parser.h>

static struct SensorValue {
    int airHumidity = 0;
    int airTemperature = 0;
    int lightValue = 0;
    int groundHumValue = 0;
} sensorValue;

static const int InterruptDetectPin = 2;
static const int relayOnePin = 22;
static const int relayTwoPin = 23;
static const int relayThreePin = 24;
static const int dhtPin = 25;
static const int lightSensorPin = A0;
static const int groundHumSensorPin = A1;
static const long uploadInterval = 1800000L;//1000 * 60 * 60 * 0.5; //MS->S S->M M->H
static const long maintainEthernetInterval = 1800000L;//1000 * 60 * 60 * 2;

static struct pt ctrlSensorDataUpload;
static struct pt ctrlMaintainEthernet;

static const char *serverAddress = "192.168.1.102";
static const int serverPort = 81;
static byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

static LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
static DHT *airSensor = new DHT();
static EthernetClient *ethernetClient = new EthernetClient();
static SDFile *configFile = new SDFile();

static bool isEthernetOk = false;
static bool isConnectionOk = false;
static bool isSdOk = false;
static bool isDhtOk = false;
static bool isLcdOk = false;

//User methods
void pinTwoInterruptHandler() {

}

inline void checkSensors() {
    Serial.println("Reading sensors");
    sensorValue.lightValue = analogRead(lightSensorPin);
    sensorValue.airHumidity = airSensor->getHumidity();
    sensorValue.airTemperature = airSensor->getTemperature();
    sensorValue.groundHumValue = analogRead(groundHumSensorPin);
    Serial.println("Done.");
}

byte *readEthernet() {
    Serial.println("Reading Ethernet");
    if (!ethernetClient->connected()) {
        Serial.println("Not connected.");
        Serial.println("Done.");
        return NULL;
    }

    size_t packet_size = 1;
    byte *packet = MALLOC_HEAP(packet_size, byte);

    do
    {
        packet[packet_size - 1] = ethernetClient->read();
        packet_size ++;
        packet = REALLOC_HEAP(packet, packet_size, byte);
    } while (ethernetClient->available());
    
    Serial.println("Done.");
    return packet;
}

static int uploadSensorData(struct pt *pt) {
    PT_BEGIN(pt);
    checkSensors();
    Serial.println("Uploading sensor data");
    for (static int index = 1; index <= 5; index++) {
        if (ethernetClient->connect(serverAddress, serverPort)) {
            Serial.println("Connection established.");
            break;
        }
        else
        {
            Serial.println(String("Connection broken. Retry ") + String(index));
            PT_YIELD(pt);
        }
    }
    static String request = String("GET /upload.php?air_temp=") + String(sensorValue.airTemperature) \
        + String("&air_hum=") + String(sensorValue.airHumidity) \
        + String("&air_light=") + String(sensorValue.lightValue) \
        + String("&ground_hum=") + String(sensorValue.groundHumValue) \
        + String(" HTTP/1.1\r\n" \
        "Accept: */*\r\n" \
        "Host: ") + String(serverAddress) + String(":") + String(serverPort) + String("\r\n") + String(
        "User-Agent: arduino/mega2560\r\n" \
        "Connection: close\r\n" \
        "\r\n" \
        "");
    Serial.println(request);
    ethernetClient->print(request);
    
    do
    {
        Serial.println(ethernetClient->readString());
    } while (ethernetClient->available());
    
    ethernetClient->stop();
    Serial.println("Done. Connection closed.");
    PT_TIMER_DELAY(pt, uploadInterval);
    PT_END(pt);
}

void initSerial() {
    Serial.begin(9600);
    Serial.flush();
    Serial.println("Serial opened.");
}

void initEthernet() {
    Serial.println("Initializing Ethernet");
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(mac) == 0)
            Serial.println(String("DHCP failed. Retry ") + String(index));
        else {
            Serial.println("DHCP OK.");
            Serial.println("    Ethernet information: IP, DNS, Gateway");
            Serial.println(Ethernet.localIP());
            Serial.println(Ethernet.dnsServerIP());
            Serial.println(Ethernet.gatewayIP());
            isEthernetOk = true;
            break;
        }
    }
    Serial.println("Done.");

    Serial.println("Running tests on EthernetClient");
    for (int index = 1; index <= 5; index++) {
        if (ethernetClient->connect(serverAddress, serverPort)) {
            Serial.println("Test connection established.");
            ethernetClient->flush();
            Serial.println("Test connection flushed.");
            ethernetClient->stop();
            Serial.println("Test connection closed.");
            isConnectionOk = true;
            break;
        }
        else
        {
            Serial.println(String("Test connection broken. Retry ") + String(index));
        }
    }
    Serial.println("Done.");
}

void initSd() {
    Serial.println("Initializing SD");
    if (!SD.begin()) {
        Serial.println("SD Card init failed.");
    } else isSdOk = true;

    if (!SD.exists("config.ini")) {
        Serial.println("Config file not found.");
    }

    Serial.println("Done.");
}

void initLcd() {
    Serial.println("Initializing LCD");
    screen->begin(16, 2);
    screen->clear();
    screen->setBacklight(false);
    isLcdOk = true;
    Serial.println("Done.");
}

void initDht() {
    Serial.println("Initializing DHT");
    airSensor->setup(dhtPin, DHT::DHT_MODEL_t::DHT11);
    Serial.println(airSensor->getStatusString());
    isDhtOk = true;
    Serial.println("Done.");
}

static int maintainEthernet(struct pt *pt) {
    PT_BEGIN(pt);
    Serial.println("Maintaining Ethernet connection");
    Ethernet.maintain();
    Serial.println("Done.");
    PT_TIMER_DELAY(pt, maintainEthernetInterval);
    PT_END(pt);
}


//Main methods
void setup() {
    //Startup scripts
    initSerial();

    initEthernet();

    initLcd();

    initDht();
    //attachInterrupt(InterruptDetectPin, pinTwoInterruptHandler, CHANGE);
    PT_INIT(&ctrlMaintainEthernet);
    PT_INIT(&ctrlSensorDataUpload);

    Serial.println("Init done.");
}

void loop() {
    uploadSensorData(&ctrlSensorDataUpload);
    maintainEthernet(&ctrlMaintainEthernet);
}
