<?php

$HOST = $_SERVER['HTTP_HOST'];
$PROTOCOL = 'http';
	if(!empty($_SERVER['HTTPS'])) $PROTOCOL = 'https'; // http://php.net/manual/en/reserved.variables.server.php
	// If not accessed from the rms subdomain, redirect to it
	/*if(substr($HOST, 0, 3) !== 'rms')
	{
		// To allow for local dev environments, only change the sub domain part of the url
		$hostParts = explode('.', $HOST);
		switch(count($hostParts))
		{
			case 2: $location = 'rms.' . $hostParts[0] . '.' . $hostParts[1]; break; // Add the subdomain
			case 3: $location = 'rms.' . $hostParts[1] . '.' . $hostParts[2]; break; // Replace the subdomain
			default: $PROTOCOL = 'http'; $location = 'scubawhere.com'; // Case undefined, go to main domain
		}

		header("Location: " . $PROTOCOL . "://" . $location . "/");
		exit;
	}*/
	$BASE_URL = $PROTOCOL . "://" . $HOST;

	session_start();

	// Check for the authentication cookie
	if(!$_COOKIE["scubawhere_session"])
	{
		header("Location: " . $BASE_URL . "/login/");
		exit();
	}

	// Check if company details can be received with Laravel
	$strCookie = 'scubawhere_session=' . $_COOKIE['scubawhere_session'] . '; path=/';
	$ch = curl_init($BASE_URL . '/api/company');
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
	$result = curl_exec( $ch );
	curl_close( $ch );
	$result = json_decode( $result );
	if( empty($result->id) ) {
		// Not logged in
		header("Location: " . $BASE_URL . "/login/");
		exit();
	}
?>
<!DOCTYPE html>
<html>
<head>

	<title>scubawhereRMS | Dashboard</title>

	<!-- favicon -->
	<link rel="icon" type="image/ico" href="/common/favicon.ico" />

	<!--Bootstrap CSS-->
	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-scubawhere.css" />

	<!-- scubawhere styles -->
	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />

	<!-- Plugins -->
	<link rel='stylesheet' type="text/css" href='/common/css/fullcalendar.min.css' />
	<link rel="stylesheet" type="text/css" href="/common/css/jquery.reveal.css" />
	<link rel="stylesheet" type="text/css" href="/common/vendor/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-datetimepicker.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/select2.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/select2-bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/introjs.css" />
	<link rel="stylesheet" type="text/css" href="/common/vendor/nprogress/nprogress.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap.datatables.css" />
	<link rel="stylesheet" type="text/css" href="/common/vendor/datatables-tabletools/css/dataTables.tableTools.css" />

	<!-- jQuery -->
	<script src="/common/js/jquery/jquery.min.js"></script>

	<!--Bootstrap js-->
	<script type="text/javascript" src="/common/bootstrap/js/bootstrap.min.js"></script>

	<!-- other -->
	<!--<script type="text/javascript" src="/common/js/handlebars.min.js"></script>-->
	<script type="text/javascript" src="/common/vendor/handlebars/handlebars.min.js"></script>
	<script type="text/javascript" src="/common/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="/common/ckeditor/adapters/jquery.js"></script>
	<script type="text/javascript" src="/common/js/moment.min.js"></script>
	<script type="text/javascript" src="/common/js/underscore-min.js"></script>
	<script type="text/javascript" src="/common/js/jquery/jquery.serialize-object.min.js"></script>
	<script type="text/javascript" src="/common/js/jquery/jquery.reveal.js"></script>

	<!--Datetimepicker-->
	<script type="text/javascript" src="/common/js/bootstrap-datetimepicker.min.js"></script>

	<!--Select 2-->
	<script type="text/javascript" src="/common/js/select2.min.js"></script>

	<!--intojs tour-->
	<!--<script type="text/javascript" src="/common/js/intro.js"></script>-->

	<!--nprogress bar-->
	<script type="text/javascript" src="/common/vendor/nprogress/nprogress.js"></script>

	<!-- Datatables -->
	<script type="text/javascript" src="/common/js/jquery/jquery.datatables.min.js"></script>
	<script type="text/javascript" src="/common/js/bootstrap.datatables.js"></script>
	<script type="text/javascript" src="/common/vendor/datatables-tabletools/js/dataTables.tableTools.js"></script>


	<!-- scubawhere files -->
	<script type="text/javascript">
		// Set scubawhere namespace
		window.sw = {};
		window.promises = {};
		window.facebook = {};
	</script>

	<?php if(gethostname() === 'rms.scubawhere.com') { ?>
		<script type="text/javascript" src="js/app.min.js"></script>
	<?php } else { ?>
		<script type="text/javascript" src="js/main.js"></script>
		<script type="text/javascript" src="js/navigation.js"></script>
		<!--<script type="text/javascript" src="js/tour.js"></script>-->
		<script type="text/javascript" src="js/ui.js"></script>
		<script type="text/javascript" src="js/validate.js"></script>

		<!-- Load all front-end controllers -->
		<script type="text/javascript" src="js/Controllers/Accommodation.js"></script>
		<script type="text/javascript" src="js/Controllers/Addon.js"></script>
		<script type="text/javascript" src="js/Controllers/Agency.js"></script>
		<script type="text/javascript" src="js/Controllers/Agent.js"></script>
		<script type="text/javascript" src="js/Controllers/Boat.js"></script>
		<script type="text/javascript" src="js/Controllers/Boatroom.js"></script>
		<script type="text/javascript" src="js/Controllers/Booking.js"></script>
		<script type="text/javascript" src="js/Controllers/Class.js"></script>
		<script type="text/javascript" src="js/Controllers/Company.js"></script>
		<script type="text/javascript" src="js/Controllers/Course.js"></script>
		<script type="text/javascript" src="js/Controllers/Customer.js"></script>
		<script type="text/javascript" src="js/Controllers/Location.js"></script>
		<script type="text/javascript" src="js/Controllers/Package.js"></script>
		<script type="text/javascript" src="js/Controllers/Payment.js"></script>
		<script type="text/javascript" src="js/Controllers/Refund.js"></script>
		<script type="text/javascript" src="js/Controllers/Report.js"></script>
		<script type="text/javascript" src="js/Controllers/Session.js"></script>
		<script type="text/javascript" src="js/Controllers/Ticket.js"></script>
		<script type="text/javascript" src="js/Controllers/Timetable.js"></script>
		<script type="text/javascript" src="js/Controllers/Trip.js"></script>
		<script type="text/javascript" src="js/Controllers/CustomerGroup.js"></script>
		<script type="text/javascript" src="js/Controllers/Campaign.js"></script>
		<script type="text/javascript" src="js/Controllers/Certificate.js"></script>
        <script type="text/javascript" src="js/Controllers/Equipment.js"></script>
	<?php } ?>

	<script type="text/javascript">
		// Load company info
		Company.getCompany(function success(data) {
			console.info('Company info loaded');
			window.company = data;

			$(function() {
				$('.username').text(window.company.name);
			});
		});

		/**
		 * Nothing else should be in here, in the index.php!
		 * Every other logic/code/whatever MUST go into their respective tab's script and onLoad function!
		 */
	</script>

	<meta http-equiv="X-UA-Compatible" content="IE=9">

