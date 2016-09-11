<!DOCTYPE html>
<html>
<head>
	<title>Email | {{$company->name}}</title>

	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/css/login-register.css">
</head>
<body>

	<div>

		<h1>New email from {{$company->name}}</h1>

		<p>{{$data['message']}}</p>

	</div>
</body>
</html>
