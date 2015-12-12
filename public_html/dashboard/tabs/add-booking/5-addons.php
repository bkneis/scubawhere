<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Addon Selection <small>Do you require any addons?</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Select the trip</h2>
			</div>
			<div class="panel-body">
				<div class="list-group" id="addon-booking-details">
				</div>
			</div>
			<script type="text/x-handlebars-template" id="addon-booking-details-template">
				{{#each details}}{{#if ticket}}
					<a href="javascript:void(0);" class="list-group-item list-group-radio" data-id="{{id}}">
						<h4 class="list-group-item-heading"><span class="customer-name">{{{customer.firstname}}} {{{customer.lastname}}}</span></h4>
						<p class="list-group-item-text"><strong>Ticket:</strong>
							{{#if packagefacade}}
								<span class="label label-warning">{{{packagefacade.package.name}}}</span>
							{{else}}
								<span class="label label-default">Ticket</span>
							{{/if}}
							<span class="ticket-name">{{{ticket.name}}}</span>
						</p>
						<p class="list-group-item-text"><strong>Trip:</strong> <span class="trip-name">{{#if temporary}}-{{else}}{{{session.trip.name}}}{{/if}}</span></p>
						<p class="list-group-item-text"><strong>Date:</strong> <span class="start-date">{{#if temporary}}No date set{{else}}{{friendlyDate session.start}} - {{tripFinish session.start session.trip.duration}}{{/if}}</span></p>
					</a>
				{{/if}}{{/each}}
			</script>
		</div>
	</div>
	<div class="col-sm-6">
		<div id="packaged-addons-list-container">
		</div>
		<script type="text/x-handlebars-template" id="packaged-addons-list-template">
			<div class="panel panel-warning">
				<div class="panel-heading">
					<h2 class="panel-title">Packaged add-ons</h2>
				</div>
				<div class="panel-body">
					<div class="list-group" id="packaged-addons-list">
						{{#each packages}}
							{{#each addons}}
								<a href="javascript:void(0);" data-id="{{id}}" data-packagefacade-id="{{../packagefacade}}" data-package-uid="{{../UID}}" class="list-group-item list-group-radio">
									{{{name}}} <span class="badge badge-default small">{{qty}}</span>
								</a>
							{{/each}}
						{{/each}}
					</div>
					<button class="btn btn-primary pull-right add-packaged-addon">Add</button>
					<button class="btn btn-default btn-sm clear-packaged-addon" onclick="setUpAddonsTab();">Clear selection</button>
				</div>
			</div>
		</script>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Select Addons</h2>
			</div>
			<div class="panel-body">
				<ul class="list-group" id="addons-list">
					<span class="loader"></span>
				</ul>
			</div>
		</div>
		<script type="text/x-handlebars-template" id="addons-list-template">
			{{#each addons}}
				{{#unless compulsory}}
					<li data-id="{{id}}" class="list-group-item">
						<h4 class="list-group-item-heading addon-name">{{{name}}}</h4>
						{{!-- <p>{{{description}}}</p> --}}
						<div class="row">
							<div class="col-md-4">
								<p class="lead mb5">{{pricerange base_prices undefined}}</p>
							</div>
							<div class="col-md-5 pull-right">
								<div class="input-group">
									<input type="number" min="1" max="50" value="1" name="addon-qty" data-id="{{id}}" class="form-control input-sm addon-qty">
									<span class="input-group-btn">
										<button data-id="{{id}}" class="btn btn-primary btn-sm add-addon" type="button">Add</button>
									</span>
								</div>
							</div>
						</div>
					</li>
				{{/unless}}
			{{/each}}
		</script>
	</div>
</div>
