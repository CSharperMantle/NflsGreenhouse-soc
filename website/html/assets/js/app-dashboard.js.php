<?php
  require '..\\php\\shared_db.php';
  require '..\\php\\shared_sql.php';

  $db = DBConnectionSingleton::getInstance();
?>

var App = (function () {
  'use strict';
  
  App.dashboard = function( ){

    //Counter
    function counter(){

      $('[data-toggle="counter"]').each(function(i, e){
        var _el = $(this);
        var prefix = '';
        var suffix = '';
        var start = 0;
        var end = 0;
        var decimals = 0;
        var duration = 2.5;

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

    //CounterUp Init
    counter();

    //Loader show
    toggleLoader();

    //Row 1
    sparklines();

    //Row 5
    map();

  };

  return App;
})(App || {});
