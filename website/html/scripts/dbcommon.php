<?php
    $host = 'db.example.com';
    $dbname = 'plant_data';
    $passwd = 'plant_client';
    $username = 'plant_client';

    $INSERT_SQL = 'INSERT INTO data (air_temp, air_hum, air_light, ground_hum) VALUES (?, ?, ?, ?);';
    $FETCH_SQL = 'SELECT * FROM data WHERE id=MAX(id);';

    function purifyData($data) {
        $data = trim($data);
        $data = htmlspecialchars($data, ENT_COMPAT | ENT_HTML5);
        $data = stripslashes($data);
        return $data;
    }
?>