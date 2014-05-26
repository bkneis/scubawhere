<?php 
	$root =  $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/init.php");
	require_once($root."/engine/core/db/interface/trips.php");
	
	$act = count_active_trips_by_company($_SESSION['id']);
	$inact = count_inactive_trips_by_company($_SESSION['id']);
	
?>

<div class="block-title">Active Vs. Inactive Trips</div>

<canvas id="doughnut" height="200" width="200"></canvas>

<div id="pie-key">
	<span id="act-key"><?php echo $act; ?> active trips</span>
	<span id="inact-key"><?php echo $inact; ?> inactive trips</span>
	
</div>

<script>

	var pieData = [
			{
				value: <?php echo $act; ?>,
				color:"#4a9cff"
			},
			{
				value : <?php echo $inact; ?>,
				color : "#FF7163"
			}
		
		];
	
	var myPie = new Chart(document.getElementById("doughnut").getContext("2d")).Doughnut(pieData);
	
</script>