<?php
  /*
  GET Params:
    air_temp 空气温度
    air_hum 空气湿度
    air_light 空气亮度
    ground_hum 地面湿度
  */
  require 'assets\\php\\shared.php';

  $air_temp = purify_data($_GET['air_temp']);
  $air_hum = purify_data($_GET['air_hum']);
  $air_light = purify_data($_GET['air_light']);
  $ground_hum = purify_data($_GET['ground_hum']);

  try {
    $db = new PDO($dbdsn, $username, $passwd);
    run_query($db, $INSERT_DATA_SQL, array($air_temp, $air_hum, $air_light, $ground_hum));
    // Air checks - temp
    if ($air_temp > $airTempSwitchValveHigh) {
      run_query($db, $INSERT_ALERT_SQL, array("Air temperature too high"));
    }
    if ($air_temp > $airTempSwitchValveLow) {
      run_query($db, $INSERT_ALERT_SQL, array("Air temperature too low"));
    }
    //hum
    if ($air_hum > $airHumSwitchValveHigh) {
      run_query($db, $INSERT_ALERT_SQL, array("Humidity of the air too high"));
    }
    if ($air_hum < $airHumSwitchValveLow) {
      run_query($db, $INSERT_ALERT_SQL, array("Humidity of the air too low"));
    }
    //light
    if ($air_light > $airLightSwitchValveHigh) {
      run_query($db, $INSERT_ALERT_SQL, array("Too bright"));
    }
    if ($air_light < $airLightSwitchValveLow) {
      run_query($db, $INSERT_ALERT_SQL, array("Too dark"));
    }
    //Ground checks - hum
    if ($ground_hum > $groundHumSwitchValveHigh) {
      run_query($db, $INSERT_ALERT_SQL, array("Humidity of the soid too high"));
    }
    if ($ground_hum < $groundHumSwitchValveLow) {
      run_query($db, $INSERT_ALERT_SQL, array("Humidity of the soid too low"));
    }
  }
  catch (PDOException $e) {
    exit(500);
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ningbo Foreign Language School - Uploader</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <?php
    print '<div>air_temp: ' . $air_temp . '</div>';
    print '<br/>';
    print '<div>air_hum: ' . $air_hum . '</div>';
    print '<br/>';
    print '<div>air_light: ' . $air_light . '</div>';
    print '<br/>';
    print '<div>ground_hum: ' . $ground_hum . '</div>';
    print '<br/>';
  ?>
</body>
</html>