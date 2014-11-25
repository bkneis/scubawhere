@extends('status.template')

@section('content')
	<form method="post" role="form" class="form-inline text-center">
		<h1><i class="fa fa-lock"></i></h1>
		<div class="input-group">
			<div class="input-group-addon">Password</div>
			<input type="password" name="password" class="form-control text-center" autofocus>
		</div>
		<input type="submit" value="Login" class="btn btn-default">
	</form>
@stop
