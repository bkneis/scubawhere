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
	//on selection of a radio trip - load into trip activation appropriate trip
	$(".tripID").click(function(){
		var actID = $(this).val();
		
		$("#activate-wrap").html('<img src="img/loading.gif">').load("tabs/activate-trip/do.php?id=" + actID);
	});
</script>

<div id="list-view-title" class="floating">List View</div>
<div id="list-view-wrap" class="floating">

	<?php foreach($allTrips as $trip){ ?>
			<div class="list-view-item">
					<input type="radio" class="tripID" value="<?php echo $trip['id']; ?>" name="trip" id="<?php echo $trip['name']; ?>">
					<label for="<?php echo $trip['name']; ?>"><?php echo $trip['name']; ?></label>
			</div>
	<?php } ?>
</div>

<?php }else{ ?>
	<div class="yellow-helper">No Inactive Trips, Sorry.</div>
<?php } ?>