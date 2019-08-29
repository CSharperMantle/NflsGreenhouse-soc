/**
 * Copyright (c) 2019 Ningbo Foreign Language School
 * This part of program should be delivered with the whole project.
 * Partly use is not allowed.
 * Licensed under GPL-v3 Agreement
 */
#define PT_USE_TIMER
#define PT_USE_SEM

#define USING_PACKET_ENUM
#define USING_PACKET_MARCO


#include <Arduino.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <WString.h>
#include <Ethernet.h>
#include <EthernetClient.h>
#include <DHT.h>
#include <LiquidCrystal_I2C.h>
#include <cJSON.h>
#include <packet_defs.hpp>
#include <logger.hpp>
#include <http_parser.h>
#include "marco.hpp"
#include "helper.hpp"

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
const PROGMEM char postPacketTemplate[] = 
    "POST /api/v1.2/upload.php HTTP/1.1 \r\n"
    "Accept: application/json\r\n"
    "Host: %s\r\n"
    "User-Agent: arduino/mega2560(w5100, controller)\r\n"
    "Connection: close\r\n"
    "\r\n"
    "%s";

const byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

bool pinState[14];

time_t now_time;

LiquidCrystal_I2C screen(0x27, 16, 2);
DHT aerial_sensor;
EthernetClient web_uploader;
http_parser_settings req_res_parser_settings;
http_parser req_res_parser;
struct ServerResponse {
    char *body = NULL;
};
ServerResponse server_response;
Logger logger(&Serial, LoggingLevel::INFO);

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
    logger.Info("PARSING JSON");
    cJSON *root = cJSON_Parse(str);
    CJSON_CHECK_PTR(root);
    logger.Debug("    EXECUTING REQUESTED ACTIVITIES");
    cJSON *actions = cJSON_GetObjectItemCaseSensitive(root, "actions");
    CJSON_CHECK_PTR(actions);
    for (cJSON *each_action = actions->child; each_action; each_action = each_action->next) {
        int action_type = cJSON_GetObjectItemCaseSensitive(each_action, "action_type")->valueint;
        switch (action_type)
        {
            case ActionType::RELAY_ACTION: 
            {
                    int target_id = cJSON_GetObjectItemCaseSensitive(each_action, "target_id")->valueint;
                    int param = atoi(cJSON_GetObjectItemCaseSensitive(each_action, "param")->valuestring);
                    pinMode(target_id, OUTPUT);
                    if (param == 0) {
                        logger.Debug(String("OFF action on ") + String(target_id));
                        digitalWrite(target_id, LOW);
                        pinState[target_id] = false;
                    } else {
                        logger.Debug(String("ON action on ") + String(target_id));
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
                        logger.Debug(String("DISPLAY action on ") + String(target_id));
                        screen.clear();
                        screen.home();
                        screen.print(param);
                        break;
                    default:
                        logger.Debug(String("Unknown device: ") + String(target_id));
                        break;
                }
            }
            break;
            
            case ActionType::RETRANSMIT_ACTION:
            {
                // Retransmitting requested
                //TODO: Add retransmitter
                logger.Debug("RETRANS action");
            }
                break;
            case ActionType::LCD_BACKLIGHT_SET: 
            {
                    int target_id = cJSON_GetObjectItemCaseSensitive(each_action, "target_id")->valueint;
                    int param = atoi(cJSON_GetObjectItemCaseSensitive(each_action, "param")->valuestring);
                    logger.Debug(String("BKLT action on ") + String(target_id));
                    screen.setBacklight(param);
            }
            break;
            default: 
            {
                logger.Debug(String("Unknown JSON action received: ") + String(action_type));
            }
                break;
        }
    }
    logger.Debug("    ACTIVITIES EXECUTED");
    
    CJSON_DEL_PTR(root);
    logger.Info("DONE PARSING");
}

HTTP_PARSER_CALLBACK(onMessageBeginCallback(http_parser *parser)) {
    logger.Debug("START ACCEPTING");
    clearResetScreen(&screen);
    return 0;
}

HTTP_PARSER_CALLBACK(onBodyReceivedCallback(http_parser *parser, const char *buf, size_t len)) {
    server_response.body = CALLOC_HEAP(strlen(buf) + 1, char);
    strncpy(server_response.body, buf, len);
    return 0;
}

HTTP_PARSER_CALLBACK(onMessageEndCallback(http_parser *parser)) {
    logger.Debug(server_response.body);
    parseJsonCJson(server_response.body);
    FREE_HEAP(server_response.body);
    logger.Debug("ALL DONE");
    return 0;
}

void printLicenseInfo() {
    logger.Info("This project is made by Mantle & OverJerry & iRed_K. Licensed under GPLv3.");
    logger.Info("Libs in use:");
    logger.Info("cJSON by Dave Gamble and other cJSON contributors");
    logger.Info("ProtoThreads by Adam Dunkels");
    logger.Info("HttpParser by Joyent, Inc. and other Node contributors");
    logger.Info("");
    screen.setBacklight(true);
    clearResetScreen(&screen);
    screen.print("Provided under");
    screen.setCursor(0, 1);
    screen.print("GPLv3");
    delay(1000);
    clearResetScreen(&screen);
    screen.print("Mantle &");
    screen.setCursor(0, 1);
    screen.print("OverJerry &");
    screen.setCursor(0, 1);
    screen.print("iRed_K");
    delay(1000);
    clearResetScreen(&screen);
}

