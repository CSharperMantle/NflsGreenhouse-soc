/**
 * packet_parse.h
 * Copyright (c)2018 Ningbo Foreign Language School. All rights reserved.
 * Written by Mantle Jonse and Jones Ma.
 * Declared methods for packet processing.
 * Dependencies:
 * "packet_defs.h"
 * |--<stdint.h>
 * |--<stddef.h>
 * |--<stdlib.h>
 * Licensed under GNU GPLv3
*/
#pragma once
#ifndef __PACKET_PARSER_H__
#define __PACKET_PARSER_H__

#include "packet_defs.h"

bool isPacket(byte *packet);

PacketType parsePacketType(byte *packet);

String parsePacketTypeString(byte *packet);

byte *getPacketContent(byte *packet);

#endif /* __PACKET_PARSER_H__ */