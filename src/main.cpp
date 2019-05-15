/**
 * Copyright (c) 2018 Ningbo Foreign Language School
 * This part of program should be delivered with the whole project.
 * Partly use is not allowed.
 * Licensed under GPL-v3 Agreement
 */
#define PT_USE_TIMER
#define PT_USE_SEM

#define USING_PACKET_ENUM
#define USING_PACKET_MARCO

#include <stdlib.h>
#include <string.h>
#include <Arduino.h>
#include <WString.h>
#include <Ethernet.h>
#include <EthernetClient.h>
#include <DHT.h>
#include <LiquidCrystal_I2C.h>
#include <pt.h>
#include <cJSON.h>
#include <time.h>
#include <packet_defs.hpp>
#include <logger.hpp>
#include <http_parser.h>
#include "marco.hpp"

const PROGMEM int offlineAirTempSwitchValveLow = 10;
const PROGMEM int offlineAirTempSwitchValveHigh = 30;
const PROGMEM int offlineAirHumSwitchValveLow = 35;
const PROGMEM int offlineAirHumSwitchValveHigh = 70;
const PROGMEM int offlineLightSwitchValveLow = 100;
const PROGMEM int offlineAirLightSwitchValveHigh = 500;
const PROGMEM int offlineAirLightSwitchValveLow = 100;
const PROGMEM int offlineGroundHumSwitchValveLow = 700;
const PROGMEM int offlineGroundHumSwitchValveHigh = 1000;

const PROGMEM int waterPumpPin = 22;
const PROGMEM int fanOnePin = 23;
const PROGMEM int fanTwoPin = 24;
const PROGMEM int airCoolerPin = 25;
const PROGMEM int sideWindowOpenPin = 26;
const PROGMEM int sideWindowClosePin = 27;
const PROGMEM int topWindowOneOpenPin = 28;
const PROGMEM int topWindowOneClosePin = 29;
const PROGMEM int topWindowTwoOpenPin = 30;
const PROGMEM int topWindowTwoClosePin = 31;
const PROGMEM int skySheetOuterOpenPin = 32;
const PROGMEM int skySheetOuterClosePin = 33;
const PROGMEM int skySheetInnerOpenPin = 34;
const PROGMEM int skySheetInnerClosePin = 35;

const PROGMEM int dhtPin = 53;
const PROGMEM int lightSensorPin = A1;
const PROGMEM int groundHumSensorPin = A0;

const PROGMEM long uploadInterval = 1000L * 5; //MS->S S->M M->H
const PROGMEM long maintainEthernetInterval = 1000L * 60 * 60 * 2;
const PROGMEM long checkSensorInterval = 1000L * 2;

const char webServerAddress[] = "10.24.141.75";
const PROGMEM int webServerPort = 80;
const PROGMEM IPAddress webServerIp = IPAddress(10, 24, 141, 75);

const byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

bool pinState[14];

struct tm *p;

LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
DHT *airSensor = new DHT();
EthernetClient *webUploader = new EthernetClient();
http_parser_settings *httpParserSettings = new http_parser_settings();
http_parser *httpParser = new http_parser();
struct ServerResponse {
    char *body = NULL;
};
ServerResponse *server_response = new ServerResponse();
const Logger *logger = new Logger(&Serial, LoggingLevel::INFO);

float currentAirTemp = 0;
float currentAirHum = 0;
int currentLightValue = 0;
int currentGroundHum = 0;

void clearResetScreen(LiquidCrystal_I2C *lcd) {
    lcd->home();
    lcd->clear();
}

void clearWriteScreen(LiquidCrystal_I2C *lcd, const char *text, const int delayMillisecond) {
    clearResetScreen(lcd);
    lcd->print(text);
    delay(delayMillisecond);
}

