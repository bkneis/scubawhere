<?php 
	$root =  $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/init.php");
	require_once($root."/engine/core/db/interface/bookings.php");
	require_once($root."/engine/core/db/interface/users.php");
	
	$bookings = array_reverse(get_bookings_by_company($_SESSION['id']));

?>


<div class="block-title">Recent Bookings</div>
<div id="bookings-wrap">
	<?php foreach($bookings as $booking){ ?>
		<?php	
			$user = get_user_by_id($booking['id']);
		?>
		<div class="dash-booking">
			<div class="dash-booking-user">
				<?php echo $user['firstName']." ".$user['lastName']; ?>
			</div>
			
			<div class="dash-booking-date">
				<?php echo date("d-m-Y H:i:s", strtotime($booking['created'])); ?>
			</div>
		</div>
	<?php } ?>
</div>