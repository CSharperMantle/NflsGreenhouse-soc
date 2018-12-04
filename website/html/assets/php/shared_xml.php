<?php
/**
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */

    function xml_print_header(string $version = '1.0', string $encoding = 'UTF-8') {
        print "<?xml version=\"$version\" encoding=\"$encoding\"?>";
    }

    function xml_print_root_begin() {
        print "<root>";
    }

    function xml_print_root_end() {
        print "</root>";
    }

    function xml_print_timestamp(int $hour, int $minute, int $second) {
        print "<timestamp><hour>$hour</hour><minute>$minute</minute><second>$second</second></timestamp>";
    }

    function xml_print_actions_begin() {
        print "<actions>";

    }

    function xml_print_actions_end() {
        print "</actions>";
    }

    function xml_print_action(int $type, int $target_id, string $param) {
        /*
        <action>
            <type>1</type>
            <target_id>22</target_id>
            <param>1</param>
        </action>
        */
        print "<action>";
        print "    <type>$type</type>";
        print "    <target_id>$target_id</target_id>";
        print "    <param>$param</param>";
        print "</action>";
    }

    class AlertType {
        public const AIR_TEMP = 0;
        public const AIR_HUM = 1;
        public const AIR_LIGHT = 2;
        public const GROUND_HUM = 3;
        public const OK = 0;
        public const HIGH = 1;
        public const LOW = 2;
    }

    class AlertInfo {
        public const GOOD = 0;
        public const INFO = 1;
        public const WARNING = 2;
        public const DANGER = 3;
    }
    
    class ActionType {
        public const RELAY_ACTION = 1;
        public const DEVICE_ACTION = 2;
        public const RETRANSMIT_ACTION = 3;
    }

    class RelayAction {
        public const ON = 1;
        public const OFF = 0;
    }

    class SkySheetAction {
        public const MOVEON = 1;
        public const MOVEOFF = 0;
        public const STOP = 2;
    }
?>