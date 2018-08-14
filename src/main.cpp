#define PT_USE_SEM
#define PT_USE_TIMER

#include <Arduino.h>
#include <Ethernet.h>
#include <LiquidCrystal_I2C.h>
#include <DHT.h>
#include <pt.h>

static const int InterruptDetectPin = 2;
static const int relayOnePin = 22;
static const int relayTwoPin = 23;
static const int relayThreePin = 24;
static const int dhtPin = 25;
static const int lightSensorPin = A0;
static const int groundHumSensorPin = A1;
static const char *serverAddress = "192.168.1.101";
static byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

static LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
static DHT *airSensor = new DHT();

void setup() {
    // put your setup code here, to run once:
    Serial.begin(115200);
    Serial.flush();
    Serial.println("Initializing");
    
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(mac) == 0) 
            Serial.println("DHCP failed. Retry " + index);
        else {
            Serial.println("DHCP OK.");
            break;
        }
    }
    
    attachInterrupt(InterruptDetectPin, pinTwoInterruptHandler, CHANGE);
}

void loop() {
    // put your main code here, to run repeatedly:
}

void pinTwoInterruptHandler() {

}