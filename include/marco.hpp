#ifndef __MARCO_HPP__
#define __MARCO_HPP__


#include "cJSON.h"

#define CJSON_CHECK_PTR(ptr) \
if (!ptr) { \
    Serial.println("ERROR found."); \
    Serial.println(cJSON_GetErrorPtr()); \
    return; \
}

#define CJSON_DEL_PTR(ptr) \
if (ptr) { \
    cJSON_Delete(ptr); \
    ptr = NULL; \
}

#define CJSON_FREE_PTR(ptr) \
if (ptr) { \
    cJSON_free(ptr); \
    ptr = NULL; \
}

#define CONST_CAST(src, dst_type) (const_cast<dst_type>(src))

#define HTTP_PARSER_CALLBACK(name_args) int name_args 


#endif // __MARCO_HPP__
