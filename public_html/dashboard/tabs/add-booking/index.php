<div class="container">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#source-tab">Source</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#ticket-tab">Ticket</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#customer-tab">Customers</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#session-tab">Sessions</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#addon-tab">Addons</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#extra-tab">Extra Details</a></li>
		<li role="presentation"><a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#summary-tab">Summary</a></li>
	</ul>

	<div class="tab-content">

		<div role="tabpanel" class="tab-pane fade in active" id="source-tab">
			<div class="row">
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
							<div class="list-group" id="agents">
							</div>
						</div>
					</div>
					<script id="agents-list-template" type="text/x-handlebars-template">
						{{#each agents}}
						<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
							{{name}} <span class="badge">{{branch_name}}</span>
						</a>
						{{/each}}
					</script>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary source-finish pull-right">Next</a>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane fade" id="ticket-tab">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h2>Tickets <small>Which tickets would you like to book?</small></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-8">
					<div class="form-group" id="tickets">
						
					</div>
					<script id="tickets-list-template" type="text/x-handlebars-template">
						{{#each tickets}}
							<div class="col-sm-3">
								<a role="button" class="btn btn-default btn-lg btn-ticket" data-id="{{id}}">
									<p class="ticket-icon"><i class="fa fa-ticket fa-3x"></i></p>
									<p class="text-center ticket-name">{{name}}</p>
									<p class="text-center ticket-price">{{base_prices.decimal_price}}</p>
								</a>
							</div>
						{{/each}}
					</script>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item active">
							<h4 class="list-group-item-heading">Basket</h4>
							<p class="list-group-item-text">Total tickets: <span id="basket-total">0</span></p>
						</li>
						<li class="list-group-item" id="basket">

						</li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary tickets-finish pull-right">Next</a>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane fade" id="customer-tab">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h2>Customers <small>Select the customers this booking is for.</small></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">Existing Customer</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label for="existing-customers" class="col-sm-4 control-label">Name</label>
								<div class="col-sm-8">
									<select id="existing-customers" name="existing-customers" class="form-control select2"></select>
									<script id="customers-list-template" type="text/x-handlebars-template">
										{{#each customers}}
											<option value="{{id}}">{{firstname}} {{lastname}} - {{email}}</option>
										{{/each}}
									</script>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<div class="row">
								<div class="col-xs-12">
									<a href="javascript:void(0);" class="btn btn-primary add-customer pull-right" style="margin-left:5px;">Add to booking</a>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default form-horizontal">
						<div class="panel-heading">
							<h3 class="panel-title">New Customer</h3>
						</div>
						<form id="new-customer">
							<fieldset>
								<div class="panel-body">
									<div class="form-group">
										<label for="email" class="col-sm-4 control-label">Email</label>
										<div class="col-sm-8">
											<input id="customer-email" name="email" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label for="firstname" class="col-sm-4 control-label">First Name</label>
										<div class="col-sm-8">
											<input id="customer-firstname" name="firstname" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label for="lastname" class="col-sm-4 control-label">Last Name</label>
										<div class="col-sm-8">
											<input id="customer-lastname" name="lastname" class="form-control">
										</div>
									</div>
								</div>
								<div class="panel-footer">
									<div class="row">
										<div class="col-xs-12">
											<button type="submit" class="btn btn-primary new-customer pull-right" style="margin-left:5px;">Create</button>
											<a href="javascript:void(0);" class="btn btn-warning clear-customer pull-right">Clear</a>
										</div>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="col-md-5">
					<ul class="list-group">
						<li href="#" class="list-group-item active">
							<h4 class="list-group-item-heading">Added Customers</h4>
						</li>
						<div id="added-customers">

						</div>
						<script id="added-customers-template" type="text/x-handlebars-template">
							<li href="#" class="list-group-item">
								<h4 class="list-group-item-heading">{{firstname}} {{lastname}}</h4>
								<p class="list-group-item-text">
									<a href="mailto:{{email}}">{{email}}</a><br>
									{{address_1}}<br>
									{{city}}, {{county}}, {{postcode}}<br>
									<abbr title="Phone">P:</abbr> {{phone}}
								</p>
								<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-id="{{id}}">Edit</a>
								<a href="javascript:void(0);" class="btn btn-danger btn-sm remove-customer">Remove</a>
							</li>
						</script>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary customers-finish pull-right">Next</a>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane fade" id="session-tab">
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h2>Sessions <small>When would you like to go diving?</small></h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4 col-sm-offset-2">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Select Customer</h2>
						</div>
						<div class="panel-body">
							<div class="list-group" id="session-customers">
							</div>
						</div>
					</div>
					<script id="session-customers-template" type="text/x-handlebars-template">
						<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
							{{firstname}} {{lastname}}
						</a>
					</script>
				</div>
				<div class="col-sm-4">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h2 class="panel-title">Select Ticket</h2>
						</div>
						<div class="panel-body">
							<div class="list-group" id="session-tickets">
							</div>
						</div>
					</div>
					<script id="session-tickets-template" type="text/x-handlebars-template">
						<a href="javascript:void(0);" data-id="{{id}}" class="list-group-item list-group-radio">
							{{name}}
						</a>
					</script>
				</div>
			</div>
			<div class="row">
				<form class="form-inline" role="form">
					<div class="col-sm-4">
						<div class="form-group">
							<label for="" class="col-sm-5 control-label">After:</label>
							<div class="col-sm-7">
								<input type="email" class="form-control" id="inputEmail3" placeholder="Email">
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="" class="col-sm-5 control-label">Before:</label>
							<div class="col-sm-7">
								<input type="email" class="form-control" id="inputEmail3" placeholder="Email">
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label for="" class="col-sm-5 control-label">Trip:</label>
							<div class="col-sm-7">
								<select id="trips" name="trips" class="form-control select2"></select>
							</div>
							<script id="trips-list-template" type="text/x-handlebars-template">
								{{#each trips}}
									<option value="{{id}}">{{name}}</option>
								{{/each}}
							</script>
						</div>
					</div>
				</form>
			</div>
			<div class="row">
				<div class="col-md-10 col-md-offset-1">
					<table class="table table-condensed" id="sessions-table">
						<thead>
							<tr>
								<th>Start</th>
								<th>Finish</th>
								<th>Trip</th>
								<th>Free Spaces</th>
								<th>Boat</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>
				</div>
				<script id="sessions-table-template" type="text/x-handlebars-template">
					{{#each sessions}}
						<tr>
							<td>{{friendlyDate start}}</td>
							<td>{{tripFinish start trip.duration}}</td>
							<td>{{trip.name}}</td>
							<td><span id="free-spaces{{id}}">{{freeSpaces capacity}}</span></td>
							<td>{{boat.name}}</td>
							<td><a href="javascript:void(0);" class="btn btn-primary btn-sm assign-session" data-id="{{id}}">Assign</a></td>
						</tr>
					{{/each}}
				</script>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane fade" id="addon-tab">
			<div class="row">
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
							<h2 class="panel-title">Select Addons</h2>
						</div>
						<div class="panel-body">
							<ul class="list-group" id="addons">
							</ul>
						</div>
					</div>
					<script id="addons-template" type="text/x-handlebars-template">
						{{#each addons}}
							<li data-id="{{id}}" class="list-group-item">
								<h4 class="list-group-item-heading addon-name">{{name}}</h4>
    							<p>{{description}}</p>
    							<div class="row">
    								<div class="col-sm-4">
    									<p class="lead mb5">£<span id="baseprice-{{id}}" class="price">{{price}}</span></p>
    								</div>
    								<div class="col-sm-3 pull-right">
		    							<div class="input-group">
		    								<input type="number" min="1" max="50" value="1" name="qty" class="form-control input-sm">
		    								<span class="input-group-btn">
		    									<button data-id="{{id}}" class="btn btn-primary btn-sm add-addon" type="button">Add</button>
		    								</span>
		    							</div>
	    							</div>
    							</div>
							</li>
						{{/each}}
					</script>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item active">
							<h4 class="list-group-item-heading">Addons Basket</h4>
							<p class="list-group-item-text">Total: <span id="addons-total">0</span></p>
						</li>
						<li class="list-group-item" id="addons-basket">

						</li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary addon-finish pull-right">Next</a>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane fade" id="addon-tab">
			<div class="row">
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
							<h2 class="panel-title">Select Addons</h2>
						</div>
						<div class="panel-body">
							<ul class="list-group" id="addons">
							</ul>
						</div>
					</div>
					<script id="addons-template" type="text/x-handlebars-template">
						{{#each addons}}
							<li data-id="{{id}}" class="list-group-item">
								<h4 class="list-group-item-heading addon-name">{{name}}</h4>
    							<p>{{description}}</p>
    							<div class="row">
    								<div class="col-sm-4">
    									<p class="lead mb5">£<span id="baseprice-{{id}}" class="price">{{price}}</span></p>
    								</div>
    								<div class="col-sm-3 pull-right">
		    							<div class="input-group">
		    								<input type="number" min="1" max="50" value="1" name="qty" class="form-control input-sm">
		    								<span class="input-group-btn">
		    									<button data-id="{{id}}" class="btn btn-primary btn-sm add-addon" type="button">Add</button>
		    								</span>
		    							</div>
	    							</div>
    							</div>
							</li>
						{{/each}}
					</script>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item active">
							<h4 class="list-group-item-heading">Addons Basket</h4>
							<p class="list-group-item-text">Total: <span id="addons-total">0</span></p>
						</li>
						<li class="list-group-item" id="addons-basket">

						</li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<a href="javascript:void(0);" class="btn btn-primary addon-finish pull-right">Next</a>
				</div>
			</div>
		</div>
	</div>
</div>



<!--Styling (Temporary)-->
<link rel="stylesheet" href="tabs/add-booking/css/style.css" type="text/css" />

<!--Bootstrap-->
<link rel="stylesheet" href="common/css/bootstrap.min.css" type="text/css" />
<script type="text/javascript" src="common/js/bootstrap.min.js"></script>

<!--Select 2-->
<link rel="stylesheet" href="common/css/select2.css" type="text/css" />
<link rel="stylesheet" href="common/css/select2-bootstrap.css" type="text/css" />
<script type="text/javascript" src="common/js/select2.min.js"></script>

<!--Controllers-->
<script src="/dashboard/js/Controllers/Agent.js"></script>
<script src="/dashboard/js/Controllers/Ticket.js"></script>
<script src="/dashboard/js/Controllers/Package.js"></script>
<script src="/dashboard/js/Controllers/Session.js"></script>
<script src="/dashboard/js/Controllers/Booking.js"></script>
<script src="/dashboard/js/Controllers/Trip.js"></script>
<script src="/dashboard/js/Controllers/Customer.js"></script>
<script src="/dashboard/js/Controllers/Addon.js"></script>
<!--My scripts-->
<script type="text/javascript" src="tabs/add-booking/js/script.js"></script>
<script type="text/javascript" src="tabs/add-booking/js/frontend.js"></script>