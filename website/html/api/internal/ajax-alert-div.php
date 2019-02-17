<?php
    /*
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */
    
    require '..\\..\\assets\\php\\shared_sql.php';
    require '..\\..\\assets\\php\\shared_db.php';
    require '..\\..\\assets\\php\\shared_html.php';
    require '..\\..\\assets\\php\\shared_const.php';
    require '..\\..\\assets\\php\\shared_util.php';

    try {
        $db = DBConnectionSingleton::getInstance();

        $result = run_query_fetch($db, FETCH_LATEST_AIR_TEMP_ALERT_SQL);
        $air_temp_is_ok = $result['is_ok'];
        $result = run_query_fetch($db, FETCH_LATEST_AIR_HUM_ALERT_SQL);
        $air_hum_is_ok = $result['is_ok'];
        $result = run_query_fetch($db, FETCH_LATEST_AIR_LIGHT_ALERT_SQL);
        $air_light_is_ok = $result['is_ok'];
        $result = run_query_fetch($db, FETCH_LATEST_GROUND_HUM_ALERT_SQL);
        $ground_hum_is_ok = $result['is_ok'];
    } catch (PDOException $e) {
        $error_occur = true;
    }

    if (isset($error_occur)) {
        print_alert(AlertInfo::DANGER, '错误！', '加载页面时出错。页面不会正常工作。');
        http_response_code(503);
        exit();
    }
    switch ($air_temp_is_ok) {
      case AlertType::HIGH:
        print_alert(AlertInfo::WARNING, '警告!', '空气温度过高。');
        $is_all_fine = false;
        break;
      case AlertType::LOW:
        print_alert(AlertInfo::WARNING, '警告!', '空气温度过低。');
        $is_all_fine = false;
        break;
      default:
        break;
    }
    switch ($air_hum_is_ok) {
      case AlertType::HIGH:
        print_alert(AlertInfo::WARNING, '警告!', '空气湿度过高。');
        $is_all_fine = false;
        break;
      case AlertType::LOW:
        print_alert(AlertInfo::WARNING, '警告!', '空气湿度过低。');
        $is_all_fine = false;
        break;
      default:
        break;
    }
    switch ($air_light_is_ok) {
      case AlertType::HIGH:
        print_alert(AlertInfo::WARNING, '警告!', '光线过强。');
        $is_all_fine = false;
        break;
      case AlertType::LOW:
        print_alert(AlertInfo::WARNING, '警告!', '光线过弱。');
        $is_all_fine = false;
        break;
      default:
        break;
    }
    switch ($ground_hum_is_ok) {
      case AlertType::HIGH:
        print_alert(AlertInfo::WARNING, '警告!', ' 土壤过湿。');
        $is_all_fine = false;
        break;
      case AlertType::LOW:
        print_alert(AlertInfo::WARNING, '警告!', '土壤过干。');
        $is_all_fine = false;
        break;
      default:
        break;
    }
    if (!isset($is_all_fine)) {
      print_alert(AlertInfo::GOOD, '放心！', '一切正常。');
    }
?>