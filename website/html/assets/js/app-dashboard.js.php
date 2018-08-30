<?php
  require '../php/shared.php';

  $db = new PDO($dbdsn, $username, $passwd);
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
            parent.removeClass('be-loading-active');
          }, 3000);
        }
      });
    }

    //Top tile widgets
    function sparklines(){

      var color1 = App.color.primary;
      var color2 = App.color.warning;
      var color3 = App.color.success;
      var color4 = App.color.danger;

      $('#spark1').sparkline([0,5,3,7,5,10,3,6,5,10], { 
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
	  
	  $('#spark2').sparkline([0,5,3,7,5,10,3,6,5,10], { 
        width: '85',
        height: '35',
        lineColor: color2,
        highlightSpotColor: color2,
        highlightLineColor: color2,
        fillColor: false,
        spotColor: false,
        minSpotColor: false,
        maxSpotColor: false,
        lineWidth: 1.15
      });

      /*$("#spark2").sparkline([5,8,7,10,9,10,8,6,4,6,8,7,6,8], { 
        type: 'bar', 
        width: '85',
        height: '35',
        barWidth: 3,
        barSpacing: 3,
        chartRangeMin: 0,
        barColor: color2 
      });*/

      $('#spark3').sparkline([2,3,4,5,4,3,2,3,4,5,6,5,4,3,4,5,6,5,4,4,5], { 
        type: 'discrete', 
        width: '85',
        height: '35',
        lineHeight: 20,
        lineColor: color3,
        xwidth: 18 
      });

      $('#spark4').sparkline([2,5,3,7,5,10,3,6,5,7], { 
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
    }

    //Main chart
    function mainChart(){

      var color1 = App.color.primary;
      var color2 = tinycolor( App.color.primary ).lighten( 10 ).toString();
      var color3 = tinycolor( App.color.primary ).lighten( 15 ).toString();
	    var color4 = tinycolor( App.color.primary ).lighten( 20 ).toString();

      var temp_data = [
        <?php
          $air_temp_query = run_query_fetch_all($db, $FETCH_AIR_TEMP_SQL);
          foreach ($air_temp_query as $id => $array) {
            $_id = $array['id'];
            $_temp = $array['air_temp'];
            print "[$_id, $_temp], \r\n";
          }
        ?>
      ];

      var hum_data = [
        <?php
          $air_hum_query = run_query_fetch_all($db, $FETCH_AIR_HUM_SQL);
          foreach ($air_hum_query as $id => $array) {
            $_id = $array['id'];
            $_hum = $array['air_hum'];
            print "[$_id, $_hum], \r\n";
          }
        ?>
      ];

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

      var plot_statistics = $.plot($("#main-chart"), [{
        data: hum_data, showLabels: true, label: "空气湿度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      },{
        data: temp_data, showLabels: true, label: "空气温度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      }/*,{
        data: data3, showLabels: true, label: "空气亮度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      },{
        data: data4, showLabels: true, label: "地面湿度", labelPlacement: "below", canvasRender: true, cColor: "#FFFFFF" 
      }*/],
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
    mainChart();

	  //Row 4
    topSales();
    calendar();

    //Row 5
    map();

  };

  return App;
})(App || {});
