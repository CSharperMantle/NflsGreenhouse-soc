#include <Arduino.h>
#include <tinyxml.h>
#include <unity.h>

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

void setup() {
    delay(2000);
    UNITY_BEGIN();
}

void loop() {
    RUN_TEST(test_tinyXml_load);
    UNITY_END();
}

