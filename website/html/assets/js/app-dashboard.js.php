<?php
  require '../php/shared.php';

  $db = new PDO(dbdsn, username, passwd);
?>

var App = (function () {
  'use strict';
  
  App.dashboard = function( ){

    //Counter
    function counter(){

      $('[data-toggle="counter"]').each(function(i, e){
        var _el       = $(this);
        var prefix    = '';
        var suffix    = '';
        var start     = 0;
        var end       = 0;
        var decimals  = 0;
        var duration  = 2.5;

        if( _el.data('prefix') ){ prefix = _el.data('prefix'); }

        if( _el.data('suffix') ){ suffix = _el.data('suffix'); }

        if( _el.data('start') ){ start = _el.data('start'); }

        if( _el.data('end') ){ end = _el.data('end'); }

        if( _el.data('decimals') ){ decimals = _el.data('decimals'); }

        if( _el.data('duration') ){ duration = _el.data('duration'); }

        var count = new CountUp(_el.get(0), start, end, decimals, duration, { 
          suffix: suffix,
          prefix: prefix,
        });

        count.start();
      });
    }

    //Show loading class toggle
    function toggleLoader(){
      $('.toggle-loading').on('click',function(){
        var parent = $(this).parents('.widget, .panel');
        if( parent.length ){
          parent.addClass('be-loading-active');

          setTimeout(function(){
            this.document.location.reload();
            parent.removeClass('be-loading-active');
          }, 300);
        }
      });
    }

    //Top tile widgets
    function sparklines() {

      var color1 = App.color.primary;
      var color2 = App.color.warning;
      var color3 = App.color.success;
      var color4 = App.color.danger;

      $('#all-commits-count-sparkline').sparkline([
        <?php
          $result = run_query_fetch_all($db, FETCH_COMMITS_EACH_DAY_SQL);
          foreach ($result as $key => $array) {
            print $array['count'] . ',';
          }
        ?>
      ], { 
        width: '85',
        height: '35',
        lineColor: color1,
        highlightSpotColor: color1,
        highlightLineColor: color1,
        fillColor: false,
        spotColor: false,
        minSpotColor: false,
        maxSpotColor: false,
        lineWidth: 1.15
      });
	  
    $('#all-alerts-count-sparkline').sparkline([
      <?php
          $result = run_query_fetch_all($db, FETCH_ALERTS_EACH_DAY_SQL);
          foreach ($result as $key => $array) {
            print $array['count'] . ',';
          }
      ?>
    ], {
        type: 'bar', 
        width: '85',
        height: '35',
        barWidth: 4,
        barSpacing: 3,
        chartRangeMin: 0,
        barColor: color3,
      });
    }

    //Air temp widget
    function widget_air_temp_line_chart() {
      var color1 = App.color.primary;
      var plot_statistics = $.plot($("#air-temp-line-chart"), [{
        data: [
          <?php
            $air_temp_query = run_query_fetch_all($db, FETCH_AIR_TEMP_SQL);
            foreach ($air_temp_query as $id => $array) {
              $_id = $array['id'];
              $_temp = $array['air_temp'];
              print "[$_id, $_temp], \r\n";
            }
          ?>
        ],
        label: "空气温度"
      }], {
        series: {
          lines: {
            show: true,
            lineWidth: 2, 
            fill: true,
            fillColor: {
              colors: [{
                opacity: 0.35
              }, {
                opacity: 0.35
              }
              ]
            } 
          },
          points: {
            show: true
          },
          shadowSize: 1
        },
        legend:{
          show: false
        },
        grid: {
          margin: {
            left: -8,
            right: -8,
            top: 0,
            bottom: 0
          },
          show: false,
          labelMargin: 15,
          axisMargin: 500,
          hoverable: true,
          clickable: true,
          tickColor: "rgba(0,0,0,0.15)",
          borderWidth: 0
        },
        tooltip:{
          show: true,
          cssClass: "tooltip-chart",
          content: "<div class='content-chart'> <div class='label'> <div class='label-x'> %x.0 </div> - <div class='label-y'> %y.0 </div> </div></div>",
          defaultTheme: false
        },
        colors: [color1],
        xaxis: {
          ticks: 11,
          tickDecimals: 0
        },
        yaxis: {
          autoscaleMargin: 0.5,
          ticks: 4,
          tickDecimals: 0
        }
      });

      widget_tooltipPosition('air-temp-line-chart', 45);
    }

    //Air hum widget
    function widget_air_hum_line_chart() {
      var color1 = App.color.warning;
      var plot_statistics = $.plot($("#air-hum-line-chart"), [{
        data: [
          <?php
            $air_hum_query = run_query_fetch_all($db, FETCH_AIR_HUM_SQL);
            foreach ($air_hum_query as $id => $array) {
              $_id = $array['id'];
              $_hum = $array['air_hum'];
              print "[$_id, $_hum], \r\n";
            }
          ?>
        ],
        label: "空气湿度"
      }], {
        series: {
          lines: {
            show: true,
            lineWidth: 2, 
            fill: true,
            fillColor: {
              colors: [{
                opacity: 0.35
              }, {
                opacity: 0.35
              }
              ]
            } 
          },
          points: {
            show: true
          },
          shadowSize: 1
        },
        legend:{
          show: false
        },
        grid: {
          margin: {
            left: -8,
            right: -8,
            top: 0,
            bottom: 0
          },
          show: false,
          labelMargin: 15,
          axisMargin: 500,
          hoverable: true,
          clickable: true,
          tickColor: "rgba(0,0,0,0.15)",
          borderWidth: 0
        },
        tooltip:{
          show: true,
          cssClass: "tooltip-chart",
          content: "<div class='content-chart'> <div class='label'> <div class='label-x'> %x.0 </div> - <div class='label-y'> %y.0 </div> </div></div>",
          defaultTheme: false
        },
        colors: [color1],
        xaxis: {
          ticks: 11,
          tickDecimals: 0
        },
        yaxis: {
          autoscaleMargin: 0.5,
          ticks: 4,
          tickDecimals: 0
        }
      });

      widget_tooltipPosition('air-hum-line-chart', 60);
    }

    //Ground hum widget
    function widget_ground_hum_line_chart() {
      var color1 = App.color.danger;
      var plot_statistics = $.plot($("#ground-hum-line-chart"), [{
        data: [
          <?php
            $ground_hum_query = run_query_fetch_all($db, FETCH_GROUND_HUM_SQL);
            foreach ($ground_hum_query as $id => $array) {
              $_id = $array['id'];
              $_hum = $array['ground_hum'];
              print "[$_id, $_hum], \r\n";
            }
          ?>
        ],
        label: "空气湿度"
      }], {
        series: {
          lines: {
            show: true,
            lineWidth: 2, 
            fill: true,
            fillColor: {
              colors: [{
                opacity: 0.35
              }, {
                opacity: 0.35
              }
              ]
            } 
          },
          points: {
            show: true
          },
          shadowSize: 1
        },
        legend:{
          show: false
        },
        grid: {
          margin: {
            left: -8,
            right: -8,
            top: 0,
            bottom: 0
          },
          show: false,
          labelMargin: 15,
          axisMargin: 500,
          hoverable: true,
          clickable: true,
          tickColor: "rgba(0,0,0,0.15)",
          borderWidth: 0
        },
        tooltip:{
          show: true,
          cssClass: "tooltip-chart",
          content: "<div class='content-chart'> <div class='label'> <div class='label-x'> %x.0 </div> - <div class='label-y'> %y.0 </div> </div></div>",
          defaultTheme: false
        },
        colors: [color1],
        xaxis: {
          ticks: 11,
          tickDecimals: 0
        },
        yaxis: {
          autoscaleMargin: 0.5,
          ticks: 4,
          tickDecimals: 0
        }
      });

      widget_tooltipPosition('ground-hum-line-chart', 75);
    }

    //Map widget
    function map(){

      var color1 = tinycolor( App.color.primary ).lighten( 15 ).toHexString();
      var color2 = tinycolor( App.color.primary ).lighten( 8 ).toHexString();
      var color3 = tinycolor( App.color.primary ).toHexString();

      //Highlight data
      var data = {
        "ru": "14",
        "us": "14",
        "ca": "10",
        "br": "10",
        "au": "11",
        "uk": "3",
        "cn": "12"
      };

      $('#map-widget').vectorMap({
        map: 'world_en',
        backgroundColor: null,
        color: color1,
        hoverOpacity: 0.7,
        selectedColor: color2,
        enableZoom: true,
        showTooltip: true,
        values: data,
        scaleColors: [color1, color2],
        normalizeFunction: 'polynomial'
      });
    }

    //Positioning tooltip
    function widget_tooltipPosition(id, top){
      $('#'+id).bind("plothover", function (event, pos, item) {
        var widthToolTip = $('.tooltip-chart').width();
        if(item){
          $(".tooltip-chart")
            .css({top: item.pageY - top, left: item.pageX - (widthToolTip / 2)})
            .fadeIn(200);
        }else{
          $(".tooltip-chart").hide();
        }
      });
    }

    //CounterUp Init
    counter();

    //Loader show
    toggleLoader();

    //Row 1
    sparklines();

	  //Row 2
    widget_air_temp_line_chart();
    widget_air_hum_line_chart();
    widget_ground_hum_line_chart();

    //Row 5
    map();

  };

  return App;
})(App || {});
