<?php
    $host = 'localhost:3306';
    $dbname = 'plant_data';
    $passwd = 'plant_client';
    $username = 'plant_client';
    $dbdsn = "mysql:host=$host;dbname=$dbname";

    $INSERT_DATA_SQL = "SET @max = (SELECT MAX(id) FROM data); INSERT INTO data (id, air_temp, air_hum, air_light, ground_hum) VALUES (@max + 1, ?, ?, ?, ?);";
    $FETCH_LATEST_SQL = "SELECT * FROM data WHERE data.id = (SELECT MAX(id) FROM data);";
    $FETCH_TOTAL_COUNT_SQL = "SELECT MAX(id) FROM data;";
    $FETCH_ALL_SQL = "SELECT * FROM data;";
    $FETCH_AIR_TEMP_SQL = "SELECT id, air_temp FROM data;";
    $FETCH_AIR_HUM_SQL = "SELECT id, air_hum FROM data;";
    $FETCH_AIR_LIGHT_SQL = "SELECT id, air_light FROM data;";
    $FETCH_GROUND_HUM_SQL = "SELECT id, ground_hum FROM data;";

    $lightSwitchValve = 500;
    $groundHumSwitchValve = 500;
    //TODO: Real value needed.

    function purify_data (string $data) {
        $data = trim($data);
        $data = htmlspecialchars($data, ENT_COMPAT | ENT_HTML5);
        $data = stripslashes($data);
        return $data;
    }

    function run_query (PDO $connection, string $sql, array $args = array()) {
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    function run_query_fetch_all (PDO $connection, string $sql, array $args = array()) {
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function print_all_commits_each_item (string $timestamp, string $oddity, string $type, string $air_temp, string $air_hum, string $air_light, string $ground_hum) {
        print "
            <tr class=\"$oddity\">
                <td>$timestamp</td>
                <td>$type</td>
                <td>$air_temp</td>
                <td>$air_hum</td>
                <td>$air_light</td>
                <td>$ground_hum</td>
            </tr>
        ";
    }
?>