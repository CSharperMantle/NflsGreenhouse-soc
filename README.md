# Deploy notes
## Requirements
* Required `PHP7+`
* Required `session.auto_start=1`
* Required settings in Apache: `ErrorDocument 404 .../NflsWatering/website/html/pages-404.html`
## Deserted code
```php
var light_data = [
    <?php
        $air_light_query = run_query_fetch_all($db, FETCH_AIR_LIGHT_SQL);
        foreach ($air_light_query as $id => $array) {
            $_id = $array['id'];
            $_light = $array['air_light'];
            print "[$_id, $_light], \r\n";
        }
    ?>
];
	  
var gnd_hum_data = [
    <?php
        $ground_hum_query = run_query_fetch_all($db, FETCH_GROUND_HUM_SQL);
        foreach ($ground_hum_query as $id => $array) {
            $_id = $array['id'];
            $_hum = $array['ground_hum'];
            print "[$_id, $_hum], \r\n";
        }
    ?>
];
$('#spark2').sparkline([
      <?php
          $result = run_query_fetch_all($db, FETCH_ALERTS_EACH_DAY_SQL);
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
```html
<div class="row">
<div class="col"><a href="#" class="connection-item"><img src="assets/img/github.png" alt="Github"><span>GitHub</span></a></div>
<div class="col"><a href="#" class="connection-item"><img src="assets/img/bitbucket.png" alt="Bitbucket"><span>Bitbucket</span></a></div>
<div class="col"><a href="#" class="connection-item"><img src="assets/img/slack.png" alt="Slack"><span>Slack</span></a></div>
</div>
<div class="row">
    <div class="col"><a href="#" class="connection-item"><img src="assets/img/dribbble.png" alt="Dribbble"><span>Dribbble</span></a></div>
    <div class="col"><a href="#" class="connection-item"><img src="assets/img/mail_chimp.png" alt="Mail Chimp"><span>Mail
                Chimp</span></a></div>
    <div class="col"><a href="#" class="connection-item"><img src="assets/img/dropbox.png" alt="Dropbox"><span>Dropbox</span></a></div>
</div>
```
```js
//Top sales chart
function topSales(){
  var data = [
    { label: "Services", data: 33 },
    { label: "Standard Plans", data: 33 },
    { label: "Services", data: 33 }
  ];
  var color1 = App.color.success;
  var color2 = App.color.warning;
  var color3 = App.color.primary;
  $.plot('#top-sales', data, {
    series: {
      pie: {
        radius: 0.75,
        innerRadius: 0.58,
        show: true,
        highlight: {
          opacity: 0.1
        },
        label: {
          show: false
        }
      }
    },
    grid:{
      hoverable: true,
    },
    legend:{
      show: false
    },
    colors: [color1, color2, color3]
  });
  //Chart legend color setter
  $('[data-color="top-sales-color1"]').css({'background-color':color1});
  $('[data-color="top-sales-color2"]').css({'background-color':color2});
  $('[data-color="top-sales-color3"]').css({'background-color':color3});
}
//Calendar widget
function calendar(){
  var widget = $("#calendar-widget");
  var now  = new Date();
  var year = now.getFullYear();
  var month = now.getMonth();
  var events = [year + '-' + (month+1) + '-16', year + '-' + (month+1) + '-20'];
  function checkRows(datepicker){
    var dp = datepicker.dpDiv;
    var rows = $("tbody tr", dp).length;
    
    if( rows == 6 ){
      dp.addClass('ui-datepicker-6rows');
    }else{
      dp.removeClass('ui-datepicker-6rows');
    }
  }
  //Extend default datepicker to support afterShow event
  $.extend($.datepicker, {
    _updateDatepicker_original: $.datepicker._updateDatepicker,
    _updateDatepicker: function(inst) {
      this._updateDatepicker_original(inst);
      var afterShow = this._get(inst, 'afterShow');
      if (afterShow){
        afterShow.apply(inst, [inst]);
      }
    }
  });
  if (typeof jQuery.ui != 'undefined') {
    widget.datepicker({
      showOtherMonths: true,
      selectOtherMonths: true,
      beforeShowDay: function(date) {
        var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
        if($.inArray(y + '-' + (m+1) + '-' + d, events) != -1)  {
          return [true, 'has-events', 'This day has events!'];
        }else{
          return [true, "", ""];
        }
      },
      afterShow:function(o){
        //If datepicker has 6 rows add a class to the widget
        checkRows(o);
      }
    });
  }
}

//Row 4
topSales();
calendar();

