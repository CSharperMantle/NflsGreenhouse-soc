<?php
  require '..\\php\\shared_db.php';
  require '..\\php\\shared_sql.php';

  $db = DBConnectionSingleton::getInstance();
?>

var App = (() => {
  'use strict';
  
  App.dashboard = () => {

    //Top tile widgets
    function sparklines() {

      var color1 = App.color.primary;
      var color2 = App.color.warning;
      var color3 = App.color.success;
      var color4 = App.color.danger;

      $('#all-commits-count-sparkline').sparkline([
        <?php
          $result = run_query_fetch_multi($db, FETCH_COMMITS_EACH_DAY_SQL);
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
          $result = run_query_fetch_multi($db, FETCH_ALERTS_EACH_DAY_SQL);
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

    //Row 1
    sparklines();

    //Row 2
    map();

  };

  

  return App;
})(App || {});
