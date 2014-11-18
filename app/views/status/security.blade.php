@extends('status.template')

@section('content')
	<form method="post" role="form" class="form-inline text-center">
		<div class="input-group">
			<input type="password" name="password" class="form-control" autofocus>
			<span class="input-group-addon"><i class="fa fa-lock"></i></span>
		</div>
	</form>
@stop
