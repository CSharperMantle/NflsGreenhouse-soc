<?php
    /*
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */

    const host = 'localhost';
    const port = 3306;
    const dbname = 'plant_data';
    const passwd = 'plant_client';
    const username = 'plant_client';
    const dbdsn = "mysql:host=" . host . ";port=" . port . ";dbname=" . dbname;

    function run_query(PDO $connection, string $sql, array $args = array()) {
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
    }

    function run_query_fetch(PDO $connection, string $sql, array $args = array(), int $fetch_style = PDO::FETCH_ASSOC) {
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->fetch($fetch_style);
        return $result;
    }

    function run_query_fetch_multi(PDO $connection, string $sql, array $args = array(), int $fetch_style = PDO::FETCH_ASSOC) {
        $stmt = $connection->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->fetchAll($fetch_style);
        return $result;
    }

    class DBConnectionSingleton {
        private static $_pdo_instance;
        
        public static function getInstance() {
            if (!(DBConnectionSingleton::$_pdo_instance instanceof PDO)) {
                DBConnectionSingleton::$_pdo_instance = new PDO(dbdsn, username, passwd);
            }
            return DBConnectionSingleton::$_pdo_instance;
        }

        private function __constructor() {
        }
        private function __clone() {
            throw new RuntimeException('DBConnectionSingleton cannot be cloned');
        }
    }
?>