void initSerial() {
    Serial.begin(115200);
    Serial.flush();
    logger.Debug("Serial opened.");
}

void initEthernet() {
    logger.Info("Initializing Ethernet");
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(const_cast<byte *>(mac)) == 0) {
            logger.Error(String("DHCP failed. Retry ") + String(index));
            clearWriteScreen(&screen, (String("ETH ERR: ") + String(index)).c_str(), 300);
        } else {
            logger.Debug("DHCP OK.");
            logger.Debug("    Ethernet information: IP, DNS, Gateway");
            logger.Debug(String(Ethernet.localIP()));
            logger.Debug(String(Ethernet.dnsServerIP()));
            logger.Debug(String(Ethernet.gatewayIP()));
            clearWriteScreen(&screen, "ETH OK", 300);
            break;
        }
    }
    logger.Info("Done.");

    logger.Info("Setting up HTTP Parser");
    clearWriteScreen(&screen, "PARSER SETUP", 300);
    http_parser_settings_init(&req_res_parser_settings);
    http_parser_init(&req_res_parser, http_parser_type::HTTP_RESPONSE);
    req_res_parser_settings.on_body = onBodyReceivedCallback;
    req_res_parser_settings.on_message_begin = onMessageBeginCallback;
    req_res_parser_settings.on_message_complete = onMessageEndCallback;
    logger.Info("Done.");
}

void initLcd() {
    logger.Info("Initializing LCD");
    screen.init();
    screen.clear();
    screen.setBacklight(true);
    logger.Info("Done.");
}

void initDht() {
    logger.Info("Initializing DHT");
    aerial_sensor.setup(dhtPin, DHT::DHT_MODEL_t::DHT11);
    logger.Debug(aerial_sensor.getStatusString());
    logger.Info("Done.");
}

void initPin() {
    for (int target_id = 22; target_id <= 35; target_id++) {
        logger.Debug(String("OFF action on ") + String(target_id));
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

    logger.Info("Init done.");
}

void loop() {
    currentGroundHum = analogRead(groundHumSensorPin);
    currentLightValue = analogRead(lightSensorPin);
    currentAirTemp = aerial_sensor.getTemperature();
    currentAirHum = aerial_sensor.getHumidity();

    logger.Info("Uploading sensor data");
    if (web_uploader.connect(webServerIp, webServerPort)) {
        logger.Debug("Connection established.");
        cJSON * root =  cJSON_CreateObject();

        cJSON_AddItemToObject(root, "version", cJSON_CreateString("1.2.0"));

        time(&now_time);
        tm calen;
        localtime_r(&now_time, &calen);

        cJSON *timestamp = cJSON_AddObjectToObject(root, "timestamp");
        cJSON_AddItemToObject(timestamp, "hour", cJSON_CreateNumber(calen.tm_hour));
        cJSON_AddItemToObject(timestamp, "minute", cJSON_CreateNumber(calen.tm_min));
        cJSON_AddItemToObject(timestamp, "second", cJSON_CreateNumber(calen.tm_sec));

        cJSON *data = cJSON_AddArrayToObject(root, "data");
        cJSON *data_air_temp = cJSON_CreateObject();
        cJSON *data_air_hum = cJSON_CreateObject();
        cJSON *data_light_value = cJSON_CreateObject();
        cJSON *data_ground_hum = cJSON_CreateObject();
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

        cJSON *state_arr = cJSON_AddArrayToObject(root, "state_arr");
        hlp_cJSON_appendPinStateToArray(state_arr, waterPumpPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, fanOnePin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, fanTwoPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, airCoolerPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, sideWindowOpenPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, sideWindowClosePin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, topWindowOneOpenPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, topWindowOneClosePin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, topWindowTwoOpenPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, topWindowTwoClosePin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, skySheetOuterOpenPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, skySheetOuterClosePin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, skySheetInnerOpenPin, pinState);
        hlp_cJSON_appendPinStateToArray(state_arr, skySheetInnerClosePin, pinState);

        size_t packet_len = snprintf(NULL, 0, postPacketTemplate,
                webServerAddress, cJSON_Print(root)) + 1; // +1 for the terminating NULL
        char *buf = MALLOC_HEAP(packet_len, char);
        snprintf(buf, packet_len, postPacketTemplate,
                webServerAddress, cJSON_Print(root));
        web_uploader.print(buf);
        FREE_HEAP(buf);

        cJSON_Delete(root);
        root = NULL;

        String respond = web_uploader.readString();
        http_parser_init(&req_res_parser, HTTP_RESPONSE);
        http_parser_execute(&req_res_parser, &req_res_parser_settings, respond.c_str(), strlen(respond.c_str()));
        
        logger.Info("Done. Connection closed.");
        delay(1000);
    } else {
        logger.Error("Connection broke.");
    }
    web_uploader.flush();
    web_uploader.stop();
    
    logger.Info("Maintaining Ethernet connection");
    Ethernet.maintain();
    logger.Info("Done.");
}