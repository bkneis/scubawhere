<?php
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/places.php");
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/tickets.php");
	
	$company_id = $_SESSION['id'];
	
	$tripID = $_GET['id'];
	
	//get trip by the get variable (trip id)
	$trip = get_trip_by_id($tripID);
	
	//trip details
	$tripTitle = $trip['name'];
	$tripDescription = $trip['description'];
	$tripDuration = $trip['duration'];
	
	//get all trip tickets
	$allTickets = get_tickets_by_trip($tripID);//MAKE THIS TICKETS BY SESSION?
	
?>
<script>
	$(document).ready(function(){
		
		$('#inact-form-wrap').html('<div id="loading"><img src="img/loading.gif"></div>').load("tabs/activate-trip/auto.php?id=<?php echo $tripID; ?>");
		$('#auto-switch').addClass('switch-act');
		$('#manual-switch').removeClass('switch-act');
		
		
		$("#auto-switch").click(function(){
			$('#inact-form-wrap').html('<div id="loading"><img src="img/loading.gif"></div>').load("tabs/activate-trip/auto.php?id=<?php echo $tripID; ?>");
			$('#auto-switch').addClass('switch-act');
			$('#manual-switch').removeClass('switch-act');
		});
		
		$("#manual-switch").click(function(){
			$('#inact-form-wrap').html('<div id="loading"><img src="img/loading.gif"></div>').load("tabs/activate-trip/manual.php?id=<?php echo $tripID; ?>");
			$('#manual-switch').addClass('switch-act');
			$('#auto-switch').removeClass('switch-act');
		});
	});
</script>


<div id="hack">
</div>

<div class="floating" id="inact-trip-info">
	<div id="inact-trip-details-title" class="item-head">Inactive Trip Details</div>
	<div id="left-inact-col">
		
		<div id="inact-tickets">
			<label>Trip Tickets</label>
			<?php //list all tickets from array ?>
			<!-- <?php if(is_array($allTickets)){ ?> -->
				<?php foreach($allTickets as $ticket){ ?>
					<div class="inact-ticket floating">
						<div class="inact-ticket-name">
							<?php echo $ticket['name']; ?>
						</div>
						<div class="inact-ticket-price">
							<?php echo $ticket['price']; ?>
						</div>
					</div>
				<?php } ?>
			<!--
<?php }else{ ?>
				<p>No tickets for this trip..</p>
			<?php } ?>
-->
		</div>
	</div>
	
	<div id="right-inact-col">
		<label>Trip Duration</label>
		<p><?php echo $tripDuration. " days"; ?></p>
		
		<label>Trip Description</label>
		<p><?php echo $tripDescription; ?></p>
	</div>
</div>

<div id="form-switch" class="floating">
	<a id="auto-switch"><div id="automatic">Automatic</div></a>
	<a id="manual-switch"><div id="manual">Manual</div></a>
</div>

<div id="inact-form-wrap">
</div>