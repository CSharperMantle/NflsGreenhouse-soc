#pragma once
#ifndef __PACKET_PARSER_H__
#define __PACKET_PARSER_H__

#include "packet_defs.h"

bool isPacket(byte *packet);

PacketType parsePacketType(byte *packet);

byte *getPacketContent(byte *packet);

#endif /* __PACKET_PARSER_H__ */