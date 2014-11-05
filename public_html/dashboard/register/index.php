<!DOCTYPE html>
<html>
<head>
	<title>Scuba Where | Dive Operator Register</title>

	<script data-main="js/config" src="/common/js/jquery.js"></script>
	<script data-main="js/config" src="/common/js/ui.min/jquery-ui.min.js"></script>
	<script src="/dashboard/register/js/jquery.steps.js"></script>
	<script src="/dashboard/register/js/jquery.steps.min.js"></script>
	<script src="/dashboard/register/js/register.js"></script>
	

	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<link rel="stylesheet" href="css/jquery.steps.css">
</head>
<body>

	<div id="register-wrapper">
	<div class="container">
		<!--<form action="#" method="post" id="regForm" accept-charset="utf-8">-->
			<img src="/dashboard/common/img/ScubaWhere_logo.svg">
			<h1>Dive Operator Dashboard Register</h1>
		<div id="example-vertical">
			<h3>General Info</h3>
			<section>
			<div id="section1">
				<div style="width:45%; float:left; padding-right:20px; text-align:left;">
					<label for="contactName">Main Person of Contact:</label>
					<input style="width:100%" class="required"  type="text" id="contactName" name="contactName">
					<label for="phone">Main Contact Phone Number:</label>
					<input style="width:100%" class="required"  type="text" id="phone" name="phone">
					<label for="email">Main Contact Email Address:</label>
					<input style="width:100%" class="required"  type="text" id="email" name="email">
					<label for="padi">Accepted Diving Instuitions</label>
					<div id="agencies">
					</div>
				</div>
				<div style="width:45%; float:left; padding-left:20px; text-align:left">
					<label for="name">Dive Operator Name:</label>
					<input style="width:100%" class="required"  type="text" id="name" name="name">
					<label for="name">Dive Operator Website (optional):</label>
					<input style="width:100%" type="text" id="website" name="website">
					<label for="description">Company Bio (Optional):</label>
					<textarea style="width:102%" rows="5" id="description" name="description"></textarea>
				</div>
				</div>
			</section>
			<h3>Business Details</h3>
			<section>
			<div id="section2">
				<div style="width:45%; float:left; padding-right:20px; text-align:left">
					<label for="addr1">Business Address Line 1:</label> <!-- make address 2 and county-->
					<input style="width:100%" class="required"  type="text" id="addr1" name="addr1">
					<label for="addr2">Business Address Line 2 (Optional):</label> <!-- make address 2 and county-->
					<input style="width:100%"  type="text" id="addr2" name="addr2">
					<label for="city">City:</label>
					<input style="width:100%" class="required"  type="text" id="city" name="city">
					<label for="county">County / State:</label>
					<input style="width:100%" type="text" id="county" name="county">
					<label for="postCode">Post Code / Zip Code:</label>
					<input style="width:100%" class="required"  type="text" id="postCode" name="postCode">
				</div>
				<div style="width:45%; float:left; padding-left:20px; text-align:left">
					<label for="businessPhone">Business Phone Number:</label>
					<input style="width:100%" class="required"  type="text" id="businessPhone" name="businessPhone">
					<label for="businessEmail">Business Email:</label>
					<input style="width:100%" class="required"  type="text" id="businessEmail" name="businessEmail">
					<label for="regNumber">Business Registration Number:</label>
					<input style="width:102%" type="text" id="regNumber" name="regNumber">
					<label for="vatNumber">VAT Number:</label>
					<input style="width:100%" type="text" id="vatNumber" name="vatNumber">
					<label for="countries">Please select your country:</label>
					 <select id="countries" class="required" style="width:107%">
					  <option value=""></option>
					  <option value="1">Remove this</option>
					</select>  
					 <label for="currencies">Please select your currency:</label>
					 <select id="currencies" class="required" style="width:107%">
					  <option value=""></option>
					</select>
				</div>
				</div>
			</section>
			<h3>Activation</h3>
			<section>
			<div id="section3">
			<div style="margin: 0 auto; text-align: center;">
				<h3>Just one more thing...</h3>
				<label for="username">Please enter your username:</label>
				<input style="width:280px; text-align:center;" class="required"  type="text" id="username" name="username">
				<button id="loginDC" onclick="submitForm()" class="submit-bttn bttn blueb">
				Create Account
				<!--<div id="save-loader" class="loader"></div>-->
			</button>

			</div>
			</div>
			</section>
		</div>
		<!--</form>-->
		</div>
		<span><a href="/terms/">Terms</a> | <a href="/policy/">Policy</a></span>
	</div>

	<footer><a href="../login/" class="bttn" id="register">Log in</a></footer>

</body>
</html>


