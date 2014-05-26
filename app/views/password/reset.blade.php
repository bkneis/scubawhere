<h1>Enter a new password</h1>
<h2>for {{ $email }}</h2>

{{ Session::get('error') or '' }}

<form action="{{ action('PasswordController@postReset') }}" method="POST">
	<input type="hidden" name="token" value="{{ $token }}">
	<input type="hidden" name="email" value="{{ $email }}"><br>
	New password: <input type="password" name="password" required="required"> (Must be at least 6 characers long)<br>
	Confirm new password: <input type="password" name="password_confirmation" required="required"><br>
	<input type="submit" value="Reset Password">
</form>
