<?php 
	$root =  $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/init.php");
	require_once($root."/engine/core/db/interface/bookings.php");
?>

<div class="block-title">Past 4 Week's Bookings</div>

<canvas id="line-chart" height="160" width="400"></canvas>

<script>
	var data = {
		labels : [
			<?php for($i = 28; $i > 0; $i--){ ?>
				<?php 
					if($i > 1){
						echo '"'.$i.'", '; 
					}else{
						echo '"'.$i.'"'; 
					}
				?>
			<?php } ?>
		],
		datasets : [
			{
				fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,1)",
				pointColor : "rgba(151,187,205,1)",
				pointStrokeColor : "#fff",
				data : [
				<?php for($i = 28; $i > 0; $i--){ ?>
					<?php 
						if($i > 1){
							echo '"'.rand(1, 100).'", '; 
						}else{
							echo '"'.rand(1, 100).'"'; 
						}
					?>
				<?php } ?>
				]
			}
		]
	}
	
	var myLine = new Chart($("#line-chart").get(0).getContext("2d")).Line(data);
</script>