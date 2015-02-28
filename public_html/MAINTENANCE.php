<?php
header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 600"); // 10 minutes
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<title>scubawhere | We'll be back shortly</title>
	<meta name="robots" content="none">

	<style type="text/css">
		body {
			text-align: center;
			font-family: "Helvetica", "Helvetica Neue", Arial, sans-serif;
			font-size: 18px;
			line-height: 1.5;
			color: #444;
		}
		#container {
			max-width: 500px;
			margin: 100px auto;
		}
	</style>
</head>
<body>
	<div id="container">
		<img src="http://scubawhere.com/dashboard/common/img/scubawhere_logo@2x.png" alt="scubawhere logo" style="width: 293px;">
		<h1>Sorry, we'll be back shortly!</h1>
		<p>
			We are currently updating our website and will be back in a few minutes. Thank you for your understanding.
		</p>
		<p>
			You can contact us at <a href="mailto:hello@scubawhere.com">hello@scubawhere.com</a>.
		</p>
		<p>
			<small><small><code>HTTP 503 Service Temporarily Unavailable</code></small></small>
		</p>
	</div>
</body>
</html>
