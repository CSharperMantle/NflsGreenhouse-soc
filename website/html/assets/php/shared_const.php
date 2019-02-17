<?php
    /*
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */
    
    const airTempSwitchValveLow = 10;
    const airTempSwitchValveHigh = 30;
    const airHumSwitchValveLow = 35;
    const airHumSwitchValveHigh = 70;
    const lightSwitchValveLow = 100;
    const airLightSwitchValveHigh = 965;
    const airLightSwitchValveLow = 950;
    const groundHumSwitchValveLow = 700;
    const groundHumSwitchValveHigh = 1000;
    //TODO: Real value needed.

    const waterPumpPin = 22;
    const fanOnePin = 23;
    const fanTwoPin = 24;
    const airCoolerPin = 25;
    const sideWindowOpenPin = 26;
    const sideWindowClosePin = 27;
    const topWindowOneOpenPin = 28;
    const topWindowOneClosePin = 29;
    const topWindowTwoOpenPin = 30;
    const topWindowTwoClosePin = 31;
    const skySheetOuterOpenPin = 32;
    const skySheetOuterClosePin = 33;
    const skySheetInnerOpenPin = 34;
    const skySheetInnerClosePin = 35;

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