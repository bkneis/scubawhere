<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/places.php");
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/tickets.php");
	
	$company_id = $_SESSION['id'];
	
	//get all companies trips in array
	$allTrips = get_inactive_trips_by_company($company_id);
	
	$numTrips = count_inactive_trips_by_company($company_id);
?>
<?php if($numTrips > 0){ ?>
<script>
	$(document).ready(function(){
	
	/* create new array for trips */
	/* ********************************************************* */
	var inactiveTrips = [ <?php foreach($allTrips as $trip){ echo '"'.$trip['name'].'", '; }?> ];
	var inactiveTripsID = [ <?php foreach($allTrips as $trip){ echo '"'.$trip['id'].'", '; }?> ];
	 
	
	
	
	//set trip to first inactive trip in array default
	$('#trip').text( inactiveTrips[0] );
		
		var arrPosition = 0;
		var arrLength = inactiveTrips.length - 1;
		
		$("#activate-wrap").html('<img src="img/loading.gif">').load("tabs/activate-trip/do.php?id=" + inactiveTripsID[arrPosition]);
		
		//click function to switch forward a trip
		$("#switch-forward").click(function(){
			if(arrPosition == arrLength){
				arrPosition = 0;
			}else{
				arrPosition++;
			}
			
			$('#trip').text( inactiveTrips[arrPosition] );
			$("#activate-wrap").html('<img src="img/loading.gif">').load("tabs/activate-trip/do.php?id=" + inactiveTripsID[arrPosition]);
		});
		
		//click function to switch backward a month
		$("#switch-back").click(function(){
			if(arrPosition == 0){
				arrPosition = arrLength;
			}else{
				arrPosition--;
			}
			
			$('#trip').text( inactiveTrips[arrPosition] );
			$("#activate-wrap").html('<img src="img/loading.gif">').load("tabs/activate-trip/do.php?id=" + inactiveTripsID[arrPosition]);
		});
		
		
		
		$('#activate-wrap').ajaxForm(function(){
             //success modal appears!
            $( "#modal-overlay:hidden:first" ).fadeIn( "slow" );
            $( "#modal-box:hidden:first" ).fadeIn( "slow" );
        }); 
		
	});
</script>


<div id="trip-switcher">
	<a id="switch-back" alt="Previous Trip">
		<div id="left-switch" class="floating">
			&#8592;
		</div>
	</a>
	  
	<div id="trip" class="floating">
		
	</div>
	
	<a id="switch-forward" alt="Next Trip">	
		<div id="right-switch" class="floating">
			&#8594;
		</div>
	</a>
</div>
<?php }else{ ?>
	<div class="yellow-helper">No Inactive Trips, Sorry.</div>
<?php } ?>