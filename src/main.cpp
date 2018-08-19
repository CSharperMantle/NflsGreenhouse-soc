#define PT_USE_SEM
#define PT_USE_TIMER

#include <Arduino.h>
#include <utility/w5100.h>
#include <Ethernet.h>
#include <EthernetClient.h>
#include <DHT.h>
#include <LiquidCrystal_I2C.h>
#include <pt.h>

static const int InterruptDetectPin = 2;
static const int relayOnePin = 22;
static const int relayTwoPin = 23;
static const int relayThreePin = 24;
static const int dhtPin = 25;
static const int lightSensorPin = A0;
static const int groundHumSensorPin = A1;

static const char *serverAddress = "192.168.1.101";
static const int serverPort = 80;
static byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

static LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
static DHT *airSensor = new DHT();
static EthernetClient *ethernetClient = new EthernetClient();

//User methods
void pinTwoInterruptHandler() {

}

void initSerial() {
    Serial.begin(115200);
    Serial.flush();
    Serial.println("Initializing");
}

void initEthernet() {
    Serial.println("Initializing Ethernet");
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(mac) == 0)
            Serial.println("DHCP failed. Retry " + index);
        else {
            Serial.println("DHCP OK.");
            break;
        }
    }
    Serial.println("Done.");

    Serial.println("Running tests on EthernetClient");
    for (int index = 1; index <= 5; index++) {
        if (ethernetClient->connect(serverAddress, serverPort)) {
            Serial.println("Test connection established.");
            ethernetClient->stop();
            Serial.println("Test connection closed.");
        }
        else
        {
            Serial.println("Test connection broken. Retry " + index);
        }

    }
    ethernetClient->flush();
    Serial.println("Done.");
}

void initLcd() {
    Serial.println("Initializing LCD");
    screen->begin(16, 2);
    screen->clear();
    screen->setBacklight(false);
    Serial.println("Done.");
}

void initDht() {
    Serial.println("Initializing DHT");
    airSensor->setup(dhtPin, DHT::DHT_MODEL_t::DHT11);
    Serial.println(airSensor->getStatusString());
    Serial.println("Done.");
}

void readEthernet() {
    Serial.println("Reading Ethernet");
    //TODO: Complete method.
    Serial.println("Done.");
}

//Main methods
void setup() {
    //Startup scripts
    initSerial();

    initEthernet();

    initLcd();

    initDht();

    attachInterrupt(InterruptDetectPin, pinTwoInterruptHandler, CHANGE);
}

void loop() {
    // put your main code here, to run repeatedly:
}
