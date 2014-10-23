<?php

	session_start();
	if(!$_COOKIE["scubawhere_session"]){
		header("Location: /dashboard/login/");
		exit();
	}

	// Conditional curl because of Soren's dev env
	if($_SERVER['HTTP_HOST'] === 'scubawhere.app:8000')
		$ch = curl_init( 'localhost/company' );
	else
		$ch = curl_init( $_SERVER['HTTP_HOST'].'/company' );

	$strCookie = 'scubawhere_session=' . $_COOKIE['scubawhere_session'] . '; path=/';

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );

	$result = curl_exec( $ch );

	curl_close( $ch );

	$result = json_decode( $result );
	if( empty($result->id) ) {
		//not logged in
		header('Location: /dashboard/login/');
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>

	<title>Scuba Where | Dashboard</title>

	<link rel="stylesheet" href="/common/css/normalize.css" type="text/css" media="screen" charset="utf-8">
	<link href='css/fullcalendar.css' rel='stylesheet' />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="/common/css/jquery.tagsinput.css">
	<link rel="stylesheet" type="text/css" href="/common/css/jquery.reveal.css">

	<!-- favicon -->
	<link rel="icon" type="image/ico" href="../common/favicon.ico" />

	<!-- dash config js -->

	<!-- Third Party Libraries -->
	<script src="/common/js/jquery.js"></script>
	<script src="/common/js/ui.min/jquery-ui.min.js"></script>
	<script src="/common/js/jquery.tagsinput.min.js"></script>
	<script src="/common/js/moment.min.js"></script>
	<script src="/common/js/fullcalendar.min.js"></script>
	<script src="/common/js/hashchange.min.js"></script>
	<script src="/common/js/handlebars.js"></script>
	<script src="/common/ckeditor/ckeditor.js"></script>
	<script src="/common/ckeditor/adapters/jquery.js"></script>
	<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
	<script src="/common/js/underscore-min.js"></script>

	<!-- ScubaWhere Files -->
	<script src="js/ui.js"></script>
	<script src="js/navigation.js"></script>
	<script src="js/main.js"></script>
	<script src="js/validate.js"></script>

</head>
<body>
	<?php
	   //INCLUDE HEADER
	   include_once("common/header/header.php");
    ?>

	<div id="page">
		<!-- tabbed navigation and sidebar LEFT -->
		<div id="sidebar">
			<div id="tab-title">Admin Panel</div>
			<!-- <div id="dc-info">
				<div id="dc-name"></div>
				<div id="dc-uname"></div>
				<div id="dc-veri"></div>
			</div> -->

			<!-- Navigation including accordion drop down menus -->

			<ul id="sidenav">
				<li data-load="dashboard">
					<div>
						<img src="img/icons/dashboard-icon.png" />
						<span>Dashboard</span>
					</div>
				</li>

				<li>
					<div>
						<img src="img/icons/profile-icon.png" />
						<span>Agents</span>
						<span class="arrow"></span>
					</div>
					<ul>
						<li data-load="agents">Manage Agents</li>
					</ul>
				</li>

				<li>
					<div>
						<img src="img/icons/booking-icon.png" />
						<span>Booking</span>
						<span class="arrow"></span>
					</div>
					<ul>
						<li data-load="add-booking">Add Booking</li>
						<li data-load="find-booking">Find Booking</li>
						<li data-load="trip-booking">Trip Bookings</li>
					</ul>
				</li>

				<li>
					<div>
						<img src="img/icons/trip-icon.png" />
						<span>Trip Management</span>
						<span class="arrow"></span>
					</div>

					<ul>
						<li data-load="trips">Manage Trips</li>
						<li data-load="activate-trip">Activate Trips</li>
						<li data-load="tickets">Tickets</li>
						<li data-load="packages">Packages</li>
						<li data-load="locations">Locations</li>
					</ul>
				</li>

				<li>
					<div>
						<img src="img/icons/trip-icon.png" />
						<span>Resources</span>
						<span class="arrow"></span>
					</div>

					<ul>
						<li data-load="boats">Boats</li>
						<li data-load="add-ons">Add-ons</li>
						<!--<li data-load="crew">Crew</li>-->
					</ul>
				</li>

				<li>
					<div>
						<img src="img/icons/reviews-icon.png" />
						<span>Reviews</span>
						<span class="arrow"></span>
					</div>

					<ul>
						<li data-load="reviews">Recent Reviews</li>
					</ul>
				</li>

				<li>
					<div>
						<img src="img/icons/profile-icon.png" />
						<span>Profile</span>
						<span class="arrow"></span>
					</div>

					<ul>
						<li>Analytics</li>
						<li>Edit Profile</li>
					</ul>
				</li>
			</ul>
		</div>

		<!-- main page content to be loaded by AJAX -->
		<div id="guts">
			<div id="content-title"></div>

			<div id="content">
				<div id="wrapper">

				</div>
			</div>
		</div>
	</div>

	<?php
	   //INCLUDE FOOTER
	   include_once("common/footer/footer.php");
    ?>
</body>
</html>
