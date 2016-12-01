<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Centre Login</title>


	<script src="/common/js/jquery/jquery.min.js"></script>
	<script src="/common/js/jquery/ui.min/jquery-ui.min.js"></script>

	<script type="text/javascript" src="/js/Repositories/CompanyRepo.js"></script>
	<script type="text/javascript" src="/js/RegisterService.js"></script>
	<script src="/js/register.js"></script>

	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/css/login-register.css">

	<link rel="icon" type="image/ico" href="/common/favicon.ico" />
</head>
<body>

	<div id="login-wrapper">
		<div id="login-form">
			<form action="#" id="registerForm" method="post" accept-charset="utf-8">
				<img src="/common/img/scubawhere_logo.svg">
				<h1>RMS Operator Sign Up</h1>

				<span class="form-errors"></span>

				<input style="width:280px;" type="text" name="contact" placeholder="Personal Contact Name" required>

				<input style="width:280px;" type="text" name="email" placeholder="Email" required>

				<input style="width:280px;" type="text" name="name" placeholder="Dive Operator Name" required>

				<input style="width:280px;" type="text" name="phone" placeholder="Phone Number (Optional)">

				<input style="width:280px;" type="text" name="username" placeholder="Username" required>

				<input style="width:280px;" type="password" name="password" placeholder="Password" required>

				<input style="width:280px;" type="password" name="repassword" placeholder="Re Enter Password" required>

				<p style="font-size:14px; width:280px; font-style: italic;">
					By signing up, you agree to our 
					<a target="_blank" href="/api/terms">terms of use</a> 
				</p>				

				<input type="hidden" name="_token" value="" />
				<button id="loginDC" class="btn btn-primary">
					Register
					<div id="save-loader" class="loader"></div>
				</button>

				<a style="font-size:14px; font-style:italic" href="/login">Already signed up? Login here</a>

			</form>
		</div>

	</div>

</body>
</html>
