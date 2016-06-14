<!DOCTYPE html>
<html>
<head>
	<title>Registration | scubawhereRMS</title>

	<script type="text/javascript" src="/common/js/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="/common/js/jquery/jquery.steps.min.js"></script>
	<script type="text/javascript" src="/common/js/jquery/jquery.serialize-object.min.js"></script>
	<script type="text/javascript" src="/common/bootstrap/js/bootstrap.min.js"></script>

	<script type="text/javascript" src="/common/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="/common/ckeditor/adapters/jquery.js"></script>

	<script type="text/javascript" src="js/register.js"></script>


	<link rel="stylesheet" type="text/css" href="/common/css/jquery.steps.css">
	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-scubawhere.css" />
	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/css/login-register.css">

	<link rel="icon" type="image/ico" href="/common/favicon.ico" />
</head>
<body>

	<div id="wrapper" class="clearfix col-md-offset-2">
		<div id="register-wrapper" class="col-md-10">
			<img src="/common/img/scubawhere_logo.svg"></img>
			<h3 style="font-weight: 200;" id="page-title">Dive Operator Registration</h3>
			<form id="register-form" method="post" accept-charset="utf-8">
				<div id="steps">
					<h3>General Info</h3>
					<section>
						<div id="section1">
							<div class="col-md-6">
								<div class="form-row">
									<label for="contact">Main Person of Contact:</label>
									<input class="required"  type="text" id="contact" name="contact">
								</div>
								<div class="form-row">
									<label>Main Contact Phone Number:</label>
									<table>
										<tr>
											<td width="25%" style="padding: 0;">
												<small>Extension</small>
												<input style="box-sizing: border-box; width: 90%;" class="required" type="text" id="phone_ext" name="phone_ext" placeholder="+44">
											</td>
											<td width="75%" style="padding: 0;">
												<small>Phone Number</small>
												<input style="box-sizing: border-box;" class="required" type="text" id="phone" name="phone" placeholder="1234567890">
											</td>
										</tr>
									</table>
								</div>
								<div class="form-row">
									<label for="email">Main Contact E-mail Address:</label>
									<input class="required"  type="text" id="email" name="email">
								</div>
								<div class="form-row">
									<label for="agencies">Affiliated Training Organisations:</label>
									<div id="agencies" class="clearfix">
										<div id="save-loader" class="loader"></div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-row">
									<label for="name">Dive Operator Name:</label>
									<input class="required"  type="text" id="name" name="name">
								</div>
								<div class="form-row">
									<label for="website">Dive Operator Website (optional):</label>
									<input type="text" id="website" name="website">
								</div>
								<div class="form-row">
									<label for="description">Company Biography (optional):</label>
									<textarea style="width:102%" rows="5" id="description" name="description"></textarea>
								</div>
							</div>
						</div>
					</section>
					<h3>Business Details</h3>
					<section>
						<div id="section2">
							<div class="col-md-6">
								<div class="form-row">
									<label for="address_1">Business Address Line 1:</label>
									<input class="required"  type="text" id="address_1" name="address_1">
								</div>
								<div class="form-row">
									<label for="address_2">Business Address Line 2 (optional):</label>
									<input  type="text" id="address_2" name="address_2">
								</div>
								<div class="form-row">
									<label for="city">City:</label>
									<input class="required"  type="text" id="city" name="city">
								</div>
								<div class="form-row">
									<label for="county">County/ State (optional):</label>
									<input type="text" id="county" name="county">
								</div>
								<div class="form-row">
									<label for="postcode">Post Code/ Zip Code:</label>
									<input class="required"  type="text" id="postcode" name="postcode">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-row">
									<label>Business Phone Number:</label>
									<table>
										<tr>
											<td width="25%" style="padding: 0;">
												<small>Extension</small>
												<input style="box-sizing: border-box; width: 90%;" class="required" type="text" id="business_phone_ext" name="business_phone_ext" placeholder="+44">
											</td>
											<td width="75%" style="padding: 0;">
												<small>Business Phone Number</small>
												<input style="box-sizing: border-box;" class="required" type="text" id="business_phone" name="business_phone" placeholder="1234567890">
											</td>
										</tr>
									</table>
								</div>
								<div class="form-row">
									<label for="business_email">Business E-mail:</label>
									<input class="required"  type="text" id="business_email" name="business_email">
								</div>
								<div class="form-row">
									<label for="registration_number">Business Registration Number (optional):</label>
									<input type="text" id="registration_number" name="registration_number">
								</div>
								<div class="form-row">
									<label for="vat_number">VAT/ GST Number (optional):</label>
									<input type="text" id="vat_number" name="vat_number">
								</div>
								<div class="form-row">
									<label for="country_id">Select your Country:</label>
									<select class="required" id="country_id" name="country_id" style="width: 100%;">
										<option value="">Please select</option>
									</select>
								</div>
								<div class="form-row">
									<label for="currency_id">Select your Currency:</label>
									<select class="required" id="currency_id" name="currency_id" style="width: 100%;">
										<option value="">Please select</option>
									</select>
								</div>
							</div>
						</div>
					</section>
					<h3>Terms</h3>
					<section>
						<div id="section3">
							<div style="float:none; margin: 0 auto;" class="form-row">
								<!--<label for="terms">(Optional) Copy and paste your Customer Booking Terms and Conditions below:</label>-->
								<label for="terms">(Optional) Upload your terms and conditions as a pdf here:</label>
								<input type="file" id="in-terms-file">
							</div>
						</div>
					</section>
					<h3>Username</h3>
					<section>
						<div id="section4">
							<div class="form-row" style="text-align: center;">
								<h3>Just one more thing...</h3>

								<label for="username">Please create your Username:</label>
								<input style="margin: 0 auto; width:280px" class="required"  type="text" id="username" name="username">

								<div style="margin-top:10px"></div>

								<label for="our-terms">
									<small>Please check the box if you have read and agreed to our <a href="/api/terms" target="_blank">Terms and Conditions</a>:</small>
									<input type="checkbox" id="our-terms" name="our_terms" class="required" value="1">
								</label>

								<div style="margin-top:10px"></div>

								<input type="submit" style="text-align:center; margin: 0 auto;"  class="submit btn btn-success btn-lg text-uppercase" value="Create Account">
							</div>
						</div>
					</section>
				</div>
			</form>
			<span><a href="/api/terms" target="_blank">Terms & Conditions</a> <!--| <a href="/policy/">Policy</a>--></span>
		</div>
	</div>

	<footer><a href="/login/" class="bttn" id="register">Log in</a></footer>
</body>
</html>
