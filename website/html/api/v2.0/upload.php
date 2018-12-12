<?php
    require '..\\..\\assets\\php\\shared_db.php';
    
    try {
        $db = DBConnectionSingleton::getInstance();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        http_response_code(500);
        exit();
    }
    $get_json_param = $_GET['data'];
    $json = json_decode($get_json_param);
?>