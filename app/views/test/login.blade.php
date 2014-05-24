<form action="{{ URL::to('login') }}" method="POST">
	Username: <input name="username"><br>
	Password: <input type="password" name="password"><br>
	<input type="checkbox" name="remember"> Remember login?<br>
	<input type="submit">
</form>
