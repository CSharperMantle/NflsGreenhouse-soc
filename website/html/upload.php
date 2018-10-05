<?php
  /**
   * Copyright (c) 2018 Ningbo Foreign Language School
   * This part of program should be delivered with the whole project.
   * Partly use is not allowed.
   * Licensed under GPL-v3 Agreement
   */
  require 'assets\\php\\shared.php';

  if ((!isset($_GET['air_temp'])) || (!isset($_GET['air_hum'])) || (!isset($_GET['air_light'])) || (!isset($_GET['ground_hum']))) {
    http_response_code(400);
    exit();
  }

  $air_temp = purify_data($_GET['air_temp']);
  $air_hum = purify_data($_GET['air_hum']);
  $air_light = purify_data($_GET['air_light']);
  $ground_hum = purify_data($_GET['ground_hum']);

  try {
    $db = new PDO(dbdsn, username, passwd);
  }
  catch (PDOException $e) {
    exit(500);
  }

  $actions = array();

  run_query($db, INSERT_DATA_SQL, array($air_temp, $air_hum, $air_light, $ground_hum));
  // Air checks - temp
  if ($air_temp > airTempSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_TEMP, AlertType::HIGH));
    $actions[count($actions) + 1] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::ON);
    $actions[count($actions) + 1] = array(ActionType::RELAY_ACTION, fanTwoPin, RelayAction::ON);
  }
  elseif ($air_temp < airTempSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_TEMP, AlertType::LOW));
    $actions[count($actions) + 1] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::OFF);
    $actions[count($actions) + 1] = array(ActionType::RELAY_ACTION, fanTwoPin, RelayAction::OFF);
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_TEMP, AlertType::OK));
  }
  //hum
  if ($air_hum > airHumSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_HUM, AlertType::HIGH));
  }
  elseif ($air_hum < airHumSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_HUM, AlertType::LOW));
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_HUM, AlertType::OK));
  }
  //light
  if ($air_light > airLightSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_LIGHT, AlertType::HIGH));
  }
  elseif ($air_light < airLightSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_LIGHT, AlertType::LOW));
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_LIGHT, AlertType::OK));
  }
  //Ground checks - hum
  if ($ground_hum > groundHumSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::GROUND_HUM, AlertType::LOW));
  }
  elseif ($ground_hum < groundHumSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::GROUND_HUM, AlertType::HIGH));
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::GROUND_HUM, AlertType::OK));
  }
?>
