<?php
    require '..\\..\\assets\\php\\shared_db.php';
    require '..\\..\\assets\\php\\shared_sql.php';
    require '..\\..\\assets\\php\\shared_util.php';
    require '..\\..\\assets\\php\\shared_ajax.php';

    try {
        $db = DBConnectionSingleton::getInstance();
    } catch (PDOException $e) {
        terminate(500);
    }

    if (!isset($_POST['data_type'])) {
        terminate(400);
    }

    $data_type = $_POST['data_type'];

    if (!is_numeric($data_type)) {
        terminate(400);
    }

    switch ($data_type) {
        case AjaxSparklineDataType::COMMITS_PER_DAY:
            $result = run_query_fetch_multi($db, FETCH_COMMITS_EACH_DAY_SQL, array(), PDO::FETCH_NUM);
            print json_encode($result);
            break;
        case AjaxSparklineDataType::ALERTS_PER_DAY:
            $result = run_query_fetch_multi($db, FETCH_ALERTS_EACH_DAY_SQL, array(), PDO::FETCH_NUM);
            print json_encode($result);
            break;
        default:
            terminate(400);
            break;
    }
?>