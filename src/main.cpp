/**
 * Copyright (c) 2018 Ningbo Foreign Language School
 * This part of program should be delivered with the whole project.
 * Partly use is not allowed.
 * Licensed under GPL-v3 Agreement
 */
#define PT_USE_TIMER
#define PT_USE_SEM

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

#pragma region constant
const __ATTR_PROGMEM__ int offlineAirTempSwitchValveLow = 10;
const __ATTR_PROGMEM__ int offlineAirTempSwitchValveHigh = 30;
const __ATTR_PROGMEM__ int offlineAirHumSwitchValveLow = 35;
const __ATTR_PROGMEM__ int offlineAirHumSwitchValveHigh = 70;
const __ATTR_PROGMEM__ int offlineLightSwitchValveLow = 100;
const __ATTR_PROGMEM__ int offlineAirLightSwitchValveHigh = 500;
const __ATTR_PROGMEM__ int offlineAirLightSwitchValveLow = 100;
const __ATTR_PROGMEM__ int offlineGroundHumSwitchValveLow = 700;
const __ATTR_PROGMEM__ int offlineGroundHumSwitchValveHigh = 1000;

const __ATTR_PROGMEM__ int waterPumpPin = 22;
const __ATTR_PROGMEM__ int fanOnePin = 23;
const __ATTR_PROGMEM__ int fanTwoPin = 24;
const __ATTR_PROGMEM__ int airCoolerPin = 25;
const __ATTR_PROGMEM__ int sideWindowOpenPin = 26;
const __ATTR_PROGMEM__ int sideWindowClosePin = 27;
const __ATTR_PROGMEM__ int topWindowOneOpenPin = 28;
const __ATTR_PROGMEM__ int topWindowOneClosePin = 29;
const __ATTR_PROGMEM__ int topWindowTwoOpenPin = 30;
const __ATTR_PROGMEM__ int topWindowTwoClosePin = 31;
const __ATTR_PROGMEM__ int skySheetOuterOpenPin = 32;
const __ATTR_PROGMEM__ int skySheetOuterClosePin = 33;
const __ATTR_PROGMEM__ int skySheetInnerOpenPin = 34;
const __ATTR_PROGMEM__ int skySheetInnerClosePin = 35;

const __ATTR_PROGMEM__ int dhtPin = 53;
const __ATTR_PROGMEM__ int lightSensorPin = A1;
const __ATTR_PROGMEM__ int groundHumSensorPin = A0;

const __ATTR_PROGMEM__ long uploadInterval = 1000L * 5; //MS->S S->M M->H
const __ATTR_PROGMEM__ long maintainEthernetInterval = 1000L * 60 * 60 * 2;
const __ATTR_PROGMEM__ long checkSensorInterval = 1000L * 2;

const char *webServerAddress = "10.24.141.75";
const __ATTR_PROGMEM__ int webServerPort = 80;
const IPAddress *webServerIp = new IPAddress(10, 24, 141, 75);

byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

#pragma endregion

#pragma region object
LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
DHT *airSensor = new DHT();
EthernetClient *webUploader = new EthernetClient();
http_parser_settings *httpParserSettings = new http_parser_settings();
http_parser *httpParser = new http_parser();
struct ServerResponse {
    char *body = NULL;
};
ServerResponse *server_response = new ServerResponse();
#pragma endregion

#pragma region var
float currentAirTemp = 0;
float currentAirHum = 0;
int currentLightValue = 0;
int currentGroundHum = 0;
#pragma endregion

#pragma region thread_controller
pt uploadSensorData_ctrl = pt();
pt maintainEthernet_ctrl = pt();
#pragma endregion

#pragma region helper
void clearResetScreen(LiquidCrystal_I2C *lcd) {
    lcd->home();
    lcd->clear();
}

void clearWriteScreen(LiquidCrystal_I2C *lcd, const char *text, const int delayMillisecond) {
    clearResetScreen(lcd);
    lcd->print(text);
    delay(delayMillisecond);
}

