#pragma once
#ifndef __PACKET_DEFS_H__
#define __PACKET_DEFS_H__
#include <stdint.h>

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

#endif /* __PACKET_DEFS_H__ */