<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Centre Register</title>

	<script data-main="js/config" src="/common/js/jquery.js"></script>
	<script data-main="js/config" src="/common/js/ui.min/jquery-ui.min.js"></script>

	<script src="/dashboard/register/js/register.js"></script>

	<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>

	<div id="login-wrapper">

		<form action="#" method="post" id="regForm" accept-charset="utf-8">
			<img src="/dashboard/common/img/ScubaWhere_logo.svg">
			<h1>Dive Centre Dashboard Register</h1>

			<span class="form-error"></span>

			<label>Username*</label>
			<input class="required"  type="text" name="username" placeholder="Username">

			<label>Dive Centre Name*</label>
			<input class="required"  type="text" name="name" placeholder="Dive Centre Name">

			<label>Email Address*</label>
			<input class="required"  type="text" name="email" placeholder="Email Address">

			<label>Address Line 1*</label>
			<input class="required"  type="text" name="address_1" placeholder="Address">

			<label>Address Line 2</label>
			<input type="text" name="address_2" placeholder="Address">

			<label>City*</label>
			<input class="required"  type="text" name="city" placeholder="City">

			<label>Postcode*</label>
			<input class="required"  type="text" name="postcode" placeholder="Postcode">

			<label>Country*</label>
			<select class="required" name="country_id" id="country-select"></select>

			<label>Phone*</label>
			<input class="required" type="text" name="phone" placeholder="Phone Number">

			<label>Website (optional)</label>
			<input type="text" name="website" placeholder="http://...">

			<br><span class="form-error"></span>

			<input type="hidden" name="_token" value="">
			<button id="regSubmit" class="bttn blueb register-bttn">
				Register
				<div id="save-loader" class="loader"></div>
			</button>


		</form>
		<span><a href="/terms/">Terms</a> | <a href="/policy/">Policy</a></span>
	</div>

    <footer><a href="../login/" class="bttn" id="register">Log in</a></footer>

</body>
</html>
