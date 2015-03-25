<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Booking Source <small>Where is your booking coming from?</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="form-group">
			<div class="btn-group btn-group-justified booking-source">
				<a role="button" class="btn btn-default btn-lg" data-type="agent">
					<p><i class="fa fa-user fa-3x"></i></p>
					<p class="text-center">Agent</p>
				</a>
				<a role="button" class="btn btn-default btn-lg" data-type="telephone">
					<p><i class="fa fa-phone fa-3x"></i></p>
					<p class="text-center">Phone</p>
				</a>
				<a role="button" class="btn btn-default btn-lg" data-type="email">
					<p><i class="fa fa-envelope fa-3x"></i></p>
					<p class="text-center">Email</p>
				</a>
				<a role="button" class="btn btn-default btn-lg" data-type="facetoface">
					<p><i class="fa fa-eye fa-3x"></i></p>
					<p class="text-center">In Person</p>
				</a>
			</div>
		</div>
	</div>
</div>
<div class="row" id="agent-info">
	<div class="col-sm-4 col-sm-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Select Agent</h2>
			</div>
			<div class="panel-body">
				<div class="list-group" id="agents-list">
					<span class="loader"></span>
				</div>
			</div>
		</div>
		<script id="agents-list-template" type="text/x-handlebars-template">
			{{#each agents}}
				<a data-toggle="collapse" data-parent="#agents-list" href="#agent-body-{{id}}"  data-id="{{id}}" class="list-group-item list-group-radio">
					{{{name}}} <span class="badge">{{{branch_name}}}</span>
				</a>
				<div id="agent-body-{{id}}" class="panel-body panel-collapse collapse form-inline">
					Agent reference: <input type="text" class="form-control" id="agent-reference-{{id}}">
				</div>
			{{/each}}
		</script>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<a href="javascript:void(0);" class="btn btn-primary source-finish pull-right">Next</a>
	</div>
</div>