void parseXmlTiny(const char *str) {
    Serial.println("PARSING XML");
    TiXmlDocument *doc = new TiXmlDocument();
    doc->Parse(str);
    Serial.println("EXECUTING XML");
    TiXmlHandle *handle = new TiXmlHandle(doc);
    TiXmlElement *action = handle->FirstChild("root").FirstChild("actions").FirstChild("action").ToElement();
    TiXmlElement *timestamp = handle->FirstChild("root").FirstChild("timestamp").ToElement();
    int hour = atoi(timestamp->FirstChild("hour")->ToText()->Value());
    int minute = atoi(timestamp->FirstChild("minute")->ToText()->Value());
    int second = atoi(timestamp->FirstChild("second")->ToText()->Value());
    for ( ; action; action = action->NextSiblingElement()) {
        TiXmlNode *type = action->FirstChildElement("type")->FirstChild();
        TiXmlNode *targetId = action->FirstChildElement("target_id")->FirstChild();
        TiXmlNode *param = action->FirstChildElement("param")->FirstChild();
        int typeValue = atoi(type->ToText()->Value());
        
        if (typeValue == ActionType::RELAY_ACTION) {
            // Relay action requested
            const char *paramValue = param->ToText()->Value();
            int targetIdValue = atoi(targetId->ToText()->Value());
            pinMode(targetIdValue, OUTPUT);
            if (!strcmp(paramValue, "0")) {
                Serial.println(String("OFF action on ") + String(targetIdValue));
                digitalWrite(targetIdValue, LOW);
            } else {
                Serial.println(String("ON action on ") + String(targetIdValue));
                digitalWrite(targetIdValue, HIGH);
            }
        } else if (typeValue == ActionType::DEVICE_ACTION) {
            // Action with other devices requested
            int targetIdValue = atoi(targetId->ToText()->Value());
            const char *paramValue = param->ToText()->Value();
            if (targetIdValue == DeviceId::DEVICE_LCD)
            {
                Serial.println(String("DISPLAY action on ") + String(targetIdValue));
                screen->clear();
                screen->home();
                screen->print(paramValue);
            } else {
                Serial.println(String("Unknown device: ") + String(targetIdValue));
            }
            //TODO: Add more devices
        } else if (typeValue == ActionType::RETRANSMIT_ACTION) {
            // Retransmitting requested
            //TODO: Add retransmitter
            Serial.println("RETRANS action");
        } else if (typeValue == ActionType::LCD_BACKLIGHT_SET) {
            // LCD backlight setting requested
            const char *paramValue = param->ToText()->Value();
            int targetIdValue = atoi(targetId->ToText()->Value());
            Serial.println(String("BKLT action on ") + String(targetIdValue));
            screen->setBacklight(atoi(paramValue));
        }
        else {
            Serial.println(String("Unknown XML action received: ") + String(typeValue));
        }
    }

    //FIXME: Maybe a bug
    delete handle;
    delete doc;
    Serial.println("DONE PARSING XML");
}
#pragma endregion

#pragma region callback
HTTP_PARSER_CALLBACK(onMessageBeginCallback(http_parser *parser)) {
    Serial.println("START PARSING");
    return 0;
}

HTTP_PARSER_CALLBACK(onBodyReceivedCallback(http_parser *parser, const char *buf, size_t len)) {
    Serial.println("IN PARSING");
    if (server_response->body != NULL) {
        Serial.println("NOT EMPTY");
        server_response->body = REALLOC_HEAP(server_response->body, strlen(server_response->body) + strlen(buf), char);
        strncat(server_response->body, buf, len);
    } else {
        Serial.println("EMPTY");
        server_response->body = CALLOC_HEAP(strlen(buf) + 1, char);
        strncpy(server_response->body, buf, len);
    }
    return 0;
}

HTTP_PARSER_CALLBACK(onMessageEndCallback(http_parser *parser)) {
    Serial.println("STOP PARSING");
    Serial.print(server_response->body);
    parseXmlTiny(server_response->body);
    FREE_HEAP(server_response->body);
    Serial.println("ALL DONE");
    return 0;
}
#pragma endregion

#pragma region init_script

