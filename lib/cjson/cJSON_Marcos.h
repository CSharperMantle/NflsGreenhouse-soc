#ifndef cJSON_Marcos__h
#define cJSON_Marcos__h

#ifdef __cplusplus
extern "C"
{
#endif

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

#ifdef __cplusplus
}
#endif

#endif