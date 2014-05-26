<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	include_once($root."/engine/core/db/interface/bookings.php");
	include_once($root."/engine/core/db/interface/users.php");
	include_once($root."/engine/core/db/interface/tickets.php");
	include_once($root."/engine/core/db/interface/trips.php");
	include_once($root."/engine/core/db/interface/places.php");
	include_once($root."/engine/core/db/interface/agencies.php");
?>

<?php 
	$cart = $_COOKIE['cart'];
?>
<div id="wrapper">
	<div class="yellow-helper">The below items have been added via the "Find User Booking" tab.</div>
	<?php echo $cart; ?>
	
</div>