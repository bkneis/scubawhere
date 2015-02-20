<!DOCTYPE html>
<html>
<head>
	<title>New Password | scubawhereRMS</title>

	<link rel="stylesheet" type="text/css" href="{{ Config::get('app.rms_url') }}/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="{{ Config::get('app.rms_url') }}/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="{{ Config::get('app.rms_url') }}/css/login-register.css">
</head>
<body>

	<div id="login-wrapper">

		<form action="{{ action('PasswordController@postReset') }}" method="POST" id="login-form">

			<img src="{{ Config::get('app.rms_url') }}/common/img/ScubaWhere_logo.svg">
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
				<input type="password" name="password" required="required" placeholder="6 characters min"><br>

				<small>Confirm new password</small>
				<input type="password" name="password_confirmation" required="required">

				<input type="submit" value="Set Password" class="btn btn-primary">
			<?php
				}
			?>
		</form>
	</div>
    <footer><a href="{{ Config::get('app.rms_url') }}/login/" class="bttn" id="register">Log in</a></footer>
</body>
</html>
