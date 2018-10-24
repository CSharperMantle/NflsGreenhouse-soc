/**
 * Copyright (c) 2018 Ningbo Foreign Language School
 * This part of program should be delivered with the whole project.
 * Partly use is not allowed.
 * Licensed under GPL-v3 Agreement
 */
#define PT_USE_TIMER

#include <stdlib.h>
#include <string.h>
#include <Arduino.h>
#include <WString.h>
#include <utility/w5100.h>
#include <Ethernet.h>
#include <EthernetClient.h>
#include <DHT.h>
#include <LiquidCrystal_I2C.h>
#include <pt.h>
#include <tinyxml.h>
#include <packet_defs.h>
#include <http_parser.h>

#define alloc_cpy(type, dest, src, len) \
    dest = (type *)malloc(len + 1); \
    memcpy(dest, src, len); \
    dest[len] = '\0';

#pragma region constant
const int InterruptDetectPin = 2;

const int skySheetOnePin = 22;
const int skySheetTwoPin = 23;
const int fanOnePin = 24;
const int fanTwoPin = 25;
const int waterPumpOnePin = 26;
const int waterPumpTwoPin = 27;

const int dhtPin = 28;
const int lightSensorPin = A1;
const int groundHumSensorPin = A0;

const long uploadInterval = 1000L * 20; //MS->S S->M M->H
const long maintainEthernetInterval = 1000L * 60 * 60 * 2;
const long checkSensorInterval = 1000L * 15;

const char *webServerAddress = "192.168.1.114";
const int webServerPort = 80;

byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};
#pragma endregion

#pragma region object
LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
DHT *airSensor = new DHT();
EthernetClient *webUploader = new EthernetClient();
http_parser_settings *httpParserSettings = new http_parser_settings();
http_parser *httpParser = (http_parser *)malloc(sizeof(http_parser));
struct http_response {
    char *body;
};
#pragma endregion

#pragma region var
float currentAirTemp = 0;
float currentAirHum = 0;
int currentLightValue = 0;
int currentGroundHum = 0;
#pragma endregion

#pragma region thread_controller
struct pt uploadSensorData_ctrl;
struct pt maintainEthernet_ctrl;
struct pt readSensorData_ctrl;
#pragma endregion

#pragma region callback 
int body_cb(http_parser *parser, const char *buf, size_t len) {
    http_response *respond = (http_response *) parser->data;
    alloc_cpy(char, respond->body, buf, len);
    return 0;
}
#pragma endregion

#pragma region helper
void clearAndResetScreen(LiquidCrystal_I2C *lcd) {
    lcd->home();
    lcd->clear();
} 

void parseXmlStringAndExecute(const char * str) {
    TiXmlDocument *doc = new TiXmlDocument();
    doc->Parse(str);

    TiXmlHandle *handle = new TiXmlHandle(doc);
    TiXmlElement *action = handle->FirstChild("root").FirstChild("actions").FirstChild("action").ToElement();
    for (action; action; action = action->NextSiblingElement()) {
        TiXmlElement *type = action->FirstChildElement("type");
        TiXmlElement *targetId = action->FirstChildElement("target_id");
        TiXmlElement *param = action->FirstChildElement("param");
        int typeValue = atoi(type->ToText()->Value());
        int targetIdValue = atoi(targetId->ToText()->Value());
        const char *paramValue = param->ToText()->Value();
        
        if (typeValue == ActionType::RELAY_ACTION) {
            // Relay action requested
            if (!strcmp(paramValue, "0")) {
                digitalWrite(targetIdValue, LOW);
            }
            else {
                digitalWrite(targetIdValue, HIGH);
            }
        }
        else if (typeValue == ActionType::DEVICE_ACTION) {
            // Action with other devices requested
            if (targetIdValue == DeviceId::DEVICE_LCD)
            {
                screen->clear();
                screen->home();
                screen->print(paramValue);
            }
            else {
                Serial.println(String("Unknown device: ") + String(targetIdValue));
            }
            //TODO: Add more devices
        }
        else if (typeValue == ActionType::RETRANSMIT_ACTION) {
            // Retransmitting requested
            //TODO: Add retransmitter
        }
        else if (typeValue == ActionType::LCD_BACKLIGHT_SET) {
            // LCD backlight setting requested
            screen->setBacklight(atoi(paramValue));
        }
        else {
            Serial.println(String("Unknown XML action received: ") + String(typeValue));
        }
    }
    
    //FIXME: Maybe a bug
    delete handle;
    delete doc;
}

#pragma endregion

