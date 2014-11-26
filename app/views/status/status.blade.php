@extends('status.template')

@section('content')
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Mean API response time [ms]</h3>
		</div>
		<div class="panel-body" id="daily-average">
			<canvas id="chart-daily-average" width="668" height="200"></canvas>
		</div>
	</div>

	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Slowest routes in last 24h [ms]</h3>
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
	<script src="/common/chart/Chart.min.js"></script>
	<script>
		"use strict";

		var data = {{ $data }};
		var html;
		var monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		var daysSubScript   = function(day) {
			// Special case for 11, 12 and 13
			if(day > 9 && day < 14) return 'th';

			var subScript = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
			return subScript[day % 10];
		};

		/**
		 * Daily average response times
		 */
		// Group by day
		var days = _.groupBy(data, 'date');
		// Calulate average duration per group
		days = _.each(days, function(dayGroup, date) {
			days[date] = {
				date: date,
				duration: Math.round( _.reduce(dayGroup, function(memo, el) {
					return memo + el.duration * 1;
				}, 0) / dayGroup.length )
			}
		});
		// Take only the last 30 days
		var dates = Object.keys(days).slice(-30);
		days = _.pick(days, dates);
		// Transform date strings
		dates = _.map(dates, function(el, key) {

			var year  = el.slice(0, 4);
			var month = parseInt( el.slice(5, 7), 10 );
			var day   = parseInt( el.slice( -2 ), 10 );
			return day + daysSubScript(day);
		});
		var ctx = document.getElementById("chart-daily-average").getContext("2d");
		var chartData = {
			labels: dates,
			datasets: [{
				label: "Daily Average Response Time",
				fillColor: "rgba(151,187,205,0.2)",
				strokeColor: "rgba(151,187,205,1)",
				pointColor: "rgba(151,187,205,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(151,187,205,1)",
				data: _.pluck(days, 'duration')
			}]
		};
		var myLineChart = new Chart(ctx).Line(chartData, {pointHitDetectionRadius : 6, bezierCurve: false});

		/*
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
		html = '';
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
		*/

		/**
		 * Slowest routes in the last 24h
		 */
		var date24HoursAgo = new Date();
		date24HoursAgo = date24HoursAgo.getTime() - 24 * 60 * 60 * 1000; // 24 hours * 60 minutes * 60 seconds * 1000 milliseconds

		var last24h = [];
		var testDate;
		for(var i = data.length - 1; i >= 0; i--) {
			testDate = new Date(data[i].date + 'T' + data[i].time);

			if( testDate.getTime() < date24HoursAgo ) {
				console.info('Last datetime included in 24h average: ' + testDate.toString());
				break;
			}

			last24h.push(data[i]);
		}
		// Group by route
		var routes = _.groupBy(last24h, 'route');
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
		html = '';
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
