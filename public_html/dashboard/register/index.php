<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Operator Register</title>

	<script src="/common/js/jquery.js"></script>
	<script src="js/jquery.steps.min.js"></script>
	<script src="js/register.js"></script>


	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.steps.css">
</head>
<body>

	<div id="register-wrapper">
		<div class="container">
			<img src="/dashboard/common/img/ScubaWhere_logo.svg">
			<h1>Dive Operator Registration</h1>
			<form id="register-form" method="post" accept-charset="utf-8">
				<div id="steps">

					<h3>General Info</h3>
					<section>
						<div id="section1">
							<div class="register-col">
								<label for="contact">Main Person of Contact:</label>
								<input class="required"  type="text" id="contact" name="contact">

								<label for="phone">Main Contact Phone Number:</label>
								<input class="required"  type="text" id="phone" name="phone">

								<label for="email">Main Contact Email Address:</label>
								<input class="required"  type="text" id="email" name="email">

								<label for="agencies">Accepted Diving Instuitions</label>
								<div id="agencies">
									<div id="save-loader" class="loader"></div>
								</div>
							</div>
							<div class="register-col">
								<label for="name">Dive Operator Name:</label>
								<input class="required"  type="text" id="name" name="name">

								<label for="website">Dive Operator Website (optional):</label>
								<input type="text" id="website" name="website">

								<label for="description">Company Bio (optional):</label>
								<textarea style="width:102%" rows="5" id="description" name="description"></textarea>
							</div>
						</div>
					</section>

					<h3>Business Details</h3>
					<section>
						<div id="section2">
							<div class="register-col">
								<label for="address_1">Business Address Line 1:</label>
								<input class="required"  type="text" id="address_1" name="address_1">

								<label for="address_2">Business Address Line 2 (optional):</label>
								<input  type="text" id="address_2" name="address_2">

								<label for="city">City:</label>
								<input class="required"  type="text" id="city" name="city">

								<label for="county">County / State (optional):</label>
								<input type="text" id="county" name="county">

								<label for="postcode">Post Code / Zip Code:</label>
								<input class="required"  type="text" id="postcode" name="postcode">
							</div>
							<div class="register-col">
								<label for="business_phone">Business Phone Number:</label>
								<input class="required"  type="text" id="business_phone" name="business_phone">

								<label for="business_email">Business Email:</label>
								<input class="required"  type="text" id="business_email" name="business_email">

								<label for="registration_number">Business Registration Number (optional):</label>
								<input type="text" id="registration_number" name="registration_number">

								<label for="vat_number">VAT Number (optional):</label>
								<input type="text" id="vat_number" name="vat_number">

								<label for="country_id">Please select your country:</label>
								<select class="required" id="country_id" name="country_id">
									<option value="">Please select</option>
								</select>

								<label for="currency_id">Please select your currency:</label>
								<select class="required" id="currency_id" name="currency_id">
									<option value="">Please select</option>
								</select>
							</div>
						</div>
					</section>
					<h3>Username</h3>
					<section>
						<div id="section3">
							<div style="margin: 0 auto; text-align: center;">
								<h3>One more thing...</h3>
								<label for="username">Please enter your username:</label>
								<input style="width:280px; text-align:center; margin: 0 auto;" class="required"  type="text" id="username" name="username">

								<input type="submit" class="submit register-bttn bttn blueb" value="Create Account">
							</div>
						</div>
					</section>
					<h3>Done!</h3>
					<section>
						<div id="section4">
							<h2>Thank you for signing up with scubawhere!</h2>
							<p>
								Please check your main email to activate your account.
							</p>
						</div>
					</section>
				</div>
			</form>
		</div>

		<span><a href="/terms/">Terms</a> | <a href="/policy/">Policy</a></span>
	</div>

	<footer><a href="../login/" class="bttn" id="register">Log in</a></footer>
</body>
</html>


