<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/places.php");
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/tickets.php");
    require_once($root."/engine/core/db/interface/agencies.php");
	
	$company_id = $_SESSION['id'];
	
	//get all companies trips in array
	$allTrips = get_active_trips_by_company($company_id);
	
	//get all countries for select
	$allCountries = get_all_COUNTRY_CONSTANTS();
	
        //get agencies & certificates
        $allAgencies = get_all_AGENCY_CONSTANTS();
        $allCertificates = get_all_CERTIFICATE_CONSTANTS();
        
        
?>
<?php 
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
	var inactiveTripsID = [ <?php foreach($allTrips as $trip){ echo '"'.$trip['id'].'", '; }?> ];
	 
	
	
	
	//set trip to first inactive trip in array default
	$('#trip').text( inactiveTrips[0] );
		
		var arrPosition = 0;
		var arrLength = inactiveTrips.length - 1;
		
		$("#trip-id").val(inactiveTripsID[arrPosition]);
		$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/manually-add-bookings/do.php?id=" + inactiveTripsID[arrPosition]);
		$("#radio-bttns").html('<img src="img/loading.gif">').load("tabs/manually-add-bookings/tickets.php?id=" + inactiveTripsID[arrPosition]);
		
		
		//click function to switch forward a trip
		$("#switch-forward").click(function(){
			if(arrPosition == arrLength){
				arrPosition = 0;
			}else{
				arrPosition++;
			}
			
			$('#trip').text( inactiveTrips[arrPosition] );
			$("#trip-id").val(inactiveTripsID[arrPosition]);
			$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/manually-add-bookings/do.php?id=" + inactiveTripsID[arrPosition]);
			$("#radio-bttns").html('<img src="img/loading.gif">').load("tabs/manually-add-bookings/tickets.php?id=" + inactiveTripsID[arrPosition]);
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
			$("#act-trip-list").html('<img src="img/loading.gif">').load("tabs/manually-add-bookings/do.php?id=" + inactiveTripsID[arrPosition]);
			$("#radio-bttns").html('<img src="img/loading.gif">').load("tabs/manually-add-bookings/tickets.php?id=" + inactiveTripsID[arrPosition]);
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
		Use this form to manually enter members onto a trip. Switch through trips using the bar below.
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
                
			<span class="form-item form-item-memb">
				
                            
				<label class="item-head">Find Registered Member</label>
				
                <label class="form-label">Email Address</label>
				<div class="form-fields">
					<input type="text" name="searchEmail" class="form-text" placeholder="Email Address" data-validation="email"/>
				</div>
                                
                               
                                
                <label class="item-head">Register New Member</label>
                                
				<label class="form-label">Member First & Last Name</label>
				<div class="form-fields">
					<input type="text" class="form-text" placeholder="First" name="firstName" data-validation="length" data-validation-length="min1" data-validation-help="Please enter a first name." /> 						<input type="text" class="form-text" name="lastName" placeholder="Last" data-validation="length" data-validation-length="min1" data-validation-help="Please enter a lest name." />
				</div>
				
                <label class="form-label">Email Address</label>
				<div class="form-fields">
					<input type="text" name="emailAddress" class="form-text" placeholder="Email Address" data-validation="email"/>
				</div>
				
				
				<label class="form-label">Date of Birth</label>
				<div class="form-fields">
					<input type="text" name="birthday" class="form-text" placeholder="DD/MM/YYYY" data-validation="date" data-validation-format="dd/mm/yyyy"/>
				</div>
				
				<label class="form-label">Country</label>
				<div class="form-fields">
					<select name="country" class="form-select">
						<?php foreach($allCountries as $country){ ?>
							<option value="<?php echo $country[id]; ?>"><?php echo $country[name]; ?></option>
						<?php } ?>
					</select>
				</div>
				
				<label class="form-label">Phone Number</label>
				<div class="form-fields">
					<input type="text" class="form-text" name="phone" placeholder="Phone Number" data-validation="length" data-validation-length="11-16" data-validation-help="Please enter an 11 digit phone number."/>
				</div>
				
				
                <label class="item-head">Diver Certification</label>
				<div class="form-fields">
					<label for="certified" id="cert-label">Is the diver certified?</label>
					<input type="checkbox" class="form-text" name="certified" id="certified" />
					
					
					<div id="is-certified" style="display: none">
												
						<label class="form-label">Certifying Agency</label>                                                        
						<select name="agency" id="agency" class="form-select">
							<?php foreach($allAgencies as $agency){ ?>
								<option value="<?php echo $agency['id']; ?>"><?php echo $agency['abbr']; ?></option>
							<?php } ?>
                                <option value="0">Other</option>
						</select>
						
                        <input type="text" class="form-text" name="agencyName" id="agencyName" />
                        <label class="form-label">Level of Certification</label>
                                                    
						<select name="certificate" id="certificate" class="form-select">
                                <option value="0">Other</option>
						</select>
						
                        <input type="text" class="form-text" name="certificateName" id="certificateName" />
						<label class="form-label">Date of Last Dive</label>
						<input type="text" class="form-text" name="dateLastDive" placeholder="DD/MM/YYYY" data-validation="date" data-validation-format="dd/mm/yyyy"/>
 
					</div>
					
				</div>
								
				<label class="item-head">Ticket</label>
                <div class="form-fields">
					<div id="radio-bttns"></div>
				</div>
				
				<input type="hidden" id="trip-id" value="" name="tripId" />
				
				<input type="submit" class="form-button enter-bttn" id="entry-submit" value="Enter Booking"/>
				
			</span>
		</form>
	</div>
    
</div>

