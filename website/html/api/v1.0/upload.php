<?php
  /*
   * Copyright (c) 2018 Ningbo Foreign Language School
   * This part of program should be delivered with the whole project.
   * Partly use is not allowed.
   * Licensed under GPL-v3 Agreement
   */
  require '..\\..\\assets\\php\\shared_db.php';
  require '..\\..\\assets\\php\\shared_util.php';
  require '..\\..\\assets\\php\\shared_sql.php';
  require '..\\..\\assets\\php\\shared_xml.php';
  require '..\\..\\assets\\php\\shared_const.php';

  http_response_code(410);
  header('Reason: Deprecated API');
  exit();

  date_default_timezone_set('PRC');

  if ((!isset($_GET['air_temp'])) || (!isset($_GET['air_hum'])) || (!isset($_GET['air_light'])) || (!isset($_GET['ground_hum']))) {
    http_response_code(400);
    header('Reason: Values not set');
    exit();
  }

  if ((!is_numeric($_GET['air_temp'])) || (!is_numeric($_GET['air_hum'])) || (!is_numeric($_GET['air_light'])) || (!is_numeric($_GET['ground_hum']))) {
    http_response_code(400);
    header('Reason: Numbers expected');
    exit();
  }

  $air_temp = purify_data($_GET['air_temp']);
  $air_hum = purify_data($_GET['air_hum']);
  $air_light = purify_data($_GET['air_light']);
  $ground_hum = purify_data($_GET['ground_hum']);

  
  try {
    $db = DBConnectionSingleton::getInstance();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  catch (PDOException $e) {
    exit(500);
  }

  $actions = array();

  header('Content-Type: application/xml');

  try {
    run_query($db, INSERT_DATA_SQL, array((int)$air_temp, (int)$air_hum, (int)$air_light, (int)$ground_hum));
  }
  catch (PDOException $e) {
    http_response_code(406);
    exit();
  }

  // Air checks - temp
  if ($air_temp > airTempSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_TEMP, AlertType::HIGH));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::ON);
  }
  elseif ($air_temp < airTempSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_TEMP, AlertType::LOW));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::OFF);
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_TEMP, AlertType::OK));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::OFF);
  }
  //hum
  if ($air_hum > airHumSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_HUM, AlertType::HIGH));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::ON);
  }
  elseif ($air_hum < airHumSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_HUM, AlertType::LOW));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::OFF);
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_HUM, AlertType::OK));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, fanOnePin, RelayAction::OFF);
  }
  //light
  if ($air_light > airLightSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_LIGHT, AlertType::HIGH));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, skySheetInnerOpenPin, RelayAction::OFF);
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, skySheetInnerClosePin, RelayAction::ON);
  }
  elseif ($air_light < airLightSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_LIGHT, AlertType::LOW));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, skySheetInnerOpenPin, RelayAction::ON);
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, skySheetInnerClosePin, RelayAction::OFF);
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::AIR_LIGHT, AlertType::OK));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, skySheetInnerOpenPin, RelayAction::OFF);
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, skySheetInnerClosePin, RelayAction::OFF);
  }
  //Ground checks - hum
  if ($ground_hum > groundHumSwitchValveHigh) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::GROUND_HUM, AlertType::LOW));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, waterPumpPin, RelayAction::ON);
  }
  elseif ($ground_hum < groundHumSwitchValveLow) {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::GROUND_HUM, AlertType::HIGH));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, waterPumpPin, RelayAction::OFF);
  }
  else {
    run_query($db, INSERT_ALERT_SQL, array(AlertType::GROUND_HUM, AlertType::OK));
    $actions[count($actions)] = array(ActionType::RELAY_ACTION, waterPumpPin, RelayAction::OFF);
  }
  xml_print_header();
  xml_print_root_begin();
  xml_print_timestamp(date("H"), date("i"), date("s"));
  xml_print_actions_begin();
  foreach ($actions as $index => $array) {
    xml_print_action($array[0], $array[1], $array[2]);
  }
  xml_print_actions_end();
  xml_print_root_end();
?>
