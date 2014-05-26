<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	include_once($root."/engine/core/db/interface/places.php");
	include_once($root."/engine/core/db/interface/trips.php");
	include_once($root."/engine/core/db/interface/tickets.php");
	
	$company_id = $_SESSION['id'];
	
	//get all companies trips in array
	$allTrips = get_active_trips_by_company($company_id);
	
 
	//function cuts the length of stings to length if over that
	function cutString($string, $length){
	
		if(strlen($string) > $length){
			$string = substr($string, 0, $length);
			$string = $string . "[...]";
		}
		
		return $string;
	}
?>
<!-- Live on page form validation -->
<script>
	$.validate({
    modules : 'location, date, security, file',
    onModulesLoaded : function() {
    }
  });
</script>
<script>
	$(document).ready(function(){
	
	
	
	/* create new array for trips */
	/* ********************************************************* */
	var inactiveTrips = [ <?php foreach($allTrips as $trip){ echo '"'.$trip['name'].'", '; }?> ];
	inactiveTripsID = [ <?php foreach($allTrips as $trip){ echo '"'.$trip['id'].'", '; }?> ];//made public for other function
	 
	
	
	
	//set trip to first inactive trip in array default
	$('#trip').text( inactiveTrips[0] );
		
		//made public for other function
		arrPosition = 0;
		var arrLength = inactiveTrips.length - 1;
		
		$("#trip-id").val(inactiveTripsID[arrPosition]);
		$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/trip-bookings/do.php?id=" + inactiveTripsID[arrPosition]);
		$("#act-trip-bookings").html('<img src="img/loading.gif">').load("tabs/trip-bookings/bookings.php?id=" + inactiveTripsID[arrPosition]);
		
		
		//click function to switch forward a trip
		$("#switch-forward").click(function(){
			if(arrPosition == arrLength){
				arrPosition = 0;
			}else{
				arrPosition++;
			}
			
			$('#trip').text( inactiveTrips[arrPosition] );
			$("#trip-id").val(inactiveTripsID[arrPosition]);
			$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/trip-bookings/do.php?id=" + inactiveTripsID[arrPosition]);
			$("#act-trip-bookings").html('<img src="img/loading.gif">').load("tabs/trip-bookings/bookings.php?id=" + inactiveTripsID[arrPosition]);
		});
		
		//click function to switch backward a month
		$("#switch-back").click(function(){
			if(arrPosition == 0){
				arrPosition = arrLength;
			}else{
				arrPosition--;
			}
			
			$('#trip').text( inactiveTrips[arrPosition] );
			$("#trip-id").val(inactiveTripsID[arrPosition]);
			$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/trip-bookings/do.php?id=" + inactiveTripsID[arrPosition]);
			$("#act-trip-bookings").html('<img src="img/loading.gif">').load("tabs/trip-bookings/bookings.php?id=" + inactiveTripsID[arrPosition]);
		});
		
		
		
	});
	
	
	 
	 
</script>

	
<div id="wrapper">	
	<div class="yellow-helper yellow-bttm-marg">
		Here you can view all members who are booked onto a particular trip.
	</div>
	
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
		
	<div id="enter-wrap" class="floating">
            <form action="/engine/trip-bookings/add_manual_booking.php" method="post">
                		
                <label class="item-head list-item-head trip-det-label floating">Trip Details</label>
				<div class="form-fields">
					
				</div>
				
                <label class="item-head list-item-head floating">Trip Dates & Capacities</label>

                <div id="trip-list-col-titles" class="floating"><div id="col-title-start">Start Date</div><div id="col-title-end">End Date</div><div id="col-title-cap">Max Capacity</div><div id="col-title-book">Bookings</div></div>

                <div id="act-trip-list" class="floating"></div>
                
                <label class="item-head list-item-head">Bookings</label>
                
                <div id="act-trip-bookings" class="floating"></div>
			
		</form>
	</div>
    
</div>

