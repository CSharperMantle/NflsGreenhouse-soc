#ifndef LOGGER__HPP
#define LOGGER__HPP

#include <HardwareSerial.h>

enum LoggingLevel {
    DEBUG = 0,
    INFO,
    WARNING,
    ERROR
};

class Logger {
public:
    Logger(HardwareSerial *pHardwareSerial, LoggingLevel loggingLevel) {
        this->serial = pHardwareSerial;
        this->loggingLevel = loggingLevel;
    }
    Logger(HardwareSerial hardwareSerial, LoggingLevel loggingLevel) {
        this->serial = &hardwareSerial;
        this->loggingLevel = loggingLevel;
    }
    ~Logger() {
        this->serial = NULL;
    }

    void Info(const char *str) {
        if (this->loggingLevel <= LoggingLevel::INFO)
            this->serial->println(String("[INFO]>> ") + String(str));
    }
    void Debug(const char *str) {
        if (this->loggingLevel <= LoggingLevel::DEBUG)
            this->serial->println(String("[DEBUG]>> ") + String(str));
    }
    void Warning(const char *str) {
        if (this->loggingLevel <= LoggingLevel::WARNING)
            this->serial->println(String("[WARNING]>> ") + String(str));
    }
    void Error(const char *str) {
        if (this->loggingLevel <= LoggingLevel::ERROR)
            this->serial->println(String("[ERROR]>> ") + String(str));
    }

    void Info(String str) {
        if (this->loggingLevel <= LoggingLevel::INFO)
            this->serial->println(String("[INFO]>> ") + str);
    }
    void Debug(String str) {
        if (this->loggingLevel <= LoggingLevel::DEBUG)
            this->serial->println(String("[DEBUG]>> ") + str);
    }
    void Warning(String str) {
        if (this->loggingLevel <= LoggingLevel::WARNING)
            this->serial->println(String("[WARNING]>> ") + str);
    }
    void Error(String str) {
        if (this->loggingLevel <= LoggingLevel::ERROR)
            this->serial->println(String("[ERROR]>> ") + str);
    }
private:
    HardwareSerial *serial = NULL;
    LoggingLevel loggingLevel = LoggingLevel::ERROR;
};

#endif