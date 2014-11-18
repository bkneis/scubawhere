@extends('status.template')

@section('content')
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Mean API response time</h3>
		</div>
		<div class="panel-body">
			<span id="total-average"></span> ms
		</div>
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Slowest routes [ms]</h3>
		</div>
		<div class="panel-body">
			<ul class="list-group" id="slowest-routes"></ul>

			<div class="text-right">
				<span class="label label-success">< 100 ms</span>
				<span class="label label-warning">> 250 ms</span>
				<span class="label label-danger">> 500 ms</span>
			</div>
		</div>
	</div>
@stop

@section('scripts')
	<script src="/common/js/underscore-min.js"></script>
	<script src="/common/chart/Chart.js"></script>
	<script>
		"use strict";

		var data = {{ $data }};

		// Total average response time
		var average = Math.round( _.reduce(data, function(memo, el) {
			return memo + el.duration * 1;
		}, 0) / data.length );
		document.getElementById('total-average').innerHTML = average;

		// Total slowest routes
		// Group by route
		var routes = _.groupBy(data, 'route');
		// Calulate average duration per group
		routes = _.each(routes, function(routeGroup, route) {
			routes[route] = {
				route: route,
				duration: Math.round( _.reduce(routeGroup, function(memo, el) {
					return memo + el.duration * 1;
				}, 0) / routeGroup.length )
			}
		});
		// Sort by average duration
		routes = _.sortBy(routes, function(el) {
			return -el.duration;
		});
		// Take only the top 5
		// routes = routes.slice(0, 5);
		// Render list HTML
		var html = '';
		var warningClass;
		_.each(routes, function(el) {
			warningClass = '';
			if(el.duration < 100) warningClass = 'list-group-item-success';
			if(el.duration > 250) warningClass = 'list-group-item-warning';
			if(el.duration > 500) warningClass = 'list-group-item-danger';

			html += '<li class="list-group-item ' + warningClass + '">';
			html += '<span class="badge">' + el.duration + '</span>';
			html += el.route;
			html += '</li>';
		});
		document.getElementById('slowest-routes').innerHTML = html;
	</script>
@stop
