<!DOCTYPE html>
<html>
<head>
	<title>Reset Password | scubawhereRMS</title>

	<link rel="stylesheet" type="text/css" href="{{ Config::get('app.rms_url') }}/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="{{ Config::get('app.rms_url') }}/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="{{ Config::get('app.rms_url') }}/dashboard/css/login-register.css">
</head>
<body>

	<div id="login-wrapper">

		<form action="{{ action('PasswordController@postRemind') }}" method="POST" id="login-form">

			<img src="{{ Config::get('app.rms_url') }}/common/img/ScubaWhere_logo.svg">
			<h1>Password Reset</h1>

			<?php
				if(isset($status))
				{
					echo '<p class="greenf">An email has been sent to your email address.</p><p>Please click the link in the email to set a new password.<p>';
				}
				else
				{
					if(isset($error)) {
						echo '<span class="form-error">'.$error.'</span>';
					}
			?>
				<small>Your email address:</small>
				<input type="email" name="email" required="required">

				<input type="submit" value="Send Reminder" class="btn btn-primary">
			<?php
				}
			?>
		</form>
	</div>
    <footer><a href="../dashboard/login/" class="bttn" id="register">Log in</a></footer>
</body>
</html>
