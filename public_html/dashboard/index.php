<?php

	session_start();
	if(!$_COOKIE["scubawhere_session"]) {
		if($_SERVER['HTTP_HOST'] === 'rms.scubawhere.com')
			$location = '/login';
		else
			$location = '/dashboard/login';

		header("Location: " . $location);
		exit();
	}

	$ch = curl_init( $_SERVER['HTTP_HOST'].'/company' );

	$strCookie = 'scubawhere_session=' . $_COOKIE['scubawhere_session'] . '; path=/';

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );

	$result = curl_exec( $ch );
	curl_close( $ch );

	$result = json_decode( $result );
	if( empty($result->id) ) {
		//not logged in
		if($_SERVER['HTTP_HOST'] === 'rms.scubawhere.com')
			$location = '/login';
		else
			$location = '/dashboard/login';
		header("Location: " . $location);
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>

	<title>scubawhereRMS | Dashboard</title>

	<link rel='stylesheet' href='/common/css/fullcalendar.css' />
	<link rel="stylesheet" type="text/css" href="/common/css/jquery.reveal.css">
	<link rel="stylesheet" type="text/css" href="/common/vendor/font-awesome/css/font-awesome.min.css">

	<!--Bootstrap CSS-->
	<link rel="stylesheet" href="/common/css/bootstrap-scubawhere.css" type="text/css" />


	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css">

	<link rel="stylesheet" type="text/css" href="css/style.css">

	<!-- favicon -->
	<link rel="icon" type="image/ico" href="/common/favicon.ico" />



	<!-- dash config js -->

	<!-- Third Party Libraries -->
	<script src="/common/js/jquery/jquery.min.js"></script>

	<!--Bootstrap js-->
	<script type="text/javascript" src="/common/bootstrap/js/bootstrap.min.js"></script>

	<script src="/common/js/hashchange.min.js"></script>

	<script src="/common/js/handlebars.min.js"></script>

	<script src="/common/ckeditor/ckeditor.js"></script>
	<script src="/common/ckeditor/adapters/jquery.js"></script>

	<script src="/common/js/moment.min.js"></script>

	<script src="/common/js/underscore-min.js"></script>

	<!--Datetimepicker-->
	<link rel="stylesheet" href="/common/css/bootstrap-datetimepicker.css" type="text/css" />
	<script type="text/javascript" src="/common/js/bootstrap-datetimepicker.min.js"></script>

	<!--intojs tour-->
	<link href="/common/css/introjs.css" rel="stylesheet">
	<script src="/common/js/intro.js"></script>

	<script src="js/Controllers/Company.js"></script>
	<script>
		// Load company info
		Company.getCompany(function success(data) {
			console.info('Company info loaded');
			window.company = data;

			$(function() {
				$('.username').text(window.company.name);
			});
		});

		// Set scubawhere namespace
		window.sw = {};

	</script>

	<!-- ScubaWhere Files -->
	<script src="js/main.js"></script>
	<script src="js/ui.js"></script>
	<script src="js/navigation.js"></script>
	<script src="js/validate.js"></script>
	<script src="js/tour.js"></script>

</head>
<body>
	<div id="nav">
		<div id="nav-wrapper">
			<h1 id="logo"><a href="/dashboard"><img src="/common/img/Scubawhere_logo.png"></a></h1>
			<button class="btn btn-default pull-right" id="logout">Logout</button>
			<div class="nav-opt pull-right"><a href="#settings" class="username"></a></div>
		</div>
	</div>

	<!-- PAGE MESSAGE FOR ERRORS AND SUCCESS MASAGES -->
	<div id="pageMssg"></div>

	<div class="sidebar-background"></div><!-- This is needed for pages that are shorter than the window height -->

	<div id="page">
		<!-- tabbed navigation and sidebar LEFT -->
		<div class="sidebar-background"></div><!-- This is needed for pages that are longer than the window height -->
		<div id="sidebar">
			<!-- Navigation including accordion drop down menus -->
			<ul id="sidenav">
				<li>
					<a href="#dashboard">
						<i class="fa fa-tachometer fa-lg fa-fw"></i>
						<span>Dashboard</span>
					</a>
				</li>

				<li>
					<a href="#add-booking">
						<i class="fa fa-plus fa-lg fa-fw"></i>
						<!-- <i class="fa fa-plus-square-o"></i> -->
						<span>Add Booking</span>
					</a>
				</li>

				<li>
					<a href="#manage-bookings">
						<i class="fa fa-pencil fa-lg fa-fw"></i>
						<!-- <i class="fa fa-pencil-square-o"></i> -->
						<span>Manage Bookings</span>
					</a>
				</li>

				<li>
					<a href="#calendar">
						<i class="fa fa-calendar fa-lg fa-fw"></i>
						<span>Calendar</span>
					</a>
				</li>

				<?php /* <li>
					<a href="#reviews">
						<i class="fa fa-comments fa-lg fa-fw"></i>
						<!-- <i class="fa fa-users"></i> -->
						<span>Reviews</span>
					</a>
				</li> */ ?>

				<li>
					<a href="#reports">
						<!-- <i class="fa fa-university"></i> -->
						<i class="fa fa-line-chart fa-lg fa-fw"></i>
						<!-- <i class="fa fa-usd"></i> -->
						<!-- <i class="fa fa-file-text-o"></i> -->
						<span>Financial Reports</span>
					</a>
				</li>

				<li>
					<div>
						<!-- <i class="fa fa-briefcase"></i> -->
						<!-- <i class="fa fa-bullhorn"></i> -->
						<!-- <i class="fa fa-paper-plane"></i> -->
						<i class="fa fa-sitemap fa-lg fa-fw"></i>
						<span>Management</span>
						<span class="caret"></span>
					</div>
					<ul id="management-submenu">
						<li>
							<a href="#accommodations">Accommodations</a>
						</li>
						<li>
							<a href="#activate-trip">Activate Trips</a>
						</li>
						<li>
							<a href="#add-ons">Add-ons</a>
						</li>
						<li>
							<a href="#agents">Agents</a>
						</li>
						<li>
							<a href="#boats">Boats</a>
						</li>
						<li>
							<a href="#locations">Locations</a>
						</li>
						<li>
							<a href="#packages">Packages</a>
						</li>
						<li>
							<a href="#tickets">Tickets</a>
						</li>
						<li>
							<a href="#trips">Trips</a>
						</li>
					</ul>
				</li>

				<li>
					<a href="#help">
						<i class="fa fa-question-circle fa-lg fa-fw"></i>
						<!-- <i class="fa fa-users"></i> -->
						<span>Help & FAQ</span>
					</a>
				</li>

				<li>
					<a href="#settings">
						<i class="fa fa-cog fa-lg fa-fw"></i>
						<span>Settings</span>
					</a>
				</li>
			</ul>
		</div>

		<!-- main page content to be loaded by AJAX -->
		<div id="guts">
		<!--add timeline here for wizard-->
			<div id="breadcrumbs"></div>

			<div id="content">
				<div id="wrapper"></div>
			</div>
		</div>
	</div>
</body>
</html>
