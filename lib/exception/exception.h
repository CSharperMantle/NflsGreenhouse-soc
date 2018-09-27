#ifndef __EXCEPTION_H__
#define __EXCEPTION_H__

#include <stdlib.h>

class Exception {
public:
    Exception() {
        this->msg = NULL;
        this->inner = NULL;
    }
    Exception(const char * msg) {
        this->msg = msg;
        this->inner = NULL;
    }
    Exception(const char * msg, Exception * inner) {
        this->msg = msg;
        this->inner = inner;
    }
    ~Exception() { 
        if (this->inner != NULL) {
            delete this->inner, 
            this->inner = NULL;
        }
        if (this->msg != NULL) {
            free((void *)this->msg); 
            this->msg = NULL; 
        }
    }
private:
    const char * msg;
    const Exception *inner;
}

#endif // __EXCEPTION_H__
