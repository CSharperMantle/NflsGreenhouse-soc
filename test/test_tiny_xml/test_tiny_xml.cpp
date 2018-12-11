#define USING_PACKET_ENUM

#include <Arduino.h>
#include <tinyxml.h>
#include <unity.h>
#include <packet_defs.h>

const PROGMEM char xmlDoc[] = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\
<root>\
    <timestamp>\
        <hour>14</hour>\
        <minute>32</minute>\
        <second>54</second>\
    </timestamp>\
    <actions>\
        <!--Action types: \
            1: RELAY (with params)\
            2: DEVICE (with params)\
            3: RETRANSMIT\
            4: LCD_BACKLIGHT_SET (with params)\
            -->\
        <action>\
            <type>1</type>\
            <target_id>22</target_id>\
            <param>1</param>\
        </action>\
        <action>\
            <type>1</type>\
            <target_id>23</target_id>\
            <param>0</param>\
        </action>\
        <action>\
            <type>2</type>\
            <target_id>-1</target_id>\
            <param>Hello, LCD</param>\
        </action>\
        <action>\
            <type>3</type>\
            <target_id></target_id>\
            <param></param>\
        </action>\
        <action>\
            <type>4</type>\
            <target_id>-1</target_id>\
            <param>1</param>\
        </action>\
    </actions>\
</root>";

void test_tinyXml_load() {
    TiXmlDocument *doc = new TiXmlDocument();
    doc->Parse(xmlDoc);
    TEST_ASSERT_EQUAL_STRING("root", doc->RootElement()->Value());
    TEST_ASSERT_EQUAL_STRING("14", doc->RootElement()->FirstChild("timestamp")->FirstChild("hour")->FirstChild()->Value());
}

void test_tinyXml_parse() {
    TiXmlDocument *doc = new TiXmlDocument();
    doc->Parse(xmlDoc, NULL, TIXML_ENCODING_UTF8);
    TiXmlHandle *handle = new TiXmlHandle(doc);
    TiXmlElement *action = handle->FirstChild("root").FirstChild("actions").FirstChild("action").ToElement();
    TiXmlElement *timestamp = handle->FirstChild("root").FirstChild("timestamp").ToElement();
    int hour = atoi(timestamp->FirstChild("hour")->ToText()->Value());
    int minute = atoi(timestamp->FirstChild("minute")->ToText()->Value());
    int second = atoi(timestamp->FirstChild("second")->ToText()->Value());
    TEST_ASSERT_EQUAL(14, hour);
    TEST_ASSERT_EQUAL(32, minute);
    TEST_ASSERT_EQUAL(54, second);
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
                digitalWrite(targetIdValue, LOW);
            } else {
                digitalWrite(targetIdValue, HIGH);
            }
        } else if (typeValue == ActionType::DEVICE_ACTION) {
            // Action with other devices requested
            int targetIdValue = atoi(targetId->ToText()->Value());
            const char *paramValue = param->ToText()->Value();
            if (targetIdValue == DeviceId::DEVICE_LCD)
            {
            } else {
            }
            //TODO: Add more devices
        } else if (typeValue == ActionType::RETRANSMIT_ACTION) {
            // Retransmitting requested
            //TODO: Add retransmitter
        } else if (typeValue == ActionType::LCD_BACKLIGHT_SET) {
            // LCD backlight setting requested
            const char *paramValue = param->ToText()->Value();
            int targetIdValue = atoi(targetId->ToText()->Value());
        }
        else {
        }
    }

    //FIXME: Maybe a bug
    delete handle;
    delete doc;
}

void setup() {
    delay(2000);
    UNITY_BEGIN();
}

void loop() {
    RUN_TEST(test_tinyXml_parse);
    UNITY_END();
}