function mainChart() {
  var color1 = App.color.primary;
  var color2 = tinycolor( App.color.primary ).lighten( 10 ).toString();
  var color3 = tinycolor( App.color.primary ).lighten( 15 ).toString();
  var color4 = tinycolor( App.color.primary ).lighten( 20 ).toString();
  var temp_data = [
    <?php
      $air_temp_query = run_query_fetch_all($db, FETCH_AIR_TEMP_SQL);
      foreach ($air_temp_query as $id => $array) {
        $_id = $array['id'];
        $_temp = $array['air_temp'];
        print "[$_id, $_temp], \r\n";
      }
    ?>
  ];
  var hum_data = [
    <?php
      $air_hum_query = run_query_fetch_all($db, FETCH_AIR_HUM_SQL);
      foreach ($air_hum_query as $id => $array) {
        $_id = $array['id'];
        $_hum = $array['air_hum'];
        print "[$_id, $_hum], \r\n";
      }
    ?>
  ];
  var plot_statistics = $.plot($("#main-chart"), [{
      data: hum_data, showLabels: true, label: "空气湿度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
  },{
    data: temp_data, showLabels: true, label: "空气温度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
  }],
  {
    series: {
      lines: {
        show: true,
        lineWidth: 0, 
        fill: true,
        fillColor: { colors: [{ opacity: 1 }, { opacity: 1 }] }
      },
      fillColor: "rgba(0, 0, 0, 1)",
      shadowSize: 0,
      curvedLines: {
        apply: true,
        active: true,
        monotonicFit: true
      }
    },
    legend:{
      show: false
    },
    grid: {
      show: true,
      margin: {
        top: 20,
        bottom: 0,
        left: 0,
        right: 0,
      },
      labelMargin: 0,
      minBorderMargin: 0,
      axisMargin: 0,
      tickColor: "rgba(0,0,0,0.05)",
      borderWidth: 0,
      hoverable: true,
      clickable: true
    },
    tooltip:{
      show: true,
      cssClass: "tooltip-chart",
      content: "<div class='content-chart'> <span> %s </span> <div class='label'> <div class='label-x'> %x.0 </div> - <div class='label-y'> %y.0 </div> </div></div>",
     defaultTheme: false
   },
   colors: [color1, color2, color3, color4],
   xaxis: {
     tickFormatter: function(){
       return '';
     },
     autoscaleMargin: 0,
     ticks: 11,
     tickDecimals: 0,
     tickLength: 0
   },
   yaxis: {
     tickFormatter: function(){
       return '';
     },
     //autoscaleMargin: 0.01,
     ticks: 4,
     tickDecimals: 0
   }
 });

 widget_tooltipPosition('main-chart', 60);

 //Chart legend color setter
 $('[data-color="main-chart-color1"]').css({'background-color':color1});
 $('[data-color="main-chart-color2"]').css({'background-color':color2});
 $('[data-color="main-chart-color3"]').css({'background-color':color3});
 $('[data-color="main-chart-color4"]').css({'background-color':color4});
}

```
```html
<div class="col-md-12">
          <div class="widget widget-fullwidth be-loading">
            <div class="widget-head">

              <div class="tools">
                <div class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle"><span class="icon mdi mdi-more-vert d-inline-block d-md-none"></span></a>
                  <div role="menu" class="dropdown-menu"><a href="#" class="dropdown-item">周</a><a href="#" class="dropdown-item">月</a><a
                      href="#" class="dropdown-item">年</a>
                    <div class="dropdown-divider"></div><a href="#" class="dropdown-item">今天</a>
                  </div>
                </div><span class="icon mdi mdi-chevron-down"></span><span class="icon toggle-loading mdi mdi-refresh-sync"></span><span
                  class="icon mdi mdi-close"></span>
              </div>

              <div class="button-toolbar d-none d-md-block">
                <div class="btn-group">
                  <button type="button" class="btn btn-secondary">一周</button>
                  <button type="button" class="btn btn-secondary active">一月</button>
                  <button type="button" class="btn btn-secondary">一年</button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-secondary">今天</button>
                </div>
              </div>

              <span name="history_data" class="title">历史数据</span>
            </div>
            <div class="widget-chart-container">
              <div class="widget-chart-info">
                <ul class="chart-legend-horizontal">
                  <li><span data-color="main-chart-color1"></span> 空气湿度</li>
                  <li><span data-color="main-chart-color2"></span> 空气温度</li>
                </ul>
              </div>
              <div class="widget-counter-group widget-counter-group-right">
                <div class="counter counter-big">
                  <div class="value">
                    <?= $air_temp . '°C'; ?>
                  </div>
                  <div class="desc">空气温度</div>
                </div>
                <div class="counter counter-big">
                  <div class="value">
                    <?= $air_hum . '%'; ?>
                  </div>
                  <div class="desc">空气湿度</div>
                </div>
                <div class="counter counter-big">
                  <div class="value">
                    <?= $air_light; ?>
                  </div>
                  <div class="desc">光强度</div>
                </div>
                <div class="counter counter-big">
                  <div class="value">
                    <?= $ground_hum; ?>
                  </div>
                  <div class="desc">地面湿度</div>
                </div>
              </div>
              <div id="main-chart" style="height: 260px;"></div>
            </div>
            <div class="be-spinner">
              <svg width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                <circle fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30" class="circle"></circle>
              </svg>
            </div>
          </div>
        </div>
```
```html
<span class="icon mdi mdi-download"></span>
                <a href="#" role="button" data-toggle="dropdown" class="dropdown-toggle">
                  <span class="icon mdi mdi-more-vert"></span>
                </a>
                <div role="menu" class="dropdown-menu">
                  <a href="#" class="dropdown-item">Action</a>
                  <a href="#" class="dropdown-item">Another action</a>
                  <a href="#" class="dropdown-item">Something else here</a>
                  <div class="dropdown-divider"></div>
                  <a href="#" class="dropdown-item">Separated link</a>
                </div>
```