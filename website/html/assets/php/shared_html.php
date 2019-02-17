<?php
    /*
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */

    const USER_NOT_REGISTERED_MESSAGE = "<div class=\"alert-warning\">用户不存在</div>";
    const USER_PASSWORD_WRONG_MESSAGE = "<div class=\"alert-danger\">密码或用户名错误</div>";

    function print_all_commits_each_item(string $timestamp, string $oddity, string $air_temp, string $air_hum, string $air_light, string $ground_hum) {
        print "<tr class=\"$oddity\">" .
                "<td>$timestamp</td>" .
                "<td>$air_temp</td>" .
                "<td>$air_hum</td>" .
                "<td>$air_light</td>" .
                "<td>$ground_hum</td>" .
            "</tr>";
    }

    function print_user_info() {
        if (isset($_SESSION['is_logged_in'])) {
            print_user_status($_SESSION['username'], 'online');
        } else {
            print_user_status('John Doe', 'offline');
        }
    }

    function print_user_name() {
        print isset($_SESSION['is_logged_in']) ? $_SESSION['username'] : 'John Doe';
    }

    function print_user_buttons() {
        if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] == true) {
            print_logoff_button();
        } else {
            print_login_button();
        }
    }

    function print_user_status(string $username, string $current_status) {
        if ($current_status == 'online') $text = '在线';
        else $text = '离线';
        print "<div class=\"user-name\">$username</div>" .
            "<div class=\"user-position $current_status\">$text</div>";
    }

    function print_login_button() {
        print "<a href=\"pages-login.php\" class=\"dropdown-item\">" .
                "<span class=\"icon mdi mdi-power\"></span> 登录" .
            "</a>";
    }

    function print_logoff_button() {
        print "<a href=\"pages-logoff.php\" class=\"dropdown-item\">" .
                "<span class=\"icon mdi mdi-power\"></span> 注销" .
            "</a>";
    }

    function print_settings_button() {
        print "<a href=\"pages-settings.php\" class=\"dropdown-item\">" .
                "<span class=\"icon mdi mdi-settings\"></span> 设置" .
            "</a>";
    }

    function print_alert(int $alert_info, string $strong, string $caption) {
        switch ($alert_info) {
            case AlertInfo::GOOD:
                print "<div role=\"alert\" class=\"alert alert-success alert-icon alert-dismissible\">" .
                            "<div class=\"icon\"><span class=\"mdi mdi-check\"></span></div>" .
                            
                            "<div class=\"message\">" .
                                "<button type=\"button\" data-dismiss=\"alert\" aria-label=\"忽略\" class=\"close\">" .
                                    "<span aria-hidden=\"true\" class=\"mdi mdi-close toggle-close\"></span>" .
                                "</button>" .
                                "<strong>$strong</strong> $caption" .
                            "</div>" .
                        "</div>";
                break;
            case AlertInfo::INFO:
                print "<div role=\"alert\" class=\"alert alert-primary alert-icon alert-dismissible\">" .
                            "<div class=\"icon\"><span class=\"mdi mdi-info-outline\"></span></div>" .
                            "<div class=\"message\">" .
                                "<button type=\"button\" data-dismiss=\"alert\" aria-label=\"忽略\" class=\"close\">" .
                                    "<span aria-hidden=\"true\" class=\"mdi mdi-close toggle-close\"></span>" .
                                "</button>" .
                                "<strong>$strong</strong> $caption" .
                            "</div>" .
                        "</div>";
                break;
            case AlertInfo::WARNING:
                print "<div role=\"alert\" class=\"alert alert-warning alert-icon\">" .
                            "<div class=\"icon\"><span class=\"mdi mdi-alert-triangle\"></span></div>" .
                            "<div class=\"message\">" .
                                "<strong>$strong</strong> $caption" .
                            "</div>" .
                        "</div>";
                break;
            case AlertInfo::DANGER:
                print "<div role=\"alert\" class=\"alert alert-danger alert-icon\">" .
                            "<div class=\"icon\"><span class=\"mdi mdi-close-circle-o\"></span></div>" .
                            "<div class=\"message\">" .
                                "<strong>$strong</strong> $caption" .
                            "</div>" .
                        "</div>";
                break;
            
            default:
                throw new InvalidArgumentException("$alert_info");
                break;
        }
    }

    function print_panel(string $caption, string $text, int $alert_info) {
        switch ($alert_info) {
            case AlertInfo::INFO:
                print "<div class=\"card card-border-color card-border-color-primary\">";
                break;
            case AlertInfo::GOOD:
                print "<div class=\"card card-border-color card-border-color-success\">";
                break;
            case AlertInfo::WARNING:
                print "<div class=\"card card-border-color card-border-color-warning\">";
                break;
            case AlertInfo::DANGER:
                print "<div class=\"card card-border-color card-border-color-danger\">";
                break;
        }
        print "<div class=\"card-header\">$caption</div>";
        print "<div class=\"card-body\">$text</div>";
        print "</div>";
    }

    function print_history_action(bool $is_latest, string $time, string $action, string $description) {
        print $is_latest ? "<li class=\"latest\">" : "<li>";
        print "<div class=\"user-timeline-date\">$time</div>";
        print "<div class=\"user-timeline-title\">$action</div>";
        print "<div class=\"user-timeline-description\">$description</div>";
        print "</li>";
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
?>