#pragma region init_script
void printLicenseInfo() {
    Serial.println("This project is made by Mantle & iRed_K. Licensed under GPLv3.");
    Serial.println("Libs in use: TinyXML by Lee Thomason");
    Serial.println("ProtoThreads by Adam Dunkels");
    Serial.println("");
    screen->setBacklight(true);
    clearAndResetScreen(screen);
    screen->print("Provided under");
    screen->setCursor(0, 1);
    screen->print("GPLv3");
    delay(1000);
    clearAndResetScreen(screen);
    screen->print("Mantle &");
    screen->setCursor(0, 1);
    screen->print("iRed_K");
    delay(1000);
    clearAndResetScreen(screen);
}

void initSerial() {
    Serial.begin(9600);
    Serial.flush();
    Serial.println("Serial opened.");
}

void initEthernet() {
    Serial.println("Initializing Ethernet");
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(mac) == 0) {
            Serial.println(String("DHCP failed. Retry ") + String(index));
            clearAndResetScreen(screen);
            screen->print(String("ETH ERR: ") + String(index));
        } else {
            Serial.println("DHCP OK.");
            Serial.println("    Ethernet information: IP, DNS, Gateway");
            Serial.println(Ethernet.localIP());
            Serial.println(Ethernet.dnsServerIP());
            Serial.println(Ethernet.gatewayIP());
            clearAndResetScreen(screen);
            screen->print(String("ETH OK"));
            break;
        }
    }
    Serial.println("Done.");

    Serial.println("Running tests on EthernetClient");
    for (int index = 1; index <= 5; index++) {
        if (webUploader->connect(webServerAddress, webServerPort)) {
            Serial.println("Test connection established.");
            webUploader->flush();
            Serial.println("Test connection flushed.");
            webUploader->stop();
            Serial.println("Test connection closed.");
            clearAndResetScreen(screen);
            screen->print(String("CONN OK"));
            break;
        } else {
            Serial.println(String("Test connection broken. Retry ") + String(index));
            clearAndResetScreen(screen);
            screen->print(String("CONN ERR:") + String(index));
        }
    }
    Serial.println("Done.");
}

void initLcd() {
    Serial.println("Initializing LCD");
    screen->init();
    screen->clear();
    screen->setBacklight(true);
    Serial.println("Done.");
}

void initDht() {
    Serial.println("Initializing DHT");
    airSensor->setup(dhtPin, DHT::DHT_MODEL_t::DHT11);
    Serial.println(airSensor->getStatusString());
    Serial.println("Done.");
}
#pragma endregion

#pragma region threaded_worker
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
    clearAndResetScreen(screen);
    screen->print(String("SENSOR READ"));
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
    clearAndResetScreen(screen);
    screen->print("DATA PREPARE");
    httpParser->data = new http_response();
    for (static int index = 1; index <= 5; index++) {
        if (webUploader->connect(webServerAddress, webServerPort)) {
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
            "Accept: application/xml\r\n" \
            "Host: ") + String(webServerAddress) + String(":") + String(webServerPort) + String("\r\n") + String( \
            "User-Agent: arduino/mega2560\r\n" \
            "Connection: close\r\n" \
            "\r\n" \
            ""
        )
    );
    clearAndResetScreen(screen);
    screen->print("DATA UPLOAD");
    webUploader->print(String("GET /upload.php?air_temp=") + String(currentAirTemp) \
            + String("&air_hum=") + String(currentAirHum) \
            + String("&air_light=") + String(currentLightValue) \
            + String("&ground_hum=") + String(currentGroundHum) \
            + String(" HTTP/1.1\r\n" \
            "Accept: application/xml\r\n" \
            "Host: ") + String(webServerAddress) + String(":") + String(webServerPort) + String("\r\n") + String( \
            "User-Agent: arduino/mega2560\r\n" \
            "Connection: close\r\n" \
            "\r\n" \
            ""
        ));
    clearAndResetScreen(screen);

    {
        String respond = String();
        do
        {
            String str = webUploader->readString();
            Serial.println(str);
            respond += str;
        } while (webUploader->available());
        http_parser_execute(httpParser, httpParserSettings, respond.c_str(), 0);
    }

    webUploader->stop();
    Serial.println("Done. Connection closed.");
    delay(1000);
    clearAndResetScreen(screen);
    screen->print(String("DATA UPLOADED"));

    parseXmlStringAndExecute(((http_response *)httpParser->data)->body);
    clearAndResetScreen(screen);
    screen->print(String("RESPONSE PARSED"));
    
    delete httpParser->data;
    httpParser->data = nullptr;
    PT_TIMER_DELAY(pt, uploadInterval);
    PT_END(pt);
}
#pragma endregion

#pragma region main
void setup() {
    //Startup scripts
    initSerial();
    initLcd();
    printLicenseInfo();
    initEthernet();
    initDht();
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
#pragma endregion