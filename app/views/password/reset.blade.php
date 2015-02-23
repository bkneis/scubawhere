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
				<input type="password" name="password" required="required" placeholder="6 characters min" style="width: 280px;"><br>

				<small>Confirm new password</small>
				<input type="password" name="password_confirmation" required="required" style="width: 280px;">

				<input type="submit" value="Set Password" class="btn btn-primary">
			<?php
				}
			?>
		</form>
	</div>
    <footer><a href="/login/" class="bttn" id="register">Log in</a></footer>
</body>
</html>