void parseJsonCJson(const char *str) {
    CONST_CAST(logger, Logger *)->Info("PARSING JSON");
    cJSON *root = cJSON_Parse(str);
    CJSON_CHECK_PTR(root);
    CONST_CAST(logger, Logger *)->Debug("    EXECUTING REQUESTED ACTIVITIES");
    cJSON *actions = cJSON_GetObjectItemCaseSensitive(root, "actions");
    CJSON_CHECK_PTR(actions);
    for (cJSON *each_action = actions->child; each_action; each_action = each_action->next ) {
        int action_type = cJSON_GetObjectItemCaseSensitive(each_action, "action_type")->valueint;
        switch (action_type)
        {
            case ActionType::RELAY_ACTION: 
            {
                    int target_id = cJSON_GetObjectItemCaseSensitive(each_action, "target_id")->valueint;
                    int param = atoi(cJSON_GetObjectItemCaseSensitive(each_action, "param")->valuestring);
                    pinMode(target_id, OUTPUT);
                    if (param == 0) {
                        CONST_CAST(logger, Logger *)->Debug(String("OFF action on ") + String(target_id));
                        digitalWrite(target_id, LOW);
                        pinState[target_id] = false;
                    } else {
                        CONST_CAST(logger, Logger *)->Debug(String("ON action on ") + String(target_id));
                        digitalWrite(target_id, HIGH);
                        pinState[target_id] = true;
                    }
            }
            break;
            case ActionType::DEVICE_ACTION:
            {
                int target_id = cJSON_GetObjectItemCaseSensitive(each_action, "target_id")->valueint;
                const char* param = cJSON_GetObjectItemCaseSensitive(each_action, "param")->valuestring;
                switch (target_id)
                {
                    case DeviceId::DEVICE_LCD:
                        CONST_CAST(logger, Logger *)->Debug(String("DISPLAY action on ") + String(target_id));
                        screen->clear();
                        screen->home();
                        screen->print(param);
                        break;
                    default:
                        CONST_CAST(logger, Logger *)->Debug(String("Unknown device: ") + String(target_id));
                        break;
                }
            }
            break;
            
            case ActionType::RETRANSMIT_ACTION:
            {
                // Retransmitting requested
                //TODO: Add retransmitter
                CONST_CAST(logger, Logger *)->Debug("RETRANS action");
            }
                break;
            case ActionType::LCD_BACKLIGHT_SET: 
            {
                    int target_id = cJSON_GetObjectItemCaseSensitive(each_action, "target_id")->valueint;
                    int param = atoi(cJSON_GetObjectItemCaseSensitive(each_action, "param")->valuestring);
                    CONST_CAST(logger, Logger *)->Debug(String("BKLT action on ") + String(target_id));
                    screen->setBacklight(param);
            }
            break;
            default: 
            {
                CONST_CAST(logger, Logger *)->Debug(String("Unknown JSON action received: ") + String(action_type));
            }
                break;
        }
    }
    CONST_CAST(logger, Logger *)->Debug("    ACTIVITIES EXECUTED");
    
    CJSON_DEL_PTR(root);
    CONST_CAST(logger, Logger *)->Info("DONE PARSING");
}

HTTP_PARSER_CALLBACK(onMessageBeginCallback(http_parser *parser)) {
    CONST_CAST(logger, Logger *)->Debug("START ACCEPTING");
    clearResetScreen(screen);
    return 0;
}

HTTP_PARSER_CALLBACK(onBodyReceivedCallback(http_parser *parser, const char *buf, size_t len)) {
    server_response->body = CALLOC_HEAP(strlen(buf) + 1, char);
    strncpy(server_response->body, buf, len);
    return 0;
}

HTTP_PARSER_CALLBACK(onMessageEndCallback(http_parser *parser)) {
    CONST_CAST(logger, Logger *)->Debug(server_response->body);
    parseJsonCJson(server_response->body);
    FREE_HEAP(server_response->body);
    CONST_CAST(logger, Logger *)->Debug("ALL DONE");
    return 0;
}

