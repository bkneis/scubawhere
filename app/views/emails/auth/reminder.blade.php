<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Password Reset</h2>
		<p>
			To create your new password, {{ HTML::link('scubawhere.com/companypasswordreset?email=' . $user->email . '&token=' . $token, 'complete this form') }}.
		</p>
		<p>
			If you did not initate a password reset, just ignore this email. Your current password is still valid.<br>
			(It doesn't hurt to change the password every now and then, though)
		</p>
		<p>
			<a href="http://scubawhere.com">scubawhere.com</a>
		</p>
	</body>
</html>
