<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Accommodation Selection <small>Do you require any accommodation?</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Select Customer</h2>
			</div>
			<div class="panel-body">
				<div class="list-group" id="accommodation-customers">
				</div>
			</div>
			<script type="text/x-handlebars-template" id="accommodation-customers-template">
				{{#each customers}}
					<a href="javascript:void(0);" class="list-group-item list-group-radio accommodation-customer" data-id="{{id}}">
						<h4 class="list-group-item-heading">{{{firstname}}} {{{lastname}}}</h4>
						{{!-- {{#each bookingdetails}}
							<p class="list-group-item-text">
								<strong>Ticket:</strong>
								{{#if packagefacade}}
									<span class="label label-warning">{{{packagefacade.package.name}}}</span>
								{{else}}
									<span class="label label-default">Ticket</span>
								{{/if}}
								<span class="ticket-name">{{{ticket.name}}}</span>
							</p>
							<p class="list-group-item-text"><strong>Trip: </strong>{{{session.trip.name}}}</p>
							<p class="list-group-item-text session-start" data-date="{{session.start}}"><strong>Date: </strong>{{friendlyDate session.start}}</p>
						{{/each}} --}}
					</a>
				{{/each}}
			</script>
		</div>
	</div>
	<div class="col-sm-8">
		<div id="packaged-accommodations-list-container">
		</div>
		<script type="text/x-handlebars-template" id="packaged-accommodations-list-template">
			<div class="panel panel-warning">
				<div class="panel-heading">
					<h2 class="panel-title">Packaged accommodations</h2>
				</div>
				<div class="panel-body">
					<div class="list-group" id="packaged-accommodations-list">
						{{#each packages}}
							{{#each accommodations}}
								<li data-id="{{id}}" class="list-group-item accommodation-item">
									<span class="badge badge-default small">{{qty}} nights</span>
									<h4 class="list-group-item-heading">{{{name}}}</h4>
									<div class="row">
										<div class="form-group">
											<label for="" class="col-lg-1 control-label">From: </label>
											<div class="col-lg-4">
												<input type="text" name="start" class="form-control input-sm datepicker accommodation-start" data-date-format="YYYY-MM-DD" data-qty="{{qty}}" data-min-date="2015-05-07">
											</div>
											<label class="col-lg-1 control-label">To: </label>
											<div class="col-lg-4">
												<input type="text" name="end" class="form-control input-sm datepicker accommodation-end" data-date-format="YYYY-MM-DD">
											</div>
											<div class="col-lg-2">
												<button class="btn btn-primary btn-sm add-packaged-accommodation pull-right" data-id="{{id}}" data-packagefacade-id="{{../packagefacade}}" data-package-uid="{{../UID}}">Add</button>
											</div>
										</div>
									</div>
								</li>
							{{/each}}
						{{/each}}
					</div>
				</div>
			</div>
		</script>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Select Accommodation</h2>
			</div>
			<div class="panel-body">
				<ul class="list-group" id="accommodations-list">
					<span class="loader"></span>
				</ul>
			</div>
		</div>
		<script type="text/x-handlebars-template" id="accommodations-list-template">
			{{#each accommodations}}
				<li data-id="{{id}}" class="list-group-item accommodation-item">
					<p class="lead mb5 pull-right">
						<span class="price">{{pricerange base_prices prices}}</span>
						<small><small>/ night</small></small>
					</p>
					<h4 class="list-group-item-heading">{{{name}}}</h4>

					<button class="btn btn-primary btn-sm select-accommodation-dates" data-id="{{id}}">Select Dates</button>
					{{!-- <button class="btn btn-primary btn-sm add-accommodation pull-right" data-id="{{id}}">Add</button> --}}

					<p>{{{description}}}</p>
				</li>
			{{/each}}
		</script>
	</div>
</div>
