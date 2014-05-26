<?php
	
	$root =  $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/init.php");
	require_once($root."/engine/core/db/interface/sessions.php");
	require_once($root."/engine/core/db/interface/trips.php");
	
	$id = $_SESSION['id'];
	
	//get all sessions for company
	//get all trips by company id
	$allTrips = get_trips_by_company($id);
	
	//todays date
	$today = date("Y-m-d");
	
	//loop through trips, get sessions for trip and put into array
	foreach($allTrips as $trip){
		//get sessions
		$tripSessions = get_sessions_by_trip($trip['id']);
		
		//loop through sessions and put data into an array
		if(is_array($tripSessions)){
			foreach($tripSessions as $session){
				//only put into the array if it starts today
				if(strpos($session['start'], $today) !== false){ 
					$startingTrips[] = array(
						'sessionID'  => $session['id'],
						'tripID'  => $trip['id'],
						'time'  => date("g:ia", strtotime($session['start'])),
						'name'  => $trip['name'],
						
					);
				} 
			}
		}
	}
?>

<div class="block-title">Trips Starting Today</div>

<?php if(is_array($startingTrips)){ ?>

	<?php foreach($startingTrips as $startingTrip){ ?>
		<div class="dash-trip-item">
			<div class="dash-trip-name">
				<?php echo $startingTrip['name']; ?>
			</div>
			<div class="dash-trip-time">
				<?php echo $startingTrip['time']; ?>
			</div>
		</div>
	<?php } ?>
	
<?php }else{ ?>
	<div class="dash-trip-mssg">
			<?php echo "There are no trips starting today.."; ?>
	</div>
<?php } ?>