void printLicenseInfo() {
    CONST_CAST(logger, Logger *)->Info("This project is made by Mantle & OverJerry & iRed_K. Licensed under GPLv3.");
    CONST_CAST(logger, Logger *)->Info("Libs in use:");
    CONST_CAST(logger, Logger *)->Info("cJSON by Dave Gamble and other cJSON contributors");
    CONST_CAST(logger, Logger *)->Info("ProtoThreads by Adam Dunkels");
    CONST_CAST(logger, Logger *)->Info("HttpParser by Joyent, Inc. and other Node contributors");
    CONST_CAST(logger, Logger *)->Info("");
    screen->setBacklight(true);
    clearResetScreen(screen);
    screen->print("Provided under");
    screen->setCursor(0, 1);
    screen->print("GPLv3");
    delay(1000);
    clearResetScreen(screen);
    screen->print("Mantle &");
    screen->setCursor(0, 1);
    screen->print("OverJerry &");
    screen->setCursor(0, 1);
    screen->print("iRed_K");
    delay(1000);
    clearResetScreen(screen);
}

void initSerial() {
    Serial.begin(115200);
    Serial.flush();
    CONST_CAST(logger, Logger *)->Debug("Serial opened.");
}

void initEthernet() {
    CONST_CAST(logger, Logger *)->Info("Initializing Ethernet");
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(const_cast<byte *>(mac)) == 0) {
            CONST_CAST(logger, Logger *)->Error(String("DHCP failed. Retry ") + String(index));
            clearWriteScreen(screen, (String("ETH ERR: ") + String(index)).c_str(), 300);
        } else {
            CONST_CAST(logger, Logger *)->Debug("DHCP OK.");
            CONST_CAST(logger, Logger *)->Debug("    Ethernet information: IP, DNS, Gateway");
            CONST_CAST(logger, Logger *)->Debug(String(Ethernet.localIP()));
            CONST_CAST(logger, Logger *)->Debug(String(Ethernet.dnsServerIP()));
            CONST_CAST(logger, Logger *)->Debug(String(Ethernet.gatewayIP()));
            clearWriteScreen(screen, "ETH OK", 300);
            break;
        }
    }
    CONST_CAST(logger, Logger *)->Info("Done.");

    CONST_CAST(logger, Logger *)->Info("Setting up HTTP Parser");
    clearWriteScreen(screen, "PARSER SETUP", 300);
    http_parser_settings_init(httpParserSettings);
    http_parser_init(httpParser, http_parser_type::HTTP_RESPONSE);
    httpParserSettings->on_body = onBodyReceivedCallback;
    httpParserSettings->on_message_begin = onMessageBeginCallback;
    httpParserSettings->on_message_complete = onMessageEndCallback;
    CONST_CAST(logger, Logger *)->Info("Done.");
}

void initLcd() {
    CONST_CAST(logger, Logger *)->Info("Initializing LCD");
    screen->init();
    screen->clear();
    screen->setBacklight(true);
    CONST_CAST(logger, Logger *)->Info("Done.");
}

void initDht() {
    CONST_CAST(logger, Logger *)->Info("Initializing DHT");
    airSensor->setup(dhtPin, DHT::DHT_MODEL_t::DHT11);
    CONST_CAST(logger, Logger *)->Debug(airSensor->getStatusString());
    CONST_CAST(logger, Logger *)->Info("Done.");
}

void initPin() {
    for (int target_id = 22; target_id <= 35; target_id++) {
        CONST_CAST(logger, Logger *)->Debug(String("OFF action on ") + String(target_id));
        digitalWrite(target_id, LOW);
    }
    for (int i = 0; i<=11 ;i++) {
        pinState[i] = false;
    }
}

void setup() {
    //Startup scripts
    initSerial();
    initLcd();
    printLicenseInfo();
    initEthernet();
    initDht();
    initPin();

    CONST_CAST(logger, Logger *)->Info("Init done.");
}

