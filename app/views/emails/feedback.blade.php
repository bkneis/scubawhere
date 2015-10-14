<!DOCTYPE html>
<html>
<head>
	<title>Feedback | scubawhereRMS</title>

	<link rel="stylesheet" type="text/css" href="/common/css/bootstrap-scubawhere.css">
	<link rel="stylesheet" type="text/css" href="/common/css/universal-styles.css">
	<link rel="stylesheet" type="text/css" href="/css/login-register.css">
</head>
<body>

	<div>

		<h1>RMS feedback from {{$company->name}}</h1>

		<p>Involving: {{$feedback['tab']}}</p>

		@if ($feedback['feature'])
			<p>Feature: {{$feedback['feature']}}</p>
		@endif

		<p>{{$feedback['issue']}}</p>

	</div>
</body>
</html>
