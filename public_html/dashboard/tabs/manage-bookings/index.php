<div id="wrapper" class="clearfix">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Search for a booking</h4>
			</div>
			<div class="panel-body">
				<form id="find-booking-form">
					<div class="form-row">
						<label class="field-label">Reference</label>
						<input type="text" name="reference" class="form-control" style="text-transform: uppercase;">
					</div>

					<div class="form-row">
						<label class="field-label">Date</label>
						<input type="text" name="date" class="datepicker form-control" data-date-format="YYYY-MM-DD">
					</div>

					<div class="form-row">
						<label class="field-label">Customer's Last Name</label>
						<input type="text" name="lastname" class="form-control">
					</div>

					<!--<div class="form-row">
						<label class="">Ticket</label>
						<select id="type-product" class="form-control">
							<option>Loading..</option>
						</select>
						<script type="text/x-handlebars-template" id="ticket-list-template">
							<option>Please select...</option>
							{{#each tickets}}
								<option value="{{id}}">{{{name}}}</option>
							{{/each}}
						</script>
					</div>-->

					<input type="reset" class="btn btn-danger btn-sm" value="Clear">
					<button class="btn btn-primary pull-right" id="find-booking">Find Booking</button>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Bookings</h4>
			</div>
			<div class="panel-body">
				<table class="bluethead">
					<thead>
						<tr class="bg-primary">
							<th></th> <!-- icons -->
							<th>Ref</th>
							<th>Arrival</th>
							<th>Customer</th>
							<th>Email</th>
							<th>Phone</th>
							<th></th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody id="booking-list">
						<tr><td colspan="9" style="text-align: center;"> </td></tr>
						<tr><td colspan="9" style="text-align: center;"><i class="fa fa-refresh fa-spin fa-lg fa-fw"></i></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<script type="text/x-handlebars-template" id="booking-list-item-template">
		{{#each bookings}}
			<tr class="accordion-header" data-id={{id}}>
				<td>{{sourceIcon}} {{statusIcon}}</td>
				<td>{{reference}}</td>
				<td>{{arrivalDate}}</td>
				<td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
				<td>{{lead_customer.email}}</td>
				<td>{{lead_customer.phone}}</td>
				<td>{{lead_customer.country.abbreviation}}</td>
				<td>{{currency}} {{decimal_price}}</td>
			</tr>
			<tr class="accordion-body accordion-{{id}}">
				<td colspan="9" class="clearfix">
					<div style="float: left; width: 360px; margin-right: 10px; border-right: 1px solid #C3D9F4; height: 100%;">
						{{#if payments}}
							<h4 class="text-center">Recieved Transactions</h4>
							<table style="width: 350px;" class="table">
								<tr>
									<th>Date</th>
									<th>Amount</th>
									<th>Via</th>
								</tr>
								{{#each payments}}
									<tr>
										<td>{{received_at}}</td>
										<td>{{currency}} {{amount}}</td>
										<td>{{paymentgateway.name}}</td>
									</tr>
								{{/each}}
								<tr>
									<td></td>
									<td class="table-sum">{{currency}} {{sumPaid}}</td>
									<td>{{remainingPay}}</td>
								</tr>
							</table>
						{{else}}
							<h5 class="text-center text-muted">No transactions yet</h5>
						{{/if}}
					</div>

					<div style="margin-bottom: 1em;">
						{{addTransactionButton}}
						{{editButton}}

						<a href="mailto:{{lead_customer.email}}"><button class="btn btn-default pull-right"><i class="fa fa-envelope fa-fw"></i> Contact customer</button></a>
					</div>
					<div>
						{{cancelButton}}
					</div>
				</td>
			</tr>
			<tr class="accordion-spacer accordion-{{id}}"></tr>
		{{else}}
			<tr><td colspan="7" style="text-align: center;">You have no bookings yet.</td></tr>
		{{/each}}
	</script>

	<script src="/common/js/jquery/jquery.serialize-object.min.js"></script>

	<script src="js/Controllers/Booking.js"></script>
	<script src="js/Controllers/Ticket.js"></script>
	<script src="tabs/manage-bookings/js/script.js"></script>
</div>
