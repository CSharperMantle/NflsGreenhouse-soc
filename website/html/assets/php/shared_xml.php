<?php
    /*
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
        print "<type>$type</type>";
        print "<target_id>$target_id</target_id>";
        print "<param>$param</param>";
        print "</action>";
    }
?>