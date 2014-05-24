<form action="{{ URL::to('test/purifier') }}" method="POST">
	Bad description:<br>
	<textarea name="description" cols="50" rows="20">

<script type="text/javascript">
function test() {
	alert(document.cookie);
}
</script>
<a href="#" onclick="test();">tes't</a>

	</textarea><br>
	<input type="submit">
</form>
