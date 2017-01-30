<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Centre Login</title>


	<script src="/dashboard/common/js/jquery/jquery.min.js"></script>
	<script src="/dashboard/common/js/jquery/ui.min/jquery-ui.min.js"></script>

	<script src="/dashboard/login/js/login.js"></script>

	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/dashboard/css/login-register.css">

	<link rel="icon" type="image/ico" href="/dashboard/common/favicon.ico" />
</head>
<body>

	<div id="login-wrapper">
		<div id="login-form">
			<form action="#" id="loginForm" method="post" accept-charset="utf-8">
				<img src="/dashboard/common/img/scubawhere_logo.svg">
				<h1>RMS Operator Login</h1>

				<span class="form-error"></span>

				<input style="width:280px;" type="text" name="username" placeholder="Username">

				<input style="width:280px;" type="password" name="password" placeholder="Password"><br>

				<!-- <label><input type="checkbox" name="remember"> Remember me</label> -->

				<input type="hidden" name="_token" value="" />
				<button id="loginDC" class="btn btn-primary">
					Log in
					<div id="save-loader" class="loader"></div>
				</button>

				<a href="/dashboard/register/" class="btn btn-success" id="register">Register Your Dive Centre</a>

			</form>
		</div>

		<a href="/api/password/remind" class="forgot-password">Forgot your password?</a>
	</div>

    <!--<footer><a href="/register/" class="btn btn-primary btn-lg" id="register">Register Your Dive Centre</a></footer>-->

</body>
</html>
