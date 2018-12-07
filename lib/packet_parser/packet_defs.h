/**
 * packet_defs.h
 * Copyright (c)2018 Ningbo Foreign Language School. All rights reserved.
 * Written by Mantle Jonse and Jones Ma.
 * Defined neccesary headers and types and consts that may need by packet
 * processing.
 * Dependencies:
 * <stdint.h>
 * <stdlib.h>
 * <stddef.h>
 * Licensed under GNU GPLv3
*/

#pragma once
#ifndef __PACKET_DEFS_H__
#define __PACKET_DEFS_H__
#include <stdint.h>
#include <stddef.h>
#include <stdlib.h>
#include <WString.h>

#define PACKET_SIZE(packet, type) (sizeof(packet) / sizeof(type))
#define MALLOC_HEAP(size, type) ((type *)malloc((size) * sizeof(type)))
#define CALLOC_HEAP(n_elem, type) ((type *)calloc((n_elem), sizeof(type)))
#define REALLOC_HEAP(pointer, size, type) ((type *)realloc(pointer, (size) * sizeof(type)))
#define FREE_HEAP(pointer) \
if (pointer != NULL) { \
    free(pointer); \
    pointer = NULL; \
}

#ifdef USING_PACKET_PARSING
typedef enum {
    pSystemOn = 0x00,
    pSystemOff = 0x01,
    pDataAccepted = 0x02,
    pSendDigital = 0x03,
    pSendAnalog = 0x04,
    pSendFloat = 0x05,
    pNull = 0xFF,
} PacketType;

typedef uint8_t byte;

const byte packetBegin = 0xF1;
const byte packetEnd = 0xF2;

//Example packet for systemOn event
const byte systemOnPacket[] = {packetBegin, 0x01, 0x00, PacketType::pSystemOn, 0x00, 0x00, 0x00, packetEnd};

#endif /* USING_PACKET_PARSING */

#ifdef USING_PACKET_ENUM
enum PROGMEM ActionType {
    RELAY_ACTION = 1,
    DEVICE_ACTION = 2,
    RETRANSMIT_ACTION = 3,
    LCD_BACKLIGHT_SET = 4,
};

enum PROGMEM DeviceId {
    DEVICE_LCD = -1,
};

#endif /* USING_PACKET_ENUM */

#endif /* __PACKET_DEFS_H__ */