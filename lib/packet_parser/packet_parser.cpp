/**
 * packet_parser.cpp
 * Copyright (c)2018 Ningbo Foreign Language School. All rights reserved.
 * Written by Mantle Jonse and Jones Ma.
 * Defined methods in "packet_parser.h"
 * Dependencies:
 * "packet_parser.h"
 * |--"packet_defs.h"
 *    |--<stdint.h>
 *    |--<stddef.h>
 *    |--<stdlib.h>
 * Licensed under GNU GPLv3
*/
#include "packet_parser.h"

bool isPacket(byte *packet) {
    
    if (packet[0] != packetBegin 
        || 
        packet[PACKET_SIZE(packet, byte) - 1] != packetEnd) {
        return false;
    } else return true;
    
}

PacketType parsePacketType(byte *packet) {
    if (!isPacket(packet)) return PacketType::pNull;  
    switch (packet[1])
    {
        case PacketType::pSystemOn:
            return PacketType::pSystemOn;
        case PacketType::pSystemOff:
            return PacketType::pSystemOff;
        case PacketType::pDataAccepted:
            return PacketType::pDataAccepted;
        case PacketType::pSendAnalog:
            return PacketType::pSendAnalog;
        case PacketType::pSendDigital:
            return PacketType::pSendDigital;
        case PacketType::pSendFloat:
            return PacketType::pSendFloat;
        default:
            return PacketType::pNull;
    }
}

String parsePacketTypeString(byte *packet) {
    if (!isPacket(packet)) return "PacketType::pNull";  
    switch (packet[1])
    {
        case PacketType::pSystemOn:
            return "PacketType::pSystemOn";
        case PacketType::pSystemOff:
            return "PacketType::pSystemOff";
        case PacketType::pDataAccepted:
            return "PacketType::pDataAccepted";
        case PacketType::pSendAnalog:
            return "PacketType::pSendAnalog";
        case PacketType::pSendDigital:
            return "PacketType::pSendDigital";
        case PacketType::pSendFloat:
            return "PacketType::pSendFloat";
        default:
            return "PacketType::pNull";
    }
}

byte *getPacketContent(byte *packet) {
    if (!isPacket(packet)) return NULL;
    size_t packetSize = PACKET_SIZE(packet, byte);
    byte *content = MALLOC_HEAP(packetSize - 4, byte);
    for(size_t index = 4; index < packetSize - 1; index++)
    {
        content[index] = packet[index];
    }
    return content;
}