<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Centre Login</title>


	<script src="/dashboard/common/js/jquery/jquery.min.js"></script>
	<script src="/dashboard/common/js/jquery/ui.min/jquery-ui.min.js"></script>

	<script type="text/javascript" src="/dashboard/js/Repositories/CompanyRepo.js"></script>
	<script type="text/javascript" src="/dashboard/js/RegisterService.js"></script>
	<script src="/dashboard/js/register.js"></script>

	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="/dashboard/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/dashboard/css/login-register.css">

	<link rel="icon" type="image/ico" href="/dashboard/common/favicon.ico" />
</head>
<body>

	<div id="login-wrapper">
		<div id="login-form">
			<form action="#" id="registerForm" method="post" accept-charset="utf-8">
				<img src="/dashboard/common/img/scubawhere_logo.svg">
				<h1>RMS Operator Sign Up</h1>

				<span class="form-errors"></span>

				</br>
				<label for="contact">Personal Contact Name</label>
				<input style="width:280px;" type="text" name="contact" placeholder="Personal Contact Name" required>
				<label for="contact">Email</label>
				<input style="width:280px;" type="text" name="email" placeholder="Email" required>
				<label for="contact">Dive Operator Name</label>
				<input style="width:280px;" type="text" name="name" placeholder="Dive Operator Name" required>
				<label for="contact">Phone number</label>
				<input style="width:280px;" type="text" name="phone" placeholder="Phone Number (Optional)">
				<label for="contact">Username</label>
				<input style="width:280px;" type="text" name="username" placeholder="Username" required>
				<label for="contact">Password</label>
				<input style="width:280px;" type="password" name="password" placeholder="Password" required>
				<label for="contact">Re enter password</label>
				<input style="width:280px;" type="password" name="repassword" placeholder="Re Enter Password" required>
				<label for="source">Where did you hear about us?</label>
				<select name="source">
					<option value="website">scubawhere website</option>
					<option value="google">Google</option>
					<option value="social-media">Social Media</option>
					<option value="contact">Direct Contact</option>
				</select>

				<p style="font-size:14px; width:280px; font-style: italic;">
					By signing up, you agree to our 
					<a target="_blank" href="/api/terms">terms of use</a> 
				</p>				

				<input type="hidden" name="_token" value="" />
				<button id="loginDC" class="btn btn-primary">
					Register
					<div id="save-loader" class="loader"></div>
				</button>

				<a style="font-size:14px; font-style:italic" href="/dashboard/login">Already signed up? Login here</a>

			</form>
		</div>

	</div>

</body>
</html>