void printLicenseInfo() {
    Serial.println("This project is made by Mantle & iRed_K. Licensed under GPLv3.");
    Serial.println("Libs in use: TinyXML by Lee Thomason");
    Serial.println("ProtoThreads by Adam Dunkels");
    Serial.println("HttpParser by Joyent, Inc. and other Node contributors");
    Serial.println("");
    screen->setBacklight(true);
    clearResetScreen(screen);
    screen->print("Provided under");
    screen->setCursor(0, 1);
    screen->print("GPLv3");
    delay(1000);
    clearResetScreen(screen);
    screen->print("Mantle &");
    screen->setCursor(0, 1);
    screen->print("iRed_K");
    delay(1000);
    clearResetScreen(screen);
}

void initSerial() {
    Serial.begin(115200);
    Serial.flush();
    Serial.println("Serial opened.");
}

void initEthernet() {
    Serial.println("Initializing Ethernet");
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(mac) == 0) {
            Serial.println(String("DHCP failed. Retry ") + String(index));
            clearWriteScreen(screen, (String("ETH ERR: ") + String(index)).c_str(), 300);
        } else {
            Serial.println("DHCP OK.");
            Serial.println("    Ethernet information: IP, DNS, Gateway");
            Serial.println(Ethernet.localIP());
            Serial.println(Ethernet.dnsServerIP());
            Serial.println(Ethernet.gatewayIP());
            clearWriteScreen(screen, "ETH OK", 300);
            break;
        }
    }
    Serial.println("Done.");

    Serial.println("Setting up HTTP Parser");
    clearWriteScreen(screen, "PARSER SETUP", 300);
    http_parser_settings_init(httpParserSettings);
    http_parser_init(httpParser, http_parser_type::HTTP_RESPONSE);
    httpParserSettings->on_body = onBodyReceivedCallback;
    httpParserSettings->on_message_begin = onMessageBeginCallback;
    httpParserSettings->on_message_complete = onMessageEndCallback;
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

PT_THREAD(maintainEthernet(pt *pt)) {
    PT_BEGIN(pt);
    Serial.println("Maintaining Ethernet connection");
    Ethernet.maintain();
    PT_YIELD(pt);
    Serial.println("Done.");
    PT_TIMER_DELAY(pt, maintainEthernetInterval);
    PT_END(pt);
}

PT_THREAD(uploadSensorData(pt *pt)) {
    PT_BEGIN(pt);
    currentGroundHum = analogRead(groundHumSensorPin);
    currentLightValue = analogRead(lightSensorPin);
    currentAirTemp = airSensor->getTemperature();
    currentAirHum = airSensor->getHumidity();

    Serial.println("Uploading sensor data");
    if (webUploader->connect(*webServerIp, webServerPort)) {
        Serial.println("Connection established.");
        clearWriteScreen(screen, "DATA UPLOAD", 300);

        webUploader->print(String("GET /api/v1.0/upload.php?air_temp=") + String(currentAirTemp) \
                + String("&air_hum=") + String(currentAirHum) \
                + String("&air_light=") + String(currentLightValue) \
                + String("&ground_hum=") + String(currentGroundHum) \
                + String(" HTTP/1.1\r\n" \
                "Accept: application/xml\r\n" \
                "Host: ") + String(webServerAddress) + String(":") + String(webServerPort) + String("\r\n") + String( \
                "User-Agent: arduino/mega2560\r\n" \
                "Connection: close\r\n" \
                "\r\n" \
                ""));
        {
            String respond = String();
            clearResetScreen(screen);
            do
            {
                String str = webUploader->readString();
                respond += str;
                clearWriteScreen(screen, str.c_str(), 1000);
            } while (webUploader->available());
            http_parser_init(httpParser, HTTP_RESPONSE);
            http_parser_execute(httpParser, httpParserSettings, respond.c_str(), strlen(respond.c_str()));
        }
        webUploader->stop();
        Serial.println("Done. Connection closed.");
        delay(1000);
        clearWriteScreen(screen, "DATA UPLOADED", 300);
        break;
    } else {
        Serial.println("Connection broke.");
        webUploader->flush();
        webUploader->stop();
    }
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

    Serial.println("Init done.");
}

void loop() {
    uploadSensorData(&uploadSensorData_ctrl);
    maintainEthernet(&maintainEthernet_ctrl);
}
#pragma endregion