<?php
    $host = 'localhost';
    $port = 3306;
    $dbname = 'plant_data';
    $passwd = 'plant_client';
    $username = 'plant_client';
    $dbdsn = "mysql:host=$host;port=$port;dbname=$dbname";

    $INSERT_DATA_SQL = "SET @max = (SELECT MAX(id) FROM data); INSERT INTO data (id, air_temp, air_hum, air_light, ground_hum) VALUES (@max + 1, ?, ?, ?, ?);";
    $INSERT_ALERT_SQL = "SET @max = (SELECT MAX(id) FROM alerts); INSERT INTO alerts (id, alert_type, is_ok) VALUES (@max + 1, ?, ?);";
    $FETCH_LATEST_SQL = "SELECT * FROM data WHERE data.id = (SELECT MAX(id) FROM data) LIMIT 1;";
    $FETCH_TOTAL_COMMITS_SQL = "SELECT COUNT(id) count FROM data LIMIT 1;";
    $FETCH_TOTAL_ALERTS_SQL = "SELECT COUNT(id) count FROM alerts WHERE alerts.is_ok!=0 LIMIT 1;";
    $FETCH_LATEST_AIR_TEMP_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=0 ORDER BY id DESC LIMIT 1;";
    $FETCH_LATEST_AIR_HUM_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=1 ORDER BY id DESC LIMIT 1;";
    $FETCH_LATEST_AIR_LIGHT_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=2 ORDER BY id DESC LIMIT 1;";
    $FETCH_LATEST_GROUND_HUM_ALERT_SQL = "SELECT id, is_ok FROM alerts WHERE alert_type=3 ORDER BY id DESC LIMIT 1;";
    // SELECT * FROM alerts GROUP BY alert_type ORDER BY id DESC;
    $FETCH_ALL_SQL = "SELECT * FROM data LIMIT 1000;";
    $FETCH_AIR_TEMP_SQL = "SELECT id, air_temp, timestamp FROM data ORDER BY id DESC LIMIT 50;";
    $FETCH_AIR_HUM_SQL = "SELECT id, air_hum, timestamp FROM data ORDER BY id DESC LIMIT 50;";
    $FETCH_AIR_LIGHT_SQL = "SELECT id, air_light, timestamp FROM data ORDER BY id DESC LIMIT 50;";
    $FETCH_GROUND_HUM_SQL = "SELECT id, ground_hum, timestamp FROM data ORDER BY id DESC LIMIT 50;";
    $FETCH_USER_EXIST_SQL = "SELECT id, username, password FROM users WHERE username=? LIMIT 1;";
    $FETCH_COMMITS_EACH_DAY_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM data GROUP BY day ORDER BY day ASC LIMIT 10;";
    $FETCH_ALERTS_EACH_DAY_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM alerts WHERE alerts.is_ok!=0 GROUP BY day ORDER BY day ASC LIMIT 10;";
    $FETCH_TODAY_COMMITS_COUNT_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM data GROUP BY day ORDER BY day DESC LIMIT 1;";
    $FETCH_TODAY_ALERTS_COUNT_SQL = "SELECT DATE_FORMAT(timestamp,'%Y-%m-%d') day, COUNT(id) count FROM alerts WHERE is_ok!=0 GROUP BY day ORDER BY day DESC LIMIT 1;";
    $FETCH_LATEST_ALERT_SQL = "SELECT * FROM alerts WHERE alerts.id = (SELECT MAX(id) FROM alerts) LIMIT 1;";
    $FETCH_LATEST_ALERT_TIMESTAMP_SQL = "SET @max = (SELECT MAX(id) FROM alerts); SELECT timestamp FROM alerts WHERE id=@max;";

    $USER_NOT_REGISTERED_MESSAGE = "<div class=\"alert-warning\">用户不存在</div>";
    $USER_PASSWORD_WRONG_MESSAGE = "<div class=\"alert-danger\">密码或用户名错误</div>";

    $airTempSwitchValveLow = 10;
    $airTempSwitchValveHigh = 30;
    $airHumSwitchValveLow = 35;
    $airHumSwitchValveHigh = 70;
    $lightSwitchValveLow = 100;
    $airLightSwitchValveHigh = 500;
    $airLightSwitchValveLow = 100;
    $groundHumSwitchValveLow = 700;
    $groundHumSwitchValveHigh = 1000;
    //TODO: Real value needed.

    function purify_data(string $data) : string {
        $data = htmlspecialchars($data, ENT_COMPAT | ENT_HTML5);
        $data = htmlentities($data);
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }

    function run_query(PDO $connection, string $sql, array $args = array(), int $fetch_style = PDO::FETCH_ASSOC) {
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->fetch($fetch_style);
        return $result;
    }

    function run_query_fetch_all(PDO $connection, string $sql, array $args = array()) {
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function print_all_commits_each_item(string $timestamp, string $oddity, string $type, string $air_temp, string $air_hum, string $air_light, string $ground_hum) {
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

    function print_user_status(string $username, string $current_status) {
        if ($current_status == 'online') $text = 'Available';
        else $text = 'Offline';
        print "
            <div class=\"user-name\">$username</div>
            <div class=\"user-position $current_status\">$text</div>
        ";
    }

    function print_login_button() {
        print "
            <a href=\"pages-login.php\" class=\"dropdown-item\">
                <span class=\"icon mdi mdi-power\"></span> Login
            </a>
        ";
    }

    function print_logoff_button() {
        print "
            <a href=\"pages-logoff.php\" class=\"dropdown-item\">
                <span class=\"icon mdi mdi-power\"></span> Logout
            </a>
        ";
    }

    function print_settings_button() {
        print "
            <a href=\"#\" class=\"dropdown-item\">
                <span class=\"icon mdi mdi-settings\"></span> Settings
            </a>
        ";
    }

    function print_alert(int $alert_info, string $strong, string $caption) {
        switch ($alert_info) {
            case AlertInfo::GOOD:
                print " <div role=\"alert\" class=\"alert alert-success alert-icon alert-dismissible\">
                            <div class=\"icon\"><span class=\"mdi mdi-check\"></span></div>
                            
                            <div class=\"message\">
                                <button type=\"button\" data-dismiss=\"alert\" aria-label=\"忽略\" class=\"close\">
                                    <span aria-hidden=\"true\" class=\"mdi mdi-close\"></span>
                                </button>
                                <strong>$strong</strong> $caption
                            </div>
                        </div>";
                break;
            case AlertInfo::INFO:
                print " <div role=\"alert\" class=\"alert alert-primary alert-icon alert-dismissible\">
                            <div class=\"icon\"><span class=\"mdi mdi-info-outline\"></span></div>
                            <div class=\"message\">
                                <button type=\"button\" data-dismiss=\"alert\" aria-label=\"忽略\" class=\"close\">
                                    <span aria-hidden=\"true\" class=\"mdi mdi-close\"></span>
                                </button>
                                <strong>$strong</strong> $caption
                            </div>
                        </div>";
                break;
            case AlertInfo::WARNING:
                print " <div role=\"alert\" class=\"alert alert-warning alert-icon\">
                            <div class=\"icon\"><span class=\"mdi mdi-alert-triangle\"></span></div>
                            <div class=\"message\">
                                <strong>$strong</strong> $caption
                            </div>
                        </div>";
                break;
            case AlertInfo::DANGER:
                print " <div role=\"alert\" class=\"alert alert-danger alert-icon\">
                            <div class=\"icon\"><span class=\"mdi mdi-close-circle-o\"></span></div>
                            <div class=\"message\">
                                <strong>$strong</strong> $caption
                            </div>
                        </div>";
                break;
            
            default:
                throw new InvalidArgumentException("$alert_info");
                break;
        }
    }

    function set_session_logged_in(string $username) {
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $username;
    }

    function set_session_logged_off() {
        unset($_SESSION['is_logged_in']);
        unset($_SESSION['username']);
    }
    
    function redirect_to(string $address) {
        header('Content-type: text/html;charset=uft-8');
        header("Location: $address");
    }

    function xml_print_header(string $version = '1.0', string $encoding = 'UTF-8') {
        print "<?xml version=\"$version\" encoding=\"$encoding\" ?>\r\n";
    }

    function xml_print_root_begin() {
        print "<root>\r\n";
    }

    function xml_print_root_end() {
        print "</root>\r\n";
    }

    function xml_print_action(int $action_type) {
        print "<action>\r\n" . 
                "$action_type\r\n" .
              "</action>\r\n";
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
        public const OPEN_RELAY = 0;
        public const SHUTDOWN_RELAY = 1;
        public const REBOOT_SYSTEM = -2;
    }

    class RetransmitType {
        public const RETRANSMIT_NOT_REQUIRED = 0;
        public const RETRANSMIT_SUGGESTED = 1;
        public const RETRANSMIT_REQUIRED = 2;
    }
?>