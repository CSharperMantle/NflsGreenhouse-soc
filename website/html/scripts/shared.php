<?php
    $host = 'localhost:3306';
    $dbname = 'plant_data';
    $passwd = 'plant_client';
    $username = 'plant_client';
    $dbdsn = "mysql:host=$host;dbname=$dbname";

    $INSERT_SQL = 'INSERT INTO data (air_temp, air_hum, air_light, ground_hum) VALUES (?, ?, ?, ?);';
    $FETCH_SQL = 'SELECT * FROM data WHERE data.id = (SELECT MAX(id) FROM data);';
    $FETCH_TOTAL_COUNT_SQL = 'SELECT MAX(id) FROM data;';

    $lightSwitchValve = 500;
    $groundHumSwitchValve = 500;
    //TODO: Real value needed.

    function purifyData($data) {
        $data = trim($data);
        $data = htmlspecialchars($data, ENT_COMPAT | ENT_HTML5);
        $data = stripslashes($data);
        return $data;
    }
?>