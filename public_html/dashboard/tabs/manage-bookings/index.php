<div id="wrapper" class="clearfix">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Search for a booking</h4>
			</div>
			<div class="panel-body">
				<form id="find-booking-form">
					<div class="form-row">
						<label class="field-label">Reference</label>
						<input type="text" name="booking_reference" class="form-control" disabled>
					</div>

					<div class="form-row">
						<label class="field-label">Date</label>
						<input type="text" name="date" class="datepicker form-control" disabled>
					</div>

					<div class="form-row">
						<label class="field-label">Customer's Last Name</label>
						<input type="text" name="last_name" class="form-control" disabled>
					</div>

					<div class="form-row">
						<p><label class="">Type of Product</label></p>
						<select id="type-product" class="form-control" disabled>
							<option>Please select..</option>
							<option>PADI course</option>
							<option>Fun dive</option>
							<option>More options will be added</option>
						</select>
					</div>

					<input type="submit" class="btn btn-primary" id="find-booking" value="Find Booking" disabled>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-8">
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
				<td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
				<td>{{lead_customer.email}}</td>
				<td>{{lead_customer.phone}}</td>
				<td>{{lead_customer.country.abbreviation}}</td>
				<td>{{currency}} {{decimal_price}}</td>
			</tr>
			<tr class="accordion-body accordion-{{id}}">
				<td colspan="9" style="overflow: auto;">
					<div style="float: left; width: 360px; margin-right: 10px; border-right: 1px solid #C3D9F4;">
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
					{{addTransactionButton}}
					{{editButton}}

					<a href="mailto:{{lead_customer.email}}" class="mailto"><button class="btn btn-default pull-right"><i class="fa fa-envelope"></i> &nbsp;Contact customer</button></a>
				</td>
			</tr>
			<tr class="accordion-spacer accordion-{{id}}"></tr>
		{{else}}
			<tr><td colspan="7" style="text-align: center;">You have no bookings yet.</td></tr>
		{{/each}}
	</script>

	<script src="js/Controllers/Booking.js"></script>
	<script src="tabs/manage-bookings/js/script.js"></script>
</div>
