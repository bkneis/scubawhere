<!DOCTYPE HTML>
<HTML>
<HEAD>
	<TITLE>Scuba Where | Dive Centre Login</TITLE>
	
	
	<script src="/common/js/jquery.js"></script>
	<script src="/common/js/ui.min/jquery-ui.min.js"></script>
	
	<script src="/dashboard/login/js/login.js"></script>
	
	<link rel="stylesheet" type="text/css" href="css/styles.css">
</HEAD>
<BODY>

	
	<div id="login-wrapper">  
		<img src="/dashboard/common/img/dc-hdr-logo.png"   />
		   
		<form action="/login" id="loginForm" method="post" accept-charset="utf-8">
			<h1>Dive Centre Dashboard Login</h1>
			
			<span id="form-error"></span>
			
			<input type="text" name="username" placeholder="Username" />
	
			<input type="password" name="password" placeholder="Password" />
			
			<input type="hidden" name="_token" value="" />
				
			<input type="submit" id="loginDC" value="Log in" class="bttn blueb">
			
		</form>
		<span>Forgot your password? <a href="../forgot/">Click here.</a></span>
	</div>
    
    <footer><a href="../register/" class="bttn" id="register">Register Your Dive Centre</a></footer>
    
</BODY>
</HTML>