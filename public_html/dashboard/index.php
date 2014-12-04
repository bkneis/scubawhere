<?php

	session_start();
	if(!$_COOKIE["scubawhere_session"]){
		header("Location: /dashboard/login/");
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
		header('Location: /dashboard/login/');
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>

	<title>Scuba Where | Dashboard</title>

	<link rel="stylesheet" href="/common/css/normalize.css" type="text/css" media="screen" charset="utf-8">
	<link rel='stylesheet' href='/common/css/fullcalendar.css' />
	<link rel="stylesheet" type="text/css" href="/common/css/jquery.reveal.css">
	<link rel="stylesheet" type="text/css" href="/common/css/font-awesome.min.css">

	<!--Bootstrap CSS-->
	<link rel="stylesheet" href="/common/bootstrap/css/bootstrap.min.css" type="text/css" />

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

	<script src="js/Controllers/Company.js"></script>
	<script>
		// Load company info
		Company.getCompany(function success(data) {
			console.info('Company info loaded');
			window.company = data;

			$('.username').text(window.company.name);
		});

		// Set scubawhere namespace
		window.sw = {};
	</script>

	<!-- ScubaWhere Files -->
	<script src="js/main.js"></script>
	<script src="js/ui.js"></script>
	<script src="js/navigation.js"></script>
	<script src="js/validate.js"></script>

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
	<div id="pageMssg">Page Message</div>

	<div class="sidebar-background"></div><!-- This is needed for pages that are shorter than the window height -->

	<div id="page">
		<!-- tabbed navigation and sidebar LEFT -->
		<div class="sidebar-background"></div><!-- This is needed for pages that are longer than the window height -->
		<div id="sidebar">
			<div id="tab-title">
				<b>scuba</b>whereRMS
			</div>
			<!-- <div id="dc-info">
				<div id="dc-name"></div>
				<div id="dc-uname"></div>
				<div id="dc-veri"></div>
			</div> -->

			<!-- Navigation including accordion drop down menus -->

			<ul id="sidenav">
				<li data-load="dashboard">
					<div>
						<i class="fa fa-tachometer"></i>
						<span>Dashboard</span>
					</div>
				</li>

				<li data-load="add-booking">
					<div>
						<i class="fa fa-plus"></i>
						<!-- <i class="fa fa-plus-square-o"></i> -->
						<span>Add Booking</span>
					</div>
				</li>

				<li data-load="find-booking">
					<div>
						<i class="fa fa-pencil"></i>
						<!-- <i class="fa fa-pencil-square-o"></i> -->
						<span>Manage Bookings</span>
					</div>
				</li>

				<li data-load="calendar">
					<div>
						<i class="fa fa-calendar"></i>
						<span>Calendar</span>
					</div>
				</li>

				<li data-load="reviews">
					<div>
						<i class="fa fa-comments"></i>
						<!-- <i class="fa fa-users"></i> -->
						<span>Reviews</span>
					</div>
				</li>

				<li data-load="reports">
					<div>
						<!-- <i class="fa fa-university"></i> -->
						<i class="fa fa-line-chart"></i>
						<!-- <i class="fa fa-usd"></i> -->
						<!-- <i class="fa fa-file-text-o"></i> -->
						<span>Financial Reports</span>
					</div>
				</li>

				<li>
					<div>
						<!-- <i class="fa fa-briefcase"></i> -->
						<!-- <i class="fa fa-bullhorn"></i> -->
						<!-- <i class="fa fa-paper-plane"></i> -->
						<i class="fa fa-sitemap"></i>
						<span>Management</span>
						<span class="arrow"></span>
					</div>
					<ul>
						<li data-load="accommodations">Accommodations</li>
						<li data-load="activate-trip">Activate Trips</li>
						<li data-load="add-ons">Add-ons</li>
						<li data-load="agents">Agents</li>
						<li data-load="boats">Boats</li>
						<li data-load="locations">Locations</li>
						<li data-load="packages">Packages</li>
						<li data-load="tickets">Tickets</li>
						<li data-load="trips">Trips</li>
					</ul>
				</li>

				<li data-load="settings">
					<div>
						<i class="fa fa-cog"></i>
						<span>Settings</span>
					</div>
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
</body>
</html>
