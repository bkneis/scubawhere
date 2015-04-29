@extends('heartbeats.template')

@section('content')
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Most recent logged-in users <span class="pull-right">Your IP: {{ $client_ip }}</span></h3>
		</div>
		<div class="panel-body">
			<table class="table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Operator</th>
						<th>IP</th>
						<th>Last heartbeat</th>
						<th><!-- action button --></th>
					</tr>
				</thead>
				<tbody id="recent-users">
				</tbody>
			</table>
		</div>
	</div>

	<!--<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">API requests per day</h3>
		</div>
		<div class="panel-body" id="daily-requests">
			<canvas id="chart-daily-requests" width="668" height="300"></canvas>
		</div>
	</div>-->
@stop

@section('scripts')
	<script src="/common/js/underscore-min.js"></script>
	<script>
		"use strict";

		var client_ip = '{{ $client_ip }}';
		var data      = {{ $data }};
		var companies = {{ $companies }};


		var recent_users = _.groupBy(data, function(heartbeat) {
			return heartbeat.company_id + heartbeat.ip;
		});

		// Create new array from all respective last heartbeats
		recent_users = _.map(recent_users, function(heartbeats) {
			return _.last(heartbeats);
		});

		// Sort list by activity datetime
		recent_users = _.sortBy(recent_users, function(heartbeat) {
			return heartbeat.date + ' ' + heartbeat.time;
		});

		// Reduce list to 5 most recent
		recent_users = _.last(recent_users, 5);

		// Reverse list, so the most recent is first
		recent_users.reverse();

		// Generate table HTML
		var html = '';
		_.each(recent_users, function(heartbeat) {
			html += '<tr>';
			html += 	'<td>' + heartbeat.company_id + '</td>';
			html += 	'<td>' + companies[heartbeat.company_id] + '</td>';

			if(heartbeat.ip === client_ip)
				html += '<td><em>' + heartbeat.ip + '</em></td>';
			else
				html += '<td>' + heartbeat.ip + '</td>';

			html += 	'<td>' + heartbeat.date + ' ' + heartbeat.time + '</td>';
			html += 	'<td><!-- action button --></td>';
			html += '</tr>';
		});

		document.getElementById('recent-users').innerHTML = html;
	</script>
@stop
