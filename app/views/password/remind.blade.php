<?php
if(Session::get('status'))
{
	echo 'An email has been sent to your email address. Please click the link in the email to set a new password.';
}
else
{
	?>
	<h1>Reset your password:</h1>

	{{ Session::get('error') or '' }}

	<form action="{{ action('PasswordController@postRemind') }}" method="POST">
		Email: <input type="email" name="email" required="required"><br>
		<input type="submit" value="Send Reminder">
	</form>
	<?php
}
