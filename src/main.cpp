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
#include <tinyxml.h>

const int InterruptDetectPin = 2;
const int relayOnePin = 22;
const int relayTwoPin = 23;
const int relayThreePin = 24;
const int dhtPin = 25;
const int lightSensorPin = A1;
const int groundHumSensorPin = A0;
const long uploadInterval = 1000L * 20; //MS->S S->M M->H
const long maintainEthernetInterval = 1000L * 60 * 60 * 2;
const long checkSensorInterval = 1000L * 15;

const char *webServerAddress = "192.168.1.120";
const int webServerPort = 80;
const char *actionServerAddress = "192.168.1.120";
const int actionServerPort = 20000;

byte mac[] = {0xB0, 0x83, 0xFE, 0x69, 0x1C, 0x9A};

LiquidCrystal_I2C *screen = new LiquidCrystal_I2C(0x27, 16, 2);
DHT *airSensor = new DHT();
EthernetClient *webUploader = new EthernetClient();
EthernetClient *actionsRequester = new EthernetClient();

TiXmlDocument *respondXml = new TiXmlDocument();

bool isEthernetOk = false;
bool isConnectionOk = false;
bool isDhtOk = false;
bool isLcdOk = false;

float currentAirTemp = 0;
float currentAirHum = 0;
int currentLightValue = 0;
int currentGroundHum = 0;

struct pt uploadSensorData_ctrl;
struct pt maintainEthernet_ctrl;
struct pt readSensorData_ctrl;

//User methods
void pinTwoInterruptHandler() {

}

byte *readEthernet() {
    Serial.println("Reading Ethernet");
    if (!webUploader->connected()) {
        Serial.println("Not connected.");
        Serial.println("Done.");
        return NULL;
    }

    size_t packet_size = 1;
    byte *packet = MALLOC_HEAP(packet_size, byte);

    do
    {
        packet[packet_size - 1] = webUploader->read();
        packet_size ++;
        packet = REALLOC_HEAP(packet, packet_size, byte);
    } while (webUploader->available());
    
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
        if (webUploader->connect(webServerAddress, webServerPort) && actionsRequester->connect(actionServerAddress, actionServerPort)) {
            Serial.println("Test connection established.");
            webUploader->flush();
            actionsRequester->flush();
            Serial.println("Test connection flushed.");
            webUploader->stop();
            actionsRequester->stop();
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

TiXmlElement *readXmlRootElementString(const char * str) {
    TiXmlDocument *doc = new TiXmlDocument();
    doc->Parse(str);
    TiXmlElement *elem = doc->RootElement();
    return elem;
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
            "Accept: text/html, */*\r\n" \
            "Host: ") + String(webServerAddress) + String(":") + String(webServerPort) + String("\r\n") + String( \
            "User-Agent: arduino/mega2560\r\n" \
            "Connection: close\r\n" \
            "\r\n" \
            ""
        )
    );
    webUploader->print(String("GET /upload.php?air_temp=") + String(currentAirTemp) \
            + String("&air_hum=") + currentAirHum \
            + String("&air_light=") + currentLightValue \
            + String("&ground_hum=") + currentGroundHum \
            + String(" HTTP/1.1\r\n" \
            "Accept: text/html, */*\r\n" \
            "Host: ") + String(webServerAddress) + String(":") + String(webServerPort) + String("\r\n") + String( \
            "User-Agent: arduino/mega2560\r\n" \
            "Connection: close\r\n" \
            "\r\n" \
            ""
        ));
    
    do
    {
        Serial.println(webUploader->readString());
    } while (webUploader->available());

    webUploader->stop();
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
