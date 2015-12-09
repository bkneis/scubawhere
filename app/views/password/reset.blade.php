<!DOCTYPE html>
<html>
<head>
	<title>New Password | scubawhereRMS</title>

	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/css/login-register.css">
</head>
<body>

	<div id="login-wrapper">

		<form action="{{ action('PasswordController@postReset') }}" method="POST" id="login-form">

			<img src="/common/img/scubawhere_logo.svg">
			<h1>Enter a new password for</h1>
			<pre>{{ $email }}</pre>

			<?php
				if(isset($status))
				{
					echo '<p class="greenf">'.$status.'<p>';
				}
				else
				{
					if(isset($error)) {
						echo '<span class="form-error">'.$error.'</span>';
					}
			?>

				<input type="hidden" name="token" value="{{ $token }}">
				<input type="hidden" name="email" value="{{ $email }}">

				<small>New password</small>
				<input type="password" name="password" id="password" required="required" placeholder="6 characters min" style="width: 280px;">

				<div class="password-meter" id="password-meter">
					<span class="meter-1"></span>
					<span class="meter-2"></span>
					<span class="meter-3"></span>
					<span class="meter-4"></span>
				</div>

				<small>Confirm new password</small>
				<input type="password" name="password_confirmation" required="required" style="width: 280px;">

				<input type="submit" value="Set Password" class="btn btn-primary">
			<?php
				}
			?>
		</form>
		<small class="forgot-password pull-right">Password meter: <a href="https://github.com/dropbox/zxcvbn" target="_blank"><b>zxcvbn</b> by Dropbox</a></small>
	</div>
    <footer><a href="/login/" class="bttn" id="register">Log in</a></footer>
    <script type="text/javascript" src="/common/js/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/common/js/zxcvbn.js"></script>
    <script type="text/javascript">
    	var passwordField = $('#password');
    	var passwordMeter = $('#password-meter');

    	function meterPassword() {
    		passwordMeter.removeClass('score-1 score-2 score-3 score-4').addClass('score-' + zxcvbn( passwordField.val() ).score);
    	}

    	passwordField.on('keyup', meterPassword);
    </script>
</body>
</html>
