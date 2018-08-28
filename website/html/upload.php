<?php
  /*
  GET Params:
    air_temp 空气温度
    air_hum 空气湿度
    air_light 空气亮度
    ground_hum 地面湿度
  */
  require 'scripts\\shared.php';

  $air_temp = purifyData($_GET['air_temp']);
  $air_hum = purifyData($_GET['air_hum']);
  $air_light = purifyData($_GET['air_light']);
  $ground_hum = purifyData($_GET['ground_hum']);

  try {
    $db = new PDO($dbdsn, $username, $passwd);
    $statement = $db->prepare($INSERT_SQL);
    $statement->execute(array($air_temp, $air_hum, $air_light, $ground_hum));
  }
  catch (PDOException $e) {
    exit(500);
  }
  // array($air_temp, $air_hum, $air_light, $ground_hum)
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Result</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <?php
    print "<div>air_temp: " . $air_temp . "</div>";
    print "<br />";
    print "<div>air_hum: " . $air_hum . "</div>";
    print "<br />";
    print "<div>air_light: " . $air_light . "</div>";
    print "<br />";
    print "<div>ground_hum: " . $ground_hum . "</div>";
    print "<br />";
  ?>
</body>
</html>