void loop() {
    currentGroundHum = analogRead(groundHumSensorPin);
    currentLightValue = analogRead(lightSensorPin);
    currentAirTemp = airSensor->getTemperature();
    currentAirHum = airSensor->getHumidity();

    CONST_CAST(logger, Logger *)->Info("Uploading sensor data");
    if (webUploader->connect(webServerIp, webServerPort)) {
        CONST_CAST(logger, Logger *)->Debug("Connection established.");
        cJSON * root =  cJSON_CreateObject();

        cJSON_AddItemToObject(root, "version", cJSON_CreateString("1.2.0"));

        cJSON * timestamp = cJSON_AddObjectToObject(root, "timestamp");
        cJSON_AddItemToObject(timestamp, "hour", cJSON_CreateNumber(p->tm_hour));
        cJSON_AddItemToObject(timestamp, "minute", cJSON_CreateNumber(p->tm_min));
        cJSON_AddItemToObject(timestamp, "second", cJSON_CreateNumber(p->tm_sec));

        cJSON * data = cJSON_AddArrayToObject(root, "data");
        cJSON * data_air_temp = cJSON_CreateObject();
        cJSON * data_air_hum = cJSON_CreateObject();
        cJSON * data_light_value = cJSON_CreateObject();
        cJSON * data_ground_hum = cJSON_CreateObject();
        cJSON_AddItemToObject(data_air_temp, "data_type", cJSON_CreateNumber(0));
        cJSON_AddItemToObject(data_air_temp, "data_content", cJSON_CreateNumber(currentAirTemp));
        cJSON_AddItemToObject(data_air_hum, "data_type", cJSON_CreateNumber(1));
        cJSON_AddItemToObject(data_air_hum, "data_content", cJSON_CreateNumber(currentAirHum));
        cJSON_AddItemToObject(data_light_value, "data_type", cJSON_CreateNumber(2));
        cJSON_AddItemToObject(data_light_value, "data_content", cJSON_CreateNumber(currentLightValue));
        cJSON_AddItemToObject(data_ground_hum, "data_type", cJSON_CreateNumber(3));
        cJSON_AddItemToObject(data_ground_hum, "data_content", cJSON_CreateNumber(currentGroundHum));
        cJSON_AddItemToArray(data, data_air_temp);
        cJSON_AddItemToArray(data, data_air_hum);
        cJSON_AddItemToArray(data, data_light_value);
        cJSON_AddItemToArray(data, data_ground_hum);

        cJSON * state = cJSON_AddArrayToObject(root, "state");
        cJSON * state_water = cJSON_CreateObject();         
        cJSON * state_fan_one = cJSON_CreateObject();
        cJSON * state_fan_two = cJSON_CreateObject();
        cJSON * state_air = cJSON_CreateObject();
        cJSON * state_windows_side_open = cJSON_CreateObject();
        cJSON * state_windows_side_close = cJSON_CreateObject();
        cJSON * state_windows_one_open = cJSON_CreateObject();
        cJSON * state_windows_one_close = cJSON_CreateObject();
        cJSON * state_windows_two_open = cJSON_CreateObject();
        cJSON * state_windows_two_close = cJSON_CreateObject();
        cJSON * state_sheet_outer_open = cJSON_CreateObject();
        cJSON * state_sheet_outer_close = cJSON_CreateObject();
        cJSON * state_sheet_inner_open = cJSON_CreateObject();
        cJSON * state_sheet_inner_close = cJSON_CreateObject();
        cJSON_AddItemToObject(state_water, "pin_id", cJSON_CreateNumber(22));
        cJSON_AddItemToObject(state_water, "state", cJSON_CreateNumber(pinState[0]));
        cJSON_AddItemToObject(state_fan_one, "pin_id", cJSON_CreateNumber(23));
        cJSON_AddItemToObject(state_fan_one, "state", cJSON_CreateNumber(pinState[1]));
        cJSON_AddItemToObject(state_fan_two, "pin_id", cJSON_CreateNumber(24));
        cJSON_AddItemToObject(state_fan_two, "state", cJSON_CreateNumber(pinState[2]));
        cJSON_AddItemToObject(state_air, "pin_id", cJSON_CreateNumber(25));
        cJSON_AddItemToObject(state_air, "state", cJSON_CreateNumber(pinState[3]));
        cJSON_AddItemToObject(state_windows_side_open, "pin_id", cJSON_CreateNumber(26));
        cJSON_AddItemToObject(state_windows_side_open, "state", cJSON_CreateNumber(pinState[4]));
        cJSON_AddItemToObject(state_windows_side_close, "pin_id", cJSON_CreateNumber(27));
        cJSON_AddItemToObject(state_windows_side_close, "state", cJSON_CreateNumber(pinState[5]));
        cJSON_AddItemToObject(state_windows_one_open, "pin_id", cJSON_CreateNumber(28));
        cJSON_AddItemToObject(state_windows_one_open, "state", cJSON_CreateNumber(pinState[6]));
        cJSON_AddItemToObject(state_windows_one_close, "pin_id", cJSON_CreateNumber(29));
        cJSON_AddItemToObject(state_windows_one_close, "state", cJSON_CreateNumber(pinState[7]));
        cJSON_AddItemToObject(state_windows_two_open, "pin_id", cJSON_CreateNumber(30));
        cJSON_AddItemToObject(state_windows_two_open, "state", cJSON_CreateNumber(pinState[8]));
        cJSON_AddItemToObject(state_windows_two_close, "pin_id", cJSON_CreateNumber(31));
        cJSON_AddItemToObject(state_windows_two_close, "state", cJSON_CreateNumber(pinState[9]));
        cJSON_AddItemToObject(state_sheet_outer_open, "pin_id", cJSON_CreateNumber(32));
        cJSON_AddItemToObject(state_sheet_outer_open, "state", cJSON_CreateNumber(pinState[10]));
        cJSON_AddItemToObject(state_sheet_outer_close, "pin_id", cJSON_CreateNumber(33));
        cJSON_AddItemToObject(state_sheet_outer_close, "state", cJSON_CreateNumber(pinState[11]));
        cJSON_AddItemToObject(state_sheet_inner_open, "pin_id", cJSON_CreateNumber(34));
        cJSON_AddItemToObject(state_sheet_inner_open, "state", cJSON_CreateNumber(pinState[12]));
        cJSON_AddItemToObject(state_sheet_inner_close, "pin_id", cJSON_CreateNumber(35));
        cJSON_AddItemToObject(state_sheet_inner_close, "state", cJSON_CreateNumber(pinState[13]));
        cJSON_AddItemToArray(state, state_water);        
        cJSON_AddItemToArray(state, state_fan_one);
        cJSON_AddItemToArray(state, state_fan_two);
        cJSON_AddItemToArray(state, state_air);
        cJSON_AddItemToArray(state, state_windows_side_open);
        cJSON_AddItemToArray(state, state_windows_side_close);
        cJSON_AddItemToArray(state, state_windows_one_open);
        cJSON_AddItemToArray(state, state_windows_one_close);
        cJSON_AddItemToArray(state, state_windows_two_open);
        cJSON_AddItemToArray(state, state_windows_two_close);
        cJSON_AddItemToArray(state, state_sheet_outer_open);
        cJSON_AddItemToArray(state, state_sheet_outer_close);
        cJSON_AddItemToArray(state, state_sheet_inner_open);
        cJSON_AddItemToArray(state, state_sheet_inner_close);

        webUploader->print(String("POST /api/v1.2/upload.php")
                + String(" HTTP/1.1\r\n" \
                "Accept: application/json\r\n" \
                "Host: ") + String(webServerAddress) + String(":") + String(webServerPort) + String("\r\n") + String( \
                "User-Agent: arduino/mega2560\r\n" \
                "Connection: close\r\n" \
                "\r\n" \
                ) +
                String(cJSON_Print(root)) \
                );
        {
            String respond = String();
            do
            {
                String str = webUploader->readString();
                respond += str;
            } while (webUploader->available());
            http_parser_init(httpParser, HTTP_RESPONSE);
            http_parser_execute(httpParser, httpParserSettings, respond.c_str(), strlen(respond.c_str()));
        }
        webUploader->stop();
        CONST_CAST(logger, Logger *)->Info("Done. Connection closed.");
        delay(1000);
    } else {
        CONST_CAST(logger, Logger *)->Error("Connection broke.");
        webUploader->flush();
        webUploader->stop();
    }
    
    CONST_CAST(logger, Logger *)->Info("Maintaining Ethernet connection");
    Ethernet.maintain();
    CONST_CAST(logger, Logger *)->Info("Done.");
}