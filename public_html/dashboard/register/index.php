<!DOCTYPE HTML>
<HTML>
<HEAD>
	<TITLE>Scuba Where | Dive Centre Register</TITLE>
		
	<script data-main="js/config" src="/common/js/jquery.js"></script>
	<script data-main="js/config" src="/common/js/ui.min/jquery-ui.min.js"></script>
	
	<script src="/dashboard/register/js/register.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/styles.css">
</HEAD>
<BODY>

	
	<div id="login-wrapper">  
		<img src="/dashboard/common/img/dc-hdr-logo.png"   />
		   
		<form action="login" method="post" id="regForm" accept-charset="utf-8">
			<h1>Dive Centre Dashboard Register</h1>
			
			<span id="form-error"></span>
			
			<input class="required"  type="text" name="username" placeholder="Username" />
				   
			<input class="required"  type="text" name="name" placeholder="Dive Centre Name" />
				   
			<input class="required"  type="text" name="email" placeholder="Email Address" />
				   
			<input class="required"  type="text" name="address_1" placeholder="Address" />
				   
			<input class="required"  type="text" name="address_2" placeholder="Address" />
				   
			<input class="required"  type="text" name="city" placeholder="City" />
				   
			<input class="required"  type="text" name="county" placeholder="County" />
				   
			<input class="required"  type="text" name="postcode" placeholder="Postcode" />
			
			<div class="select">
				<select class="required" name="region_id" id="">
					<option value="1">South West</option>
				</select>
			</div>
			
			<div class="select">
				<select class="required" name="country_id" id="">
					<option value="1">England</option>
				</select>
			</div>			
			
			<input class="required" type="text" name="phone" placeholder="Phone Number" />
			
			<input type="text" name="website" placeholder="Website (optional)" />			
				
			<input type="submit" id="regSubmit" value="Register" class="bttn blueb" />
			
			<input type="hidden" name="_token" value="" />
			
			
			
		</form>
		<span><a href="/terms/">Terms</a> | <a href="/policy/">Policy</a></span>
	</div>
    
    <footer><a href="../register/" class="bttn" id="register">Log in</a></footer>
    
</BODY>
</HTML>