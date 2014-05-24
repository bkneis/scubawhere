<h2>Register a new company</h2>
<form action="{{ action('RegisterController@postCompany') }}" method="POST">
	Username: <input type="text" name="username" required>*<br>
	Email: <input type="email" name="email" required>*<br>
	Name: <input type="text" name="name" required>*<br>
	Address 1: <input type="text" name="address_1" required>*<br>
	Address 2: <input type="text" name="address_2"><br>
	City: <input type="text" name="city" required>*<br>
	County: <input type="text" name="county"><br>
	Postcode: <input type="text" name="postcode" required>*<br>
	Region: <input type="text" name="region_id" required>* (Must be a number)<br>
	Country: <input type="text" name="country_id" required>* (Must be a number)<br>
	Phone: <input type="text" name="phone" required>*<br>
	Website: <input type="text" name="website"><br>
	<input type="submit">
</form>
