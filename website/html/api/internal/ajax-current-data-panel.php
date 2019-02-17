<?php
    /*
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */
    
    require '..\\..\\assets\\php\\shared_html.php';
    require '..\\..\\assets\\php\\shared_const.php';
    require '..\\..\\assets\\php\\shared_sql.php';
    require '..\\..\\assets\\php\\shared_db.php';
    require '..\\..\\assets\\php\\shared_ajax.php';
    require '..\\..\\assets\\php\\shared_util.php';

    if (!isset($_POST['id'])) {
        terminate(400);
    }

    $id = $_POST['id'];

    if (!is_numeric($id)) {
        terminate(400);
    }

    try {
        $db = DBConnectionSingleton::getInstance();
    }
    catch (PDOException $e) {
        terminate(500);
    }

    $result = run_query_fetch($db, FETCH_LATEST_SQL);
    $air_temp = $result['air_temp'];
    $air_hum = $result['air_hum'];
    $air_light = $result['air_light'];
    $ground_hum = $result['ground_hum'];

    switch ($id) {
        case AjaxCurrentDataType::AIR_TEMP:
            if ($air_temp > airTempSwitchValveHigh) {
                print_panel("空气温度", $air_temp . 'C', AlertInfo::DANGER);
            }
            elseif ($air_temp < airTempSwitchValveLow) {
                print_panel("空气温度", $air_temp . 'C', AlertInfo::WARNING);
            }
            else {
                print_panel("空气温度", $air_temp . 'C', AlertInfo::GOOD);
            }
            break;
        case AjaxCurrentDataType::AIR_HUM:
            if ($air_hum > airHumSwitchValveHigh) {
                print_panel("空气湿度", $air_hum . '%', AlertInfo::DANGER);
            }
            elseif ($air_hum < airHumSwitchValveLow) {
                print_panel("空气湿度", $air_hum . '%', AlertInfo::WARNING);
            }
            else {
                print_panel("空气湿度", $air_hum . '%', AlertInfo::GOOD);
            }
            break;
        case AjaxCurrentDataType::AIR_LIGHT:
            if ($air_light > airLightSwitchValveHigh) {
                print_panel("光强度", $air_light . '/1000%', AlertInfo::DANGER);
            }
            elseif ($air_light < airLightSwitchValveLow) {
                print_panel("光强度", $air_light . '/1000%', AlertInfo::WARNING);
            }
            else {
                print_panel("光强度", $air_light . '/1000%', AlertInfo::GOOD);
            }
            break;
        case AjaxCurrentDataType::GROUND_HUM:
            if ($ground_hum > groundHumSwitchValveHigh) {
                print_panel("地面湿度", $ground_hum . '/1000%', AlertInfo::DANGER);
            }
            elseif ($ground_hum < groundHumSwitchValveHigh) {
                print_panel("地面湿度", $ground_hum . '/1000%', AlertInfo::WARNING);
            }
            else {
                print_panel("地面湿度", $ground_hum . '/1000%', AlertInfo::GOOD);
            }
            break;
        
        default:
            terminate(400);
            break;
    }
?>