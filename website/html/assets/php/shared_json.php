<?php
    /**
     * Copyright (c) 2018 Ningbo Foreign Language School
     * This part of program should be delivered with the whole project.
     * Partly use is not allowed.
     * Licensed under GPL-v3 Agreement
     */
    function create_action_array(int $action_type, int $target_id, string $param) {
        return array('action_type' => $action_type, 'target_id' => $target_id, 'param' => $param);
    }
?>