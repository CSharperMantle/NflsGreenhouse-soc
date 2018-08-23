#define DEBUG

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

struct SensorValue {
    float airHumidity;
    float airTemperature;
    int lightValue;
    int groundHumValue;
} sensorValue;

static const int InterruptDetectPin = 2;
static const int relayOnePin = 22;
static const int relayTwoPin = 23;
static const int relayThreePin = 24;
static const int dhtPin = 25;
static const int lightSensorPin = A0;
static const int groundHumSensorPin = A1;

static const char *serverAddress = "192.168.1.102";
static const int serverPort = 20000;
static byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

static LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
static DHT *airSensor = new DHT();
static EthernetClient *ethernetClient = new EthernetClient();
static SDFile *configFile = new SDFile();

static bool isEthernetOk = false;
static bool isSdOk = false;
static bool isDhtOk = false;
static bool isLcdOk = false;
static bool isConnected = false;

static const byte startupPacket[] = {0xF1, 0x01, 0x00, 0x00, 0x00, 0x01, 0xF2};

//User methods
void pinTwoInterruptHandler() {

}

byte *readEthernet() {
    Serial.println("Reading Ethernet");
    if (!ethernetClient->connected()) {
        Serial.println("Not connected.");
        Serial.println("Done.");
        return;
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

void writeEthernet(const byte *buffer) {
    Serial.println("Writing Ethernet");
    ethernetClient->write(buffer, sizeof(buffer));
    Serial.println("Done.");
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
            writeEthernet(startupPacket);
            ethernetClient->stop();
            Serial.println("Test connection closed.");
            ethernetClient->flush();
            Serial.println("Test connection flushed.");
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

void maintainEthernet() {
    Serial.println("Maintaining Ethernet connection");
    Ethernet.maintain();
    Serial.println("Done.");
}

//Main methods
void setup() {
    //Startup scripts
    initSerial();

    initEthernet();

    initLcd();

    initDht();

    //attachInterrupt(InterruptDetectPin, pinTwoInterruptHandler, CHANGE);

    Serial.println("Init done.");
}

void loop() {
    //byte buffer[1] = {0xFF};
    //writeEthernet(buffer);
    //readEthernet();

    delay(5000);
    maintainEthernet();
}
