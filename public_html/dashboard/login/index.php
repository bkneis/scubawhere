<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Centre Login</title>


	<script src="/common/js/jquery.js"></script>
	<script src="/common/js/ui.min/jquery-ui.min.js"></script>

	<script src="/dashboard/login/js/login.js"></script>

	<link rel="stylesheet" type="text/css" href="css/styles.css">

	<link rel="icon" type="image/ico" href="../../common/favicon.ico" />
</head>
<body>

	<div id="login-wrapper">

		<form action="#" id="loginForm" method="post" accept-charset="utf-8">
			<img src="/dashboard/common/img/ScubaWhere_logo.svg">
			<h1>Dive Centre Dashboard Login</h1>

			<span class="form-error"></span>

			<input type="text" name="username" placeholder="Username">

			<input type="password" name="password" placeholder="Password"><br>

			<label><input type="checkbox" name="remember"> Remember me</label>

			<input type="hidden" name="_token" value="" />
			<button id="loginDC" class="submit-bttn bttn blueb">
				Log in
				<div id="save-loader" class="loader"></div>
			</button>

		</form>
		<span>Forgot your password? <a href="../../password/remind">Click here.</a></span>
	</div>

    <footer><a href="../register/" class="bttn" id="register">Register Your Dive Centre</a></footer>

</body>
</html>
