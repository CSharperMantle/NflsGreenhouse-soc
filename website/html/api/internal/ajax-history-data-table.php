<?php
    require '..\\..\\assets\\php\\shared_db.php';
    require '..\\..\\assets\\php\\shared_util.php';
    require '..\\..\\assets\\php\\shared_sql.php';
    require '..\\..\\assets\\php\\shared_html.php';

    try {
        $db = DBConnectionSingleton::getInstance();
    } catch (PDOException $e) {
        http_response_code(500);
        exit();
    }

    $all_commits = run_query_fetch_multi($db, FETCH_ALL_HISTORY_DATA_SQL);

    foreach ($all_commits as $key => $value) {
        $_air_temp = $value['air_temp'];
        $_air_light = $value['air_light'];
        $_air_hum = $value['air_hum'];
        $_ground_hum = $value['ground_hum'];
        $_timestamp = $value['timestamp'];
        $_oddity = (($key + 1) % 2) == 0 ? 'odd' : 'even';
        print_all_commits_each_item($_timestamp, $_oddity, $_air_temp, $_air_hum, $_ground_hum, $_air_light);
    }
?>