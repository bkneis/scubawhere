<!DOCTYPE html>
<html>
<head>
	<title>Reset Password | Scuba Where</title>

	<link rel="stylesheet" type="text/css" href="../dashboard/login/css/styles.css">
</head>
<body>

	<div id="login-wrapper">

		<form action="{{ action('PasswordController@postRemind') }}" method="POST">

			<img src="../dashboard/common/img/ScubaWhere_logo.svg">
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
				<label>Email address</label>
				<input type="email" name="email" required="required">
				<br>
				<br>
				<input type="submit" value="Send Reminder" class="bttn big-bttn blueb">
			<?php
				}
			?>
		</form>
	</div>
    <footer><a href="../dashboard/login/" class="bttn" id="register">Log in</a></footer>
</body>
</html>