</head>
<body>
	<div id="nav">
		<div id="nav-wrapper">
			<h1 id="logo"><a href="/"><img src="/common/img/Scubawhere_logo.png"></a></h1>

			<button class="btn btn-default pull-right" id="logout">Logout</button>

			<div class="nav-opt pull-right"><a href="#settings" class="username"></a></div>

			<!--<div class="notifications pull-right">
				<i style="padding-left:8px; padding-top:9px; cursor:pointer" class="fa fa-bell fa-lg fa-fw"></i>
				<div id="notification-messages" style="border: 2px solid #34495e;" class="messages">
				</div>
			</div>-->
		</div>
	</div>

	<script type="text/x-handlebars-template" id="notification-message-template">
		{{#each notifications}}
			<a class="message">{{this}}</a>
		{{else}}
			<a class="message">You have no notifications</a>
		{{/each}}
	</script>

	<!-- Container for page messages -->
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
					<div>
						<!-- <i class="fa fa-briefcase"></i> -->
						<!-- <i class="fa fa-bullhorn"></i> -->
						<!-- <i class="fa fa-paper-plane"></i> -->
						<i class="fa fa-calendar fa-lg fa-fw"></i>
						<span>Calendar</span>
						<span class="caret"></span>
					</div>
					<ul id="calendar-submenu">
						<li>
							<a href="#calendar">Calendar</a>
						</li>
						<li>
							<a href="#scheduling">Scheduling</a>
						</li>
						<li>
							<a href="#pickup-schedule">Pick-Up Schedule</a>
						</li>
					</ul>
				</li>

				<li>
					<div>
						<i class="fa fa-paper-plane fa-lg fa-fw"></i>
						<span>CRM</span>
						<span class="caret"></span>
					</div>
					<ul id="crm-submenu">
						<li>
							<a href="#customers">My Customers</a>
						</li>
						<?php // if(gethostname() === 'rms.scubawhere.com') { ?>
							<!-- Additional CRM features will appear here -->
						<?php // } else { ?>
							<li>
								<a href="#mailing-lists">My Mailing Lists</a>
							</li>
							<li>
								<a href="#campaigns">My Campaigns</a>
							</li>
                            <!--<li>
								<a href="#automated-emails">Automated Emails</a>
							</li>-->
						<?php // } ?>
					</ul>
				</li>

				<li>
					<a href="#reports">
						<!-- <i class="fa fa-university"></i> -->
						<i class="fa fa-line-chart fa-lg fa-fw"></i>
						<!-- <i class="fa fa-usd"></i> -->
						<!-- <i class="fa fa-file-text-o"></i> -->
						<span>Reports</span>
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
							<a href="#add-ons">Add-ons</a>
						</li>
						<li>
							<a href="#agents">Agents</a>
						</li>
						<li>
							<a href="#boats">Boats</a>
						</li>
						<li>
							<a href="#classes">Classes</a>
						</li>
						<li>
							<a href="#courses">Courses</a>
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
							<a href="#trips">Trips (Open Water)</a>
						</li>
					</ul>
				</li>
                
                <!--<li>
					<div>
						<!-- <i class="fa fa-briefcase"></i> -->
						<!-- <i class="fa fa-bullhorn"></i> -->
						<!-- <i class="fa fa-paper-plane"></i> -->
						<!--<i class="fa fa-anchor fa-lg fa-fw"></i>
						<span>Rentals</span>
						<span class="caret"></span>
					</div>
					<ul id="equipment-submenu">
						<li>
							<a href="#equipment">Equipment</a>
						</li>
					</ul>
				</li>-->

				<li>
					<a href="https://scubawhere.zendesk.com" target="_blank">
						<i class="fa fa-life-ring fa-lg fa-fw"></i>
						<!-- <i class="fa fa-users"></i> -->
						<span>Support</span>
					</a>
				</li>

				<li>
					<a href="#settings">
						<i class="fa fa-cog fa-lg fa-fw"></i>
						<span>Settings</span>
					</a>
				</li>

                <li>
                    <a href="#logs">
                        <i class="fa fa-exclamation fa-lg fa-fw"></i>
                        <span>Logs</span>
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
