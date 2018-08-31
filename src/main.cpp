#define PT_USE_TIMER

#include <stdlib.h>
#include <Arduino.h>
#include <WString.h>
#include <utility/w5100.h>
#include <Ethernet.h>
#include <EthernetClient.h>
#include <DHT.h>
#include <LiquidCrystal_I2C.h>
#include <pt.h>
#include <packet_parser.h>

const int InterruptDetectPin = 2;
const int relayOnePin = 22;
const int relayTwoPin = 23;
const int relayThreePin = 24;
const int dhtPin = 25;
const int lightSensorPin = A1;
const int groundHumSensorPin = A0;
const long uploadInterval = 1000 * 60 * 5; //MS->S S->M M->H
const long maintainEthernetInterval = 1000 * 60 * 60 * 1;
const long checkSensorInterval = 1000 * 60 * 1;

const char *serverAddress = "192.168.1.102";
const int serverPort = 81;
byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
DHT *airSensor = new DHT();
EthernetClient *ethernetClient = new EthernetClient();

bool isEthernetOk = false;
bool isConnectionOk = false;
bool isDhtOk = false;
bool isLcdOk = false;

volatile float currentAirTemp = 0;
volatile float currentAirHum = 0;
volatile int currentLightValue = 0;
volatile int currentGroundHum = 0;

struct pt uploadSensorData_ctrl;
struct pt maintainEthernet_ctrl;
struct pt readSensorData_ctrl;

//User methods
void pinTwoInterruptHandler() {

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

PT_THREAD(readSensorData(struct pt *pt)) {
    PT_BEGIN(pt);
    Serial.println("Refreshing sensor data");
    currentAirTemp = airSensor->getTemperature();
    currentAirHum = airSensor->getHumidity();
    currentGroundHum = analogRead(groundHumSensorPin);
    currentLightValue = analogRead(lightSensorPin);
    Serial.println(currentAirTemp);
    Serial.println(currentAirHum);
    Serial.println(currentGroundHum);
    Serial.println(currentLightValue);
    Serial.println("Done.");
    PT_TIMER_DELAY(pt, checkSensorInterval);
    PT_END(pt);
}

PT_THREAD(maintainEthernet(struct pt *pt)) {
    PT_BEGIN(pt);
    Serial.println("Maintaining Ethernet connection");
    Ethernet.maintain();
    Serial.println("Done.");
    PT_TIMER_DELAY(pt, maintainEthernetInterval);
    PT_END(pt);
}

PT_THREAD(uploadSensorData(struct pt *pt)) {
    PT_BEGIN(pt);
    Serial.println("Uploading sensor data");
    for (static int index = 1; index <= 5; index++) {
        if (ethernetClient->connect(serverAddress, serverPort)) {
            Serial.println("Connection established.");
            break;
        }
        else
        {
            Serial.println(String("Connection broken. Retry ") + String(index));
        }
    }
    Serial.println(String("GET /upload.php?air_temp=") + String(currentAirTemp) \
            + String("&air_hum=") + currentAirHum \
            + String("&air_light=") + currentLightValue \
            + String("&ground_hum=") + currentGroundHum \
            + String(" HTTP/1.1\r\n" \
            "Accept: text/html, */*\r\n" \
            "Host: ") + String(serverAddress) + String(":") + String(serverPort) + String("\r\n") + String( \
            "User-Agent: arduino/mega2560\r\n" \
            "Connection: close\r\n" \
            "\r\n" \
            ""
        )
    );
    ethernetClient->print(String("GET /upload.php?air_temp=") + String(currentAirTemp) \
            + String("&air_hum=") + currentAirHum \
            + String("&air_light=") + currentLightValue \
            + String("&ground_hum=") + currentGroundHum \
            + String(" HTTP/1.1\r\n" \
            "Accept: text/html, */*\r\n" \
            "Host: ") + String(serverAddress) + String(":") + String(serverPort) + String("\r\n") + String( \
            "User-Agent: arduino/mega2560\r\n" \
            "Connection: close\r\n" \
            "\r\n" \
            ""
        ));
    
    do
    {
        Serial.println(ethernetClient->readString());
    } while (ethernetClient->available());
    
    ethernetClient->stop();
    Serial.println("Done. Connection closed.");
    PT_TIMER_DELAY(pt, uploadInterval);
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
    PT_INIT(&uploadSensorData_ctrl);
    PT_INIT(&maintainEthernet_ctrl);
    PT_INIT(&readSensorData_ctrl);

    Serial.println("Init done.");
}

void loop() {
    readSensorData(&readSensorData_ctrl);
    uploadSensorData(&uploadSensorData_ctrl);
    maintainEthernet(&maintainEthernet_ctrl);
}
