<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Search for a booking:</label>
			<div class="padder">
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

		<div class="box70">
			<label class="dgreyb">Bookings</label>
			<div class="padder">
				<table class="bluethead">
					<thead>
						<tr>
							<th width="10"></th> <!-- source icon -->
							<th width="10"></th> <!-- saved/reserved/confirmed icon -->
							<th width="10"></th> <!-- payments -->
							<th>Ref</th>
							<th>Customer</th>
							<th>Email</th>
							<th>Phone</th>
							<th></th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody id="booking-list">
						<tr><td colspan="9" style="text-align: center;"><span id="save-loader" class="loader"></span></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<script type="text/x-handlebars-template" id="booking-list-item-template">
		{{#each bookings}}
			<tr class="accordion-header" data-id={{id}}>
				<td><i class="fa fa-{{sourceIcon}}" title="{{sourceTooltip}}"></td>
				<td><i class="fa fa-{{statusIcon}}" title="{{statusTooltip}}"></td>
				<td><i class="fa fa-circle" style="color: {{paymentIcon}};" title="{{paymentTooltip}}"></td>
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
										<td>{{created_at}}</td>
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
	<script src="tabs/find-booking/js/script.js"></script>
</div>

