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
				<h2 class="panel-title">Step 1: Select the booking detail</h2>
			</div>
			<div class="panel-body">
				<div class="list-group" id="addon-booking-details">
				</div>
			</div>
			<script id="addon-booking-details-template" type="text/x-handlebars-template">
				{{#each details}}{{#if session}}
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
						<p class="list-group-item-text"><strong>Trip:</strong> <span class="trip-name">{{{session.trip.name}}}</span></p>
						<p class="list-group-item-text"><strong>Date:</strong> <span class="start-date">{{friendlyDate session.start}}</span></p>
					</a>
				{{/if}}{{/each}}
			</script>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Step 2: Select Addons</h2>
			</div>
			<div class="panel-body">
				<ul class="list-group" id="addons-list">
					<span class="loader"></span>
				</ul>
			</div>
		</div>
		<script id="addons-list-template" type="text/x-handlebars-template">
			{{#each addons}}
				{{#unless compulsory}}
					<li data-id="{{id}}" class="list-group-item">
						<h4 class="list-group-item-heading addon-name">{{{name}}}</h4>
						<p>{{{description}}}</p>
						<div class="row">
							<div class="col-md-4">
								<p class="lead mb5">£ <span id="baseprice-{{id}}" class="price">{{decimal_price}}</span></p>
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
