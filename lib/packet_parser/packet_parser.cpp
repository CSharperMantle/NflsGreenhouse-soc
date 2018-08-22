#include "packet_parser.h"

bool isPacket(byte *packet) {
    
    if (packet[0] != packetBegin 
        || 
        packet[sizeof(packet) / sizeof(byte) - 1] != packetEnd) {
        return false;
    } else return true;
    
}