# Deploy notes
__PHP 7__ needed.
Must set __session.auto_start__ to __1__
Deserted code:
```php
var light_data = [
    <?php
        $air_light_query = run_query_fetch_all($db, $FETCH_AIR_LIGHT_SQL);
        foreach ($air_light_query as $id => $array) {
            $_id = $array['id'];
            $_light = $array['air_light'];
            print "[$_id, $_light], \r\n";
        }
    ?>
];
	  
var gnd_hum_data = [
    <?php
        $ground_hum_query = run_query_fetch_all($db, $FETCH_GROUND_HUM_SQL);
        foreach ($ground_hum_query as $id => $array) {
            $_id = $array['id'];
            $_hum = $array['ground_hum'];
            print "[$_id, $_hum], \r\n";
        }
    ?>
];
$('#spark2').sparkline([
      <?php
          $result = run_query_fetch_all($db, $FETCH_ALERTS_EACH_DAY_SQL);
          foreach ($result as $key => $array) {
            print $array['count'] . ',';
          }
      ?>
    ], {
        width: '85',
        height: '35',
        lineColor: color4,
        highlightSpotColor: color4,
        highlightLineColor: color4,
        fillColor: false,
        spotColor: false,
        minSpotColor: false,
        maxSpotColor: false,
        lineWidth: 1.15
      });
```
```js
var plot_statistics = $.plot($("#main-chart"), [{
        data: hum_data, showLabels: true, label: "空气湿度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      },{
        data: temp_data, showLabels: true, label: "空气温度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      },{
        data: data3, showLabels: true, label: "空气亮度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      },{
        data: data4, showLabels: true, label: "地面湿度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      }],
```
```html
<tr class="odd gradeX">
    <td>Trident</td>
    <td>
        Internet
        Explorer 4.0
    </td>
    <td>Win 95+</td>
    <td class="center"> 4</td>
    <td class="center"></td>
</tr>
<tr class="even gradeC">
    <td>Trident</td>
    <td>
        Internet
        Explorer 5.0
    </td>
    <td>Win 95+</td>
    <td class="center">5</td>
    <td class="center">C</td>
</tr>
```