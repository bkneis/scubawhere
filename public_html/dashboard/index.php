<?php
// Thiis comment will be removed next commit, it is just to test the new slack notifications
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
		header("Location: " . $BASE_URL . "/dashboard/login/");
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
		header("Location: " . $BASE_URL . "/dashboard/login/");
		exit();
	}
    $ch = curl_init($BASE_URL . '/api/company/log');
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );
    $result = curl_exec( $ch );
    curl_close( $ch );
?>
<!DOCTYPE html>
<html>
<head>

	<title>scubawhereRMS | Dashboard</title>

	<!-- favicon -->
	<link rel="icon" type="image/ico" href="/dashboard/common/favicon.ico" />

	<!--Bootstrap CSS-->
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/bootstrap-scubawhere.css" />
	<!--<link rel="stylesheet" type="text/css" href="/common/bootstrap/css/bootstrap.min.css" />-->

	<!-- scubawhere styles -->
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/universal-styles.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/css/style.css" />

	<!-- Plugins -->
	<link rel='stylesheet' type="text/css" href='/dashboard/common/css/fullcalendar.min.css' />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/jquery.reveal.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/vendor/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/bootstrap-datetimepicker.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/select2.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/select2-bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/introjs.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/vendor/nprogress/nprogress.css" />
	<!--<link rel="stylesheet" type="text/css" href="/common/css/bootstrap.datatables.css" />
	<link rel="stylesheet" type="text/css" href="/common/vendor/datatables-tabletools/css/dataTables.tableTools.css" />-->
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/dataTables.bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/fixedColumns.dataTables.min.css" />

	<link rel="stylesheet" type="text/css" href="/dashboard/css/nav.css">

	<!-- jQuery -->
	<script src="/dashboard/common/js/jquery/jquery-1.12.3.min.js"></script>

	<!--Bootstrap js-->
	<script type="text/javascript" src="/dashboard/common/bootstrap/js/bootstrap.min.js"></script>

	<!-- other -->
	<!--<script type="text/javascript" src="/common/js/handlebars.min.js"></script>-->
	<script type="text/javascript" src="/dashboard/common/vendor/handlebars/handlebars.min.js"></script>
	<script type="text/javascript" src="/dashboard/common/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="/dashboard/common/ckeditor/adapters/jquery.js"></script>
	<script type="text/javascript" src="/dashboard/common/js/moment.min.js"></script>
	<script type="text/javascript" src="/dashboard/common/js/underscore-min.js"></script>
	<script type="text/javascript" src="/dashboard/common/js/jquery/jquery.serialize-object.min.js"></script>
	<script type="text/javascript" src="/dashboard/common/js/jquery/jquery.reveal.js"></script>

	<!--Datetimepicker-->
	<script type="text/javascript" src="/dashboard/common/js/bootstrap-datetimepicker.min.js"></script>

	<!--Select 2-->
	<script type="text/javascript" src="/dashboard/common/js/select2.min.js"></script>

	<!--nprogress bar-->
	<script type="text/javascript" src="/dashboard/common/vendor/nprogress/nprogress.js"></script>

	<!-- Datatables -->
	<!--<script type="text/javascript" src="/common/js/jquery/jquery.datatables.min.js"></script>
	<script type="text/javascript" src="/common/js/bootstrap.datatables.js"></script>
	<script type="text/javascript" src="/common/vendor/datatables-tabletools/js/dataTables.tableTools.js"></script>-->
	<script type="text/javascript" src="/dashboard/common/js/datatables.min.js"></script>
	<script src="/dashboard/common/js/dataTables.fixedColumns.min.js"></script>

	<script src="/dashboard/common/js/bootbox.min.js"></script>

	<!-- testing with -->
	<script src="/dashboard/js/vue.js"></script>
	<script src="/dashboard/js/components/EventHub.js"></script>
	<script src="/dashboard/js/components/modal.js"></script>

	<!-- scubawhere files -->
	<script type="text/javascript">
		// Set scubawhere namespace
		window.sw = {};
		window.promises = {};
		window.facebook = {};
	</script>

	<?php
		if (isset($_SERVER['AWS_ENV'])) {
			if ($_SERVER['AWS_ENV'] === 'prod') {
				echo <<<EOT
<script type="text/javascript">
    window.heap=window.heap||[],heap.load=function(e,t){window.heap.appid=e,window.heap.config=t=t||{};var r=t.forceSSL||"https:"===document.location.protocol,a=document.createElement("script");a.type="text/javascript",a.async=!0,a.src=(r?"https:":"http:")+"//cdn.heapanalytics.com/js/heap-"+e+".js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n);for(var o=function(e){return function(){heap.push([e].concat(Array.prototype.slice.call(arguments,0)))}},p=["addEventProperties","addUserProperties","clearEventProperties","identify","removeEventProperty","setEventProperties","track","unsetEventProperty"],c=0;c<p.length;c++)heap[p[c]]=o(p[c])};
      heap.load("2024590828");
</script>
EOT;
			} elseif ($_SERVER['AWS_ENV'] === 'dev') {
				echo <<<EOT
<script type="text/javascript">
    window.heap=window.heap||[],heap.load=function(e,t){window.heap.appid=e,window.heap.config=t=t||{};var r=t.forceSSL||"https:"===document.location.protocol,a=document.createElement("script");a.type="text/javascript",a.async=!0,a.src=(r?"https:":"http:")+"//cdn.heapanalytics.com/js/heap-"+e+".js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(a,n);for(var o=function(e){return function(){heap.push([e].concat(Array.prototype.slice.call(arguments,0)))}},p=["addEventProperties","addUserProperties","clearEventProperties","identify","removeEventProperty","setEventProperties","track","unsetEventProperty"],c=0;c<p.length;c++)heap[p[c]]=o(p[c])};
      heap.load("2640758810");
</script>
EOT;
			}
		}
	?>

    <script type="text/javascript" src="/dashboard/js/main.js"></script>
    <script type="text/javascript" src="/dashboard/js/navigation.js"></script>
    <!--<script type="text/javascript" src="js/tour.js"></script>-->
    <script type="text/javascript" src="/dashboard/js/ui.js"></script>
    <script type="text/javascript" src="/dashboard/js/validate.js"></script>

    <!-- Load all front-end controllers -->
    <script type="text/javascript" src="/dashboard/js/Controllers/Accommodation.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Addon.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Agency.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Agent.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Boat.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Boatroom.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Booking.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Class.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Company.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Course.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Customer.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Location.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Package.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Payment.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Refund.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Report.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Session.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Ticket.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Timetable.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Trip.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/CustomerGroup.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Campaign.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Certificate.js"></script>
    <script type="text/javascript" src="/dashboard/js/Controllers/Equipment.js"></script>

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
<body id="app">
	<div id="nav">
		<div id="nav-wrapper">
			<h1 id="logo"><a href="/"><img src="/dashboard/common/img/Scubawhere_logo.png"></a></h1>

			<!--<button class="btn btn-default pull-right" id="logout">Logout</button>-->

			<!--<div class="nav-opt pull-right"><a href="#settings" class="username"></a></div>-->

			<div class="pull-right">
				<!--<companies-list></companies-list>-->
			</div>

			<companies-list></companies-list>

		</div>
	</div>

	<template type="text/x-template" id="companies-list">
		<div class="dropdown pull-right"
			 style="margin-top: 5px;"
			 v-if="companiesLoaded">
			<span class="dropbtn">
				<i class="fa fa-user" style="padding-right: 5px;"></i>
				{{currentCompany.name}}
				<i style="padding-left: 5px; padding-right: 10px;" class="fa fa-caret-down" aria-hidden="true"></i>
			</span>
			<div id="myDropdown" class="dropdown-content" style="margin-top: 10px;">
				<span v-if="companies.length > 1">
					<a href="#">Switch to : </a>
					<a v-for="company in companies"
					   v-if="company.id !== selectedCompany"
					   @click="switchCompany(company.id)">{{company.name}}</a>
					<hr>
				</span>
				<a href="#settings">Settings</a>
				<a href="/api/logout" @click="logout()">Logout</a>
			</div>
		</div>
	</template>

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
						<span>Add Booking</span>
					</a>
				</li>

				<li>
					<a href="#manage-bookings">
						<i class="fa fa-pencil fa-lg fa-fw"></i>
						<span>Manage Bookings</span>
					</a>
				</li>

				<li>
					<a href="#availability">
						<i class="fa fa-calendar-o fa-lg fa-fw"></i>
						<span>Availability</span>
					</a>
				</li>

				<li>
					<div>
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
						<li>
							<a href="#mailing-lists">My Mailing Lists</a>
						</li>
						<li>
							<a href="#campaigns">My Campaigns</a>
						</li>
					</ul>
				</li>

				<li>
					<a href="#reports">
						<i class="fa fa-line-chart fa-lg fa-fw"></i>
						<span>Reports</span>
					</a>
				</li>

				<li>
					<div>
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
							<a href="#trips">Trips</a>
						</li>
					</ul>
				</li>

				<!--<li>
					<a href="https://scubawhere.zendesk.com" target="_blank">
						<i class="fa fa-life-ring fa-lg fa-fw"></i>
						<span>Support</span>
					</a>
				</li>-->

				<li>
					<div>
						<i class="fa fa-cog fa-lg fa-fw"></i>
						<span>Settings</span>
						<span class="caret"></span>
					</div>
					<ul id="crm-submenu">
						<li>
							<a href="#settings">Account</a>
						</li>
						<li>
							<a href="#settings-users">Users</a>
						</li>
					</ul>
				</li>

				<!--<li>
					<a href="#settings">
						<i class="fa fa-cog fa-lg fa-fw"></i>
						<span>Settings</span>
					</a>
				</li>-->

                <li>
                    <a href="#troubleshooting">
                        <i class="fa fa-exclamation fa-lg fa-fw"></i>
                        <span>Troubleshooting</span>
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

<script type="text/javascript" src="/dashboard/js/Repositories/UserRepo.js"></script>
<script type="text/javascript" src="/dashboard/js/components/companies-list.js"></script>
<script type="text/javascript" src="/dashboard/js/main2.js"></script>
