<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/tickets.php");
    require_once($root."/engine/core/db/interface/agencies.php");
	
	$company_id = $_SESSION['id'];
	
	//get all companies trips in array
	$allTrips = get_active_trips_by_company($company_id);
        
        
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
	var activeTrips = [ <?php foreach($allTrips as $trip){ echo '"'.$trip['name'].'", '; }?> ];
	var activeTripsID = [ <?php foreach($allTrips as $trip){ echo '"'.$trip['id'].'", '; }?> ];
	 
	
	
	
	//set trip to first inactive trip in array default
	$('#trip').text( activeTrips[0] );
		
		var arrPosition = 0;
		var arrLength = activeTrips.length - 1;
		
		$("#trip-id").val(activeTripsID[arrPosition]);
		$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/assign-crew/do.php?id=" + activeTripsID[arrPosition]);
		$("#radio-bttns").html('<img src="img/loading.gif">').load("tabs/assign-crew/tickets.php?id=" + activeTripsID[arrPosition]);
		
		
		//click function to switch forward a trip
		$("#switch-forward").click(function(){
			if(arrPosition == arrLength){
				arrPosition = 0;
			}else{
				arrPosition++;
			}
			
			$('#trip').text( activeTrips[arrPosition] );
			$("#trip-id").val(activeTripsID[arrPosition]);
			$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/assign-crew/do.php?id=" + activeTripsID[arrPosition]);
			$("#radio-bttns").html('<img src="img/loading.gif">').load("tabs/assign-crew/tickets.php?id=" + activeTripsID[arrPosition]);
		});
		
		//click function to switch backward a month
		$("#switch-back").click(function(){
			if(arrPosition == 0){
				arrPosition = arrLength;
			}else{
				arrPosition--;
			}
			
			$('#trip').text( activeTrips[arrPosition] );
			$("#trip-id").val(activeTripsID[arrPosition]);
			$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/assign-crew/do.php?id=" + activeTripsID[arrPosition]);
			$("#radio-bttns").html('<img src="img/loading.gif">').load("tabs/assign-crew/tickets.php?id=" + activeTripsID[arrPosition]);
		});
		
	});
	
	$('#entry-submit').click(function() {
		if($('#radio-bttns input:radio:checked').length > 0){
			$('#activate-wrap').ajaxForm(function(){
	             //success modal appears!
	            $( "#modal-overlay:hidden:first" ).fadeIn( "slow" );
	            $( "#modal-box:hidden:first" ).fadeIn( "slow" );  
			});
		 }else{
		    alert("Please select a ticket.");
		 }
	 });
	 
	 
</script>

<script>
$( "#certified" ).click(function() {
  $( "#is-certified" ).toggle("slow");
});
</script>

<script>
  	$(function() {
	    // Enable are you sure script
	    $('form').areYouSure();
    });
</script>


<script>
    
$(document).ready(function(){
           
//    $("#agencyName").attr("disabled", "disabled");
//    $("#certificate").attr("disabled", "disabled");
//    $("#certificateName").attr("disabled", "disabled");
    
    
    $("#agency").change(function(){
       var agency_id = $("#agency option:selected").attr('value');
       
       if (agency_id === '0') {
            $("#agencyName").removeAttr("disabled");
        } else {
            $("#agencyName").attr("disabled", "disabled");
            $("#agencyName").val("");
        }
       
       $.post("/engine/core/db/ajax/get_certificate_options_by_agency.php", {agencyID : agency_id}, function(data){
           if (data) {
               $("#certificate").html(data);               
               $("#certificate").removeAttr("disabled");
               $("#certificate").trigger("change");
            } else {
                $("#certificate").attr("disabled", "disabled");
            }        
       });
    });
    
    $("#certificate").change(function(){
        var certificate_id = $("#certificate option:selected").attr('value');
        if (certificate_id === '0') {
            $("#certificateName").removeAttr("disabled");
        } else {
            $("#certificateName").attr("disabled", "disabled");
        }
    });
    
    
    $("#agency").trigger("change");
    
});
    
</script>


<div id="wrapper">	
	<div class="yellow-helper yellow-bttm-marg">
		Use this form to assign crew members to a particular trip.
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
            <form action="/engine/bookings/add_manual_booking.php" method="post">
                		
            <label class="item-head list-item-head trip-det-label floating">Trip Details</label>
			<div class="form-fields">
				
			</div>
			
            <label class="item-head list-item-head floating">Trip Dates & Capacities</label>

            <div id="trip-list-col-titles" class="floating">
            	<div id="col-title-start">Start Date</div>
            	<div id="col-title-end">End Date</div>
            	<div id="col-title-cap">Max Capacity</div>
            	<div id="col-title-book">Bookings</div>
            </div>

            <div id="act-trip-list" class="floating"></div>
                
		</form>
	</div>
    
</div>
