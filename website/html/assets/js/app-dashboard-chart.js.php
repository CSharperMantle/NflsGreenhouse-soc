<?php
	require '..\\php\\shared_db.php';
  	require '..\\php\\shared_sql.php';

  	$db = DBConnectionSingleton::getInstance();

  	$air_temp_query = run_query_fetch_multi($db, FETCH_AIR_TEMP_SQL);
  	$air_hum_query = run_query_fetch_multi($db, FETCH_AIR_HUM_SQL);
  	$air_light_query = run_query_fetch_multi($db, FETCH_AIR_LIGHT_SQL);
  	$ground_hum_query = run_query_fetch_multi($db, FETCH_GROUND_HUM_SQL);
?>
var App = (() => {
	'use strict';
	App.ChartJs = () => {
 		function airTempLineChart() {
 			var appPrimaryColor = tinycolor( App.color.primary );
			var appPrimaryColorLighten = tinycolor( App.color.primary ).lighten( 22 );
 			var ctx = document.getElementById('air-temp-line-chart-canvas');
 			var lineChartData = {
	      		labels: [
	      		<?php
	      			$temp_id = 1;
	      			foreach ($air_temp_query as $id => $array) {
	      				print "'$temp_id',";
	      				$temp_id++;
	      			}
	      		?>],
	      		tooltips: {
	      			enabled: true,
	      			position: 'average'
	      		},
	      		datasets: [{
	      		  	label: "空气温度",
	      		  	borderColor: appPrimaryColor.toString(),
	      		  	backgroundColor: appPrimaryColorLighten.toString(),
	      		  	data: [
	      		  	<?php
	      		  		foreach ($air_temp_query as $id => $array) {
	      		  			$_temp = $array['air_temp'];
	      		  			print "$_temp,";
	      		  		}
	      		  	?>
	      		  	]
	      		}]
	    	};
	    	var line = new Chart(ctx, {
				type: 'line',
				data: lineChartData
	    	});
 		}

 		function airHumLineChart() {
			var appSuccessColor = tinycolor( App.color.success );
			var appSuccessColorLighten = tinycolor( App.color.success ).lighten( 22 );
 			var ctx = document.getElementById('air-hum-line-chart-canvas');
 			var lineChartData = {
	      		labels: [
	      		<?php
	      			$hum_id = 1;
	      			foreach ($air_hum_query as $id => $array) {
	      				print "'$hum_id',";
	      				$hum_id++;
	      			}
	      		?>],
	      		tooltips: {
	      			enabled: true,
	      			position: 'average'
	      		},
	      		datasets: [{
	      		  	label: "空气湿度",
	      		  	borderColor: appSuccessColor.toString(),
	      		  	backgroundColor: appSuccessColorLighten.toString(),
	      		  	data: [
	      		  	<?php
	      		  		foreach ($air_hum_query as $id => $array) {
	      		  			$_hum = $array['air_hum'];
	      		  			print "$_hum,";
	      		  		}
	      		  	?>
	      		  	]
	      		}]
	    	};
	    	var line = new Chart(ctx, {
				type: 'line',
				data: lineChartData
	    	});
 		}

 		airTempLineChart();
		airHumLineChart();
	}

	return App;
})(App || {});