<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Centre Login</title>


	<script src="/common/js/jquery/jquery.min.js"></script>
	<script src="/common/js/jquery/ui.min/jquery-ui.min.js"></script>

	<script src="js/login.js"></script>

	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/dashboard/css/login-register.css">

	<link rel="icon" type="image/ico" href="/common/favicon.ico" />
</head>
<body>

	<div id="login-wrapper">
		<div id="login-form">
			<form action="#" id="loginForm" method="post" accept-charset="utf-8">
				<img src="/common/img/scubawhere_logo.svg">
				<h1>RMS Operator Login</h1>

				<span class="form-error"></span>

				<input type="text" name="username" placeholder="Username">

				<input type="password" name="password" placeholder="Password"><br>

				<!-- <label><input type="checkbox" name="remember"> Remember me</label> -->

				<input type="hidden" name="_token" value="" />
				<button id="loginDC" class="btn btn-primary">
					Log in
					<div id="save-loader" class="loader"></div>
				</button>

			</form>

				<a href="../../password/remind">Forgot your password?</a>
		</div>
	</div>

    <footer><a href="../register/" class="bttn" id="register">Register Your Dive Centre</a></footer>

</body>
</html>
