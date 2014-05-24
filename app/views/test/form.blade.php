<form action="{{ URL::to('test/form') }}" method="POST">
	<p>First Week</p>
	Mon<input type="checkbox" name="schedule[1][]" value="mon"><br>
	Tue<input type="checkbox" name="schedule[1][]" value="tue"><br>
	Wed<input type="checkbox" name="schedule[1][]" value="wed"><br>
	Thu<input type="checkbox" name="schedule[1][]" value="thu"><br>
	Fri<input type="checkbox" name="schedule[1][]" value="fri"><br>
	Sat<input type="checkbox" name="schedule[1][]" value="sat"><br>
	Sun<input type="checkbox" name="schedule[1][]" value="sun"><br>

	<p>Second Week</p>
	Mon<input type="checkbox" name="schedule[2][]" value="mon"><br>
	Tue<input type="checkbox" name="schedule[2][]" value="tue"><br>
	Wed<input type="checkbox" name="schedule[2][]" value="wed"><br>
	Thu<input type="checkbox" name="schedule[2][]" value="thu"><br>
	Fri<input type="checkbox" name="schedule[2][]" value="fri"><br>
	Sat<input type="checkbox" name="schedule[2][]" value="sat"><br>
	Sun<input type="checkbox" name="schedule[2][]" value="sun"><br>
	<input type="submit">
</form>
