<?php
    /**
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */
    function create_action_array(int $action_type, int $target_id, string $param) {
        return array('action_type' => $action_type, 'target_id' => $target_id, 'param' => $param);
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