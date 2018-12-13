/**
 * packet_parse.h
 * Copyright (c)2018 Ningbo Foreign Language School. All rights reserved.
 * Written by Mantle Jonse and Jones Ma.
 * Declared methods for packet processing.
 * Dependencies:
 * "packet_defs.hpp"
 * |--<stdint.h>
 * |--<stddef.h>
 * |--<stdlib.h>
 * Licensed under GNU GPLv3
*/
#pragma once
#ifndef __PACKET_PARSER__HPP__
#define __PACKET_PARSER__HPP__

#ifdef USING_PACKET_PARSING
#include "packet_defs.hpp"

bool isPacket(byte *packet);

PacketType parsePacketType(byte *packet);

String parsePacketTypeString(byte *packet);

byte *getPacketContent(byte *packet);
#endif /* USING_PACKET_PARSING */

#endif /* __PACKET_PARSER__HPP__ */