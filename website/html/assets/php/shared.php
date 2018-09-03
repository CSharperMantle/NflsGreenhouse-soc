<?php
    $host = 'localhost:3306';
    $dbname = 'plant_data';
    $passwd = 'plant_client';
    $username = 'plant_client';
    $dbdsn = "mysql:host=$host;dbname=$dbname";

    $INSERT_DATA_SQL = "SET @max = (SELECT MAX(id) FROM data); INSERT INTO data (id, air_temp, air_hum, air_light, ground_hum) VALUES (@max + 1, ?, ?, ?, ?);";
    $INSERT_ALERT_SQL = "SET @max = (SELECT MAX(id) FROM alerts); INSERT INTO alerts (id, message) VALUES (@max + 1, ?);";
    $FETCH_LATEST_SQL = "SELECT * FROM data WHERE data.id = (SELECT MAX(id) FROM data) LIMIT 1;";
    $FETCH_TOTAL_COUNT_SQL = "SELECT MAX(id) FROM data LIMIT 1;";
    $FETCH_ALL_SQL = "SELECT * FROM data LIMIT 1000;";
    $FETCH_AIR_TEMP_SQL = "SELECT id, air_temp FROM data ORDER BY id DESC LIMIT 100;";
    $FETCH_AIR_HUM_SQL = "SELECT id, air_hum FROM data ORDER BY id DESC LIMIT 100;";
    $FETCH_AIR_LIGHT_SQL = "SELECT id, air_light FROM data ORDER BY id DESC LIMIT 100;";
    $FETCH_GROUND_HUM_SQL = "SELECT id, ground_hum FROM data ORDER BY id DESC LIMIT 100;";
    $FETCH_USER_EXIST_SQL = "SELECT id, username, password FROM users WHERE username=? LIMIT 1;";
    $FETCH_COMMITS_EACH_DAY_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM data GROUP BY day ORDER BY day ASC LIMIT 10;";
    $FETCH_ALERTS_EACH_DAY_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM alerts GROUP BY day ORDER BY day ASC LIMIT 10;";
    $FETCH_LATEST_ALERT_SQL = "SELECT * FROM alerts WHERE alerts.id = (SELECT MAX(id) FROM alerts) LIMIT 1;";

    $USER_NOT_REGISTERED_MESSAGE = "<div class=\"alert-warning\">用户不存在</div>";
    $USER_PASSWORD_WRONG_MESSAGE = "<div class=\"alert-danger\">密码或用户名错误</div>";

    $airTempSwitchValveLow = 10;
    $airTempSwitchValveHigh = 30;
    $airHumSwitchValveLow = 35;
    $airHumSwitchValveHigh = 70;
    $lightSwitchValveLow = 100;
    $airLightSwitchValveHigh = 600;
    $groundHumSwitchValveLow = 700;
    $groundHumSwitchValveHigh = 1000;
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

    function print_user_status (string $username, string $current_status) {
        if ($current_status == 'online') $text = 'Available';
        else $text = 'Offline';
        print "
            <div class=\"user-name\">$username</div>
            <div class=\"user-position $current_status\">$text</div>
        ";
    }

    function print_login_button () {
        print "
            <a href=\"pages-login.html\" class=\"dropdown-item\">
                <span class=\"icon mdi mdi-power\"></span> Login
            </a>
        ";
    }

    function print_logoff_button () {
        print "
            <a href=\"pages-logoff.php\" class=\"dropdown-item\">
                <span class=\"icon mdi mdi-power\"></span> Logout
            </a>
        ";
    }

    function print_settings_button () {
        print "
            <a href=\"#\" class=\"dropdown-item\">
                <span class=\"icon mdi mdi-settings\"></span> Settings
            </a>
        ";
    }

    function set_session_logged_in (string $username) {
        
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $username;
    }

    function set_session_logged_off () {
        unset($_SESSION['is_logged_in']);
        unset($_SESSION['username']);
    }
    
    function redirect_to (string $address) {
        header('Content-type: text/html;charset=uft-8');
        header("Location: $address");
    } 
?>