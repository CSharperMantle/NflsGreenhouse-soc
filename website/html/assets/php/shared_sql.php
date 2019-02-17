<?php
    /*
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */
    
    const INSERT_DATA_SQL = "CALL addData(?, ?, ?, ?);";
    const INSERT_ALERT_SQL = "CALL addAlert(?, ?);";
    const FETCH_LATEST_SQL = "SELECT * FROM data WHERE data.id = (SELECT MAX(id) FROM data) LIMIT 1;";
    const FETCH_TOTAL_COMMITS_SQL = "SELECT COUNT(id) count FROM data LIMIT 1;";
    const FETCH_TOTAL_ALERTS_SQL = "SELECT COUNT(id) count FROM alerts WHERE alerts.is_ok!=0 LIMIT 1;";
    const FETCH_LATEST_AIR_TEMP_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=0 ORDER BY id DESC LIMIT 1;";
    const FETCH_LATEST_AIR_HUM_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=1 ORDER BY id DESC LIMIT 1;";
    const FETCH_LATEST_AIR_LIGHT_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=2 ORDER BY id DESC LIMIT 1;";
    const FETCH_LATEST_GROUND_HUM_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=3 ORDER BY id DESC LIMIT 1;";
    const FETCH_ALL_HISTORY_DATA_SQL = "SELECT * FROM data ORDER BY id DESC LIMIT 1000;";
    const FETCH_AIR_TEMP_SQL = "SELECT id, air_temp, timestamp FROM data ORDER BY id DESC LIMIT 20;";
    const FETCH_AIR_HUM_SQL = "SELECT id, air_hum, timestamp FROM data ORDER BY id DESC LIMIT 20;";
    const FETCH_AIR_LIGHT_SQL = "SELECT id, air_light, timestamp FROM data ORDER BY id DESC LIMIT 20;";
    const FETCH_GROUND_HUM_SQL = "SELECT id, ground_hum, timestamp FROM data ORDER BY id DESC LIMIT 20;";
    const FETCH_USER_EXIST_SQL = "SELECT id, username, password FROM users WHERE username=? LIMIT 1;";
    const FETCH_COMMITS_EACH_DAY_SQL = "SELECT COUNT(id) count FROM data GROUP BY DATE_FORMAT(timestamp,'%Y-%m-%d') ORDER BY DATE_FORMAT(timestamp,'%Y-%m-%d') ASC LIMIT 10;";
    const FETCH_ALERTS_EACH_DAY_SQL = "SELECT COUNT(id) count FROM alerts WHERE alerts.is_ok!=0 GROUP BY DATE_FORMAT(timestamp,'%Y-%m-%d') ORDER BY DATE_FORMAT(timestamp,'%Y-%m-%d') ASC LIMIT 10;";
    const FETCH_TODAY_COMMITS_COUNT_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM data GROUP BY day ORDER BY day DESC LIMIT 1;";
    const FETCH_TODAY_ALERTS_COUNT_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM alerts WHERE is_ok!=0 GROUP BY day ORDER BY day DESC LIMIT 1;";
    const FETCH_LATEST_ALERT_SQL = "SELECT * FROM alerts WHERE alerts.id = (SELECT MAX(id) FROM alerts) LIMIT 1;";
    const FETCH_LATEST_ALERT_TIMESTAMP_SQL = "SELECT timestamp FROM alerts ORDER BY id DESC LIMIT 1;";
?>