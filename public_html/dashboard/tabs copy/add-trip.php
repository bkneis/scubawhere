<?php

	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/places.php");
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/tickets.php");
    require_once($root."/engine/core/db/interface/trip_types.php");
	
    $company_id = $_SESSION['id'];
	$savedTickets = get_tickets_by_company($company_id);
	//get total tickets for company
	$numTickets = count_tickets_by_company($company_id);
	
?>
<link rel="stylesheet" type="text/css" href="http://xoxco.com/projects/code/tagsinput/jquery.tagsinput.css" />
<script type="text/javascript" src="http://xoxco.com/projects/code/tagsinput/jquery.tagsinput.js"></script>

<script src="<?php echo "http://" .$_SERVER['HTTP_HOST']. "/common/ckeditor/ckeditor.js"; ?>"></script>
<script src="<?php echo "http://" .$_SERVER['HTTP_HOST']. "/common/ckeditor/adapters/jquery.js"; ?>"></script>

<script>
	$(function(){
		//onload load in the description form by default
		$("#add-trip").load('tabs/add-trip-forms/details.php');
	});
</script>

<div id="trip-progress">
	<div id="first-stage" class="trip-progress-stage">Details</div>
	<div id="second-stage" class="trip-progress-stage">Location</div>
	<div id="third-stage" class="trip-progress-stage">Tickets</div>
</div>



<div id="add-trip" class="floating">
</div>

<form id="hidden-form" action="/engine/trips/add_trip.php" method="post">	
	<input type="hidden" name="name" value="">
	<input type="hidden" name="description" value="">
	<input type="hidden" name="type" value="">
	<input type="hidden" name="duration" value="">
	
	<div id="append-locations">
		<input type="hidden" name="locationCount" value="0">
	</div>
	
	<input type="hidden" id="ticketCount" name="ticketCount" value="0">
	
	<input type="submit" id="hidden-submit" class="form-button" value="Save Trip">
</form>


