#ifndef LOGGER__HPP
#define LOGGER__HPP

#include <HardwareSerial.h>
class Logger {
public:
    Logger(HardwareSerial *pHS) {
        this->serial = pHS;
    }
    Logger(HardwareSerial hs) {
        this->serial = &hs;
    }
    ~Logger() {
        this->serial = NULL;
    }

    void Info(const char *str) {
        this->serial->println(String("[INFO]>> ") + String(str));
    }
    void Debug(const char *str) {
        this->serial->println(String("[DEBUG]>> ") + String(str));
    }
    void Warning(const char *str) {
        this->serial->println(String("[WARNING]>> ") + String(str));
    }
    void Error(const char *str) {
        this->serial->println(String("[ERROR]>> ") + String(str));
    }

    void Info(String str) {
        this->serial->println(String("[INFO]>> ") + str);
    }
    void Debug(String str) {
        this->serial->println(String("[DEBUG]>> ") + str);
    }
    void Warning(String str) {
        this->serial->println(String("[WARNING]>> ") + str);
    }
    void Error(String str) {
        this->serial->println(String("[ERROR]>> ") + str);
    }
private:
    HardwareSerial *serial = NULL;
};

#endif