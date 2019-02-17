<?php
    /*
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */

    function purify_data(string $data) : string {
        $data = htmlspecialchars($data, ENT_COMPAT | ENT_HTML5);
        $data = htmlentities($data);
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }

    function terminate(int $code) {
        http_response_code($code);
        exit();
    }
?>