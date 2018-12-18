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
#include <cJSON_Marcos.h>
#include <packet_defs.hpp>
#include <logger.hpp>
#include <http_parser.h>

#pragma region constant
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
Logger *logger = new Logger(&Serial);
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

void parseJsonCJson(const char *str) {
    logger->Info("PARSING JSON");
    cJSON *root = cJSON_Parse(str);
    CJSON_CHECK_PTR(root);
    logger->Debug("    EXECUTING REQUESTED ACTIVITIES");
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
                        logger->Debug(String("OFF action on ") + String(target_id));
                        digitalWrite(target_id, LOW);
                    } else {
                        logger->Debug(String("ON action on ") + String(target_id));
                        digitalWrite(target_id, HIGH);
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
                        logger->Debug(String("DISPLAY action on ") + String(target_id));
                        screen->clear();
                        screen->home();
                        screen->print(param);
                        break;
                    default:
                        logger->Debug(String("Unknown device: ") + String(target_id));
                        break;
                }
            }
            break;
            
            case ActionType::RETRANSMIT_ACTION:
            {
                // Retransmitting requested
                //TODO: Add retransmitter
                logger->Debug("RETRANS action");
            }
                break;
            case ActionType::LCD_BACKLIGHT_SET: 
            {
                    int target_id = cJSON_GetObjectItemCaseSensitive(each_action, "target_id")->valueint;
                    int param = atoi(cJSON_GetObjectItemCaseSensitive(each_action, "param")->valuestring);
                    logger->Debug(String("BKLT action on ") + String(target_id));
                    screen->setBacklight(param);
            }
            break;
            default: 
            {
                logger->Debug(String("Unknown JSON action received: ") + String(action_type));
            }
                break;
        }
    }
    logger->Debug("    ACTIVITIES EXECUTED");
    
    CJSON_DEL_PTR(root);
    logger->Info("DONE PARSING");
}
#pragma endregion

#pragma region callback
HTTP_PARSER_CALLBACK(onMessageBeginCallback(http_parser *parser)) {
    logger->Debug("START ACCEPTING");
    clearResetScreen(screen);
    return 0;
}

HTTP_PARSER_CALLBACK(onBodyReceivedCallback(http_parser *parser, const char *buf, size_t len)) {
    server_response->body = CALLOC_HEAP(strlen(buf) + 1, char);
    strncpy(server_response->body, buf, len);
    return 0;
}

HTTP_PARSER_CALLBACK(onMessageEndCallback(http_parser *parser)) {
    logger->Debug(server_response->body);
    parseJsonCJson(server_response->body);
    FREE_HEAP(server_response->body);
    logger->Debug("ALL DONE");
    return 0;
}
#pragma endregion

#pragma region init_script

void printLicenseInfo() {
    logger->Info("This project is made by Mantle & iRed_K. Licensed under GPLv3.");
    logger->Info("Libs in use:");
    logger->Info("cJSON by Dave Gamble and cJSON contributors");
    logger->Info("ProtoThreads by Adam Dunkels");
    logger->Info("HttpParser by Joyent, Inc. and other Node contributors");
    logger->Info("");
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
    logger->Debug("Serial opened.");
}

void initEthernet() {
    logger->Info("Initializing Ethernet");
    for (int index = 1; index <= 5; index++) {
        if (Ethernet.begin(const_cast<byte *>(mac)) == 0) {
            logger->Error(String("DHCP failed. Retry ") + String(index));
            clearWriteScreen(screen, (String("ETH ERR: ") + String(index)).c_str(), 300);
        } else {
            logger->Debug("DHCP OK.");
            logger->Debug("    Ethernet information: IP, DNS, Gateway");
            logger->Debug(String(Ethernet.localIP()));
            logger->Debug(String(Ethernet.dnsServerIP()));
            logger->Debug(String(Ethernet.gatewayIP()));
            clearWriteScreen(screen, "ETH OK", 300);
            break;
        }
    }
    logger->Info("Done.");

    logger->Info("Setting up HTTP Parser");
    clearWriteScreen(screen, "PARSER SETUP", 300);
    http_parser_settings_init(httpParserSettings);
    http_parser_init(httpParser, http_parser_type::HTTP_RESPONSE);
    httpParserSettings->on_body = onBodyReceivedCallback;
    httpParserSettings->on_message_begin = onMessageBeginCallback;
    httpParserSettings->on_message_complete = onMessageEndCallback;
    logger->Info("Done.");
}

void initLcd() {
    logger->Info("Initializing LCD");
    screen->init();
    screen->clear();
    screen->setBacklight(true);
    logger->Info("Done.");
}

void initDht() {
    logger->Info("Initializing DHT");
    airSensor->setup(dhtPin, DHT::DHT_MODEL_t::DHT11);
    logger->Debug(airSensor->getStatusString());
    logger->Info("Done.");
}

#pragma endregion

#pragma region threaded_worker

PT_THREAD(maintainEthernet(pt *pt)) {
    PT_BEGIN(pt);
    logger->Info("Maintaining Ethernet connection");
    Ethernet.maintain();
    PT_YIELD(pt);
    logger->Info("Done.");
    PT_TIMER_DELAY(pt, maintainEthernetInterval);
    PT_END(pt);
}

PT_THREAD(uploadSensorData(pt *pt)) {
    PT_BEGIN(pt);
    currentGroundHum = analogRead(groundHumSensorPin);
    currentLightValue = analogRead(lightSensorPin);
    currentAirTemp = airSensor->getTemperature();
    currentAirHum = airSensor->getHumidity();

    logger->Info("Uploading sensor data");
    if (webUploader->connect(webServerIp, webServerPort)) {
        logger->Debug("Connection established.");

        webUploader->print(String("GET /api/v1.1/upload.php?air_temp=") + String(currentAirTemp) \
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
            do
            {
                String str = webUploader->readString();
                respond += str;
            } while (webUploader->available());
            http_parser_init(httpParser, HTTP_RESPONSE);
            http_parser_execute(httpParser, httpParserSettings, respond.c_str(), strlen(respond.c_str()));
        }
        webUploader->stop();
        logger->Info("Done. Connection closed.");
        delay(1000);
        break;
    } else {
        logger->Error("Connection broke.");
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

    logger->Info("Init done.");
}

void loop() {
    uploadSensorData(&uploadSensorData_ctrl);
    maintainEthernet(&maintainEthernet_ctrl);
}
#pragma endregion