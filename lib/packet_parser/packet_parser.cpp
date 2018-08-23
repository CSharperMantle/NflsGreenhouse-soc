/**
 * Copyright (c)2018 Ningbo Foregn Language School
 * Written by Mantle Jonse and Jones Ma.
 * Defined methods in "packet_parser.h"
 * Dependencies:
 * "packet_parser.h"
 * |--"packet_defs.h"
 *    |--<stdint.h>
 *    |--<stddef.h>
 *    |--<stdlib.h>
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

byte *getPacketContent(byte *packet) {
    size_t packetSize = PACKET_SIZE(packet, byte);
    byte *content = MALLOC_HEAP(packetSize - 4, byte);
    for(size_t index = 4; index < packetSize - 1; index++)
    {
        content[index] = packet[index];
    }
    return content;
}