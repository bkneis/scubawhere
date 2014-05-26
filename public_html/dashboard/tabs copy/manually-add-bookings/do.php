<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/sessions.php");
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/tickets.php");
	
	$company_id = $_SESSION['id'];
	
	//active trip id
	$tripID = $_GET['id'];
	
	//get all companies trips in array
	$allSessions = get_sessions_by_trip($tripID);
	
?>


<?php	
	
	foreach($allSessions as $tripSession){
	
		$startDate = date('d M Y', strtotime($tripSession['start']));
		$endDate = date('d M Y', strtotime($tripSession['end']));
                $capacity = get_session_capacity_count($tripSession['id']);
                $bookings = get_session_booking_count($tripSession['id']);
		
		?>
		<div class="act-list-item">
			
			<input type="radio" name="session" value="<?php echo $tripSession['id']; ?>" id="<?php echo $tripSession['id']; ?>" />
			<label for="<?php echo $tripSession['id']; ?>" >
				
				<div id="col-title-start"><?php echo $startDate; ?></div><div id="col-title-end"><?php echo $endDate; ?></div><div id="col-title-cap"><?php echo $capacity; ?></div><div id="col-title-book"><?php echo $bookings; ?></div>
			</label>
                        
		</div>
		<?php
	}
?>