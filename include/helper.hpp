#ifndef __HELPER_HPP__
#define __HELPER_HPP__


#include <cJSON.h>

inline void hlp_cJSON_appendPinStateToArray(cJSON *array, int pinId, bool pinState[])
{
    /*
     *[
     *    {
     *       "pin_id": $pinId,
     *       "state": $pinState[pinId - 22]
     *    },
     *    ...
     *]
     */
    // a pin state object has two members: pin_id and state.
    // for optimization, this is a inline function

    cJSON *obj = cJSON_CreateObject();
    cJSON_AddItemToObject(obj, "pin_id", cJSON_CreateNumber(pinId));
    // because we record the pin status from PIN22 on, so we make PIN22 as no 0. PIN23 is no 1, PIN24 is no 2, etc
    cJSON_AddItemToObject(obj, "state", cJSON_CreateNumber(pinState[pinId - 22]));
    cJSON_AddItemToArray(array, obj);
}

inline void hlp_cJSON_appendSensorDataToArray(cJSON *array, int dataType, int dataValue)
{
    /*
     *[
     *    {
     *       "data_type": $dataType,
     *       "data_content": $dataValue
     *    },
     *    ...
     *]
     */

    cJSON *obj = cJSON_CreateObject();
    cJSON_AddItemToObject(obj, "data_type", cJSON_CreateNumber(dataType));
    cJSON_AddItemToObject(obj, "data_content", cJSON_CreateNumber(dataValue));
    cJSON_AddItemToArray(array, obj);
}

#endif