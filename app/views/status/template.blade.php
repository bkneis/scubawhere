<!DOCTYPE html>
<!-- http://getbootstrap.com/examples/jumbotron-narrow/ -->
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Scubawhere.com status and response times">
	<link rel="icon" href="/common/favicon.ico">

	<title>Scubawhere.com status page</title>

	<!-- Bootstrap core CSS -->
	<link href="/common/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="/common/bootstrap/themes/jumbotron-narrow.css" rel="stylesheet">

	<!-- Font Awesome CSS -->
	<link href="/common/css/font-awesome/css/font-awesome.min.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<style>
		.daily-average {
			float: left;
			width: 82px;
			margin-right: 15px;
		}
		.daily-average:last-child {
			margin-right: 0;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<nav>
			<ul class="nav nav-pills pull-right">
				<li role="presentation"><a href="javascript:window.location.reload();">Reload</a></li>
			</ul>
		</nav>
		<h3 class="text-muted"><img src="/common/img/scubawhere_logo.svg" style="height: 20px; vertical-align: baseline;"> Status Page</h3>
		</div>

		<div class="row marketing">
			@yield('content')
		</div>

		<footer class="footer">
		<p>© Scubawhere Ltd.</p>
		</footer>
	</div> <!-- /container -->

	@yield('scripts')

	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="/common/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
