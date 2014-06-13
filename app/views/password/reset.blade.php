<!DOCTYPE html>
<html>
<head>
	<title>New Password | Scuba Where</title>

	<link rel="stylesheet" type="text/css" href="../dashboard/login/css/styles.css">
</head>
<body>

	<div id="login-wrapper">

		<form action="{{ action('PasswordController@postReset') }}" method="POST">

			<img src="../dashboard/common/img/ScubaWhere_logo.svg">
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

				<label>New password</label>
				<input type="password" name="password" required="required" placeholder="6 characters min">

				<label>Confirm new password</label>
				<input type="password" name="password_confirmation" required="required">
				<br>
				<br>
				<input type="submit" value="Set Password" class="bttn big-bttn blueb">
			<?php
				}
			?>
		</form>
	</div>
    <footer><a href="../dashboard/login/" class="bttn" id="register">Log in</a></footer>
</body>
</html>
