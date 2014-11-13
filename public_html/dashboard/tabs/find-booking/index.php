<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Search for a booking:</label>
			<div class="padder">
				<form id="find-booking-form">
					<div class="form-row">
						<label class="field-label">Reference</label>
						<input type="text" name="booking_reference" disabled>
					</div>

					<div class="form-row">
						<label class="field-label">Date</label>
						<input type="text" class="datepicker" name="date" disabled>
					</div>

					<div class="form-row">
						<label class="field-label">Customer's Last Name</label>
						<input type="text" name="last_name" disabled>
					</div>

					<div class="form-row">
						<p><label class="">Type of Product</label></p>
						<select id="type-product" disabled>
							<option>Please select..</option>
							<option>PADI course</option>
							<option>Fun dive</option>
							<option>More options will be added</option>
						</select>
					</div>

					<input type="submit" class="bttn blueb" id="find-booking" value="Find Booking" disabled>
				</form>
			</div>
		</div>

		<div class="box70">
			<label class="dgreyb">Bookings</label>
			<div class="padder">
				<table>
					<thead>
						<tr>
							<th width=""></th>
							<th>Reference</th>
							<th>Customer</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Country</th>
							<th>Price</th>
						</tr>
					</thead>
					<tbody id="booking-list">
						<tr><td colspan="7" style="text-align: center;"><span id="save-loader" class="loader"></span></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<script type="text/x-handlebars-template" id="booking-list-item-template">
		{{#each bookings}}
			<tr>
				<td><i class="fa fa-{{icon source}}"></td>
				<td><a>{{reference}}</a></td>
				<td>{{lead_customer.firstname}} {{lead_customer.lastname}}</td>
				<td>{{lead_customer.email}}</td>
				<td>{{lead_customer.phone}}</td>
				<td>{{lead_customer.country.abbreviation}}</td>
				<td>{{currency}} {{decimal_price}}</td>
			</tr>
		{{else}}
			<tr><td colspan="7" style="text-align: center;">You have no bookings yet.</td></tr>
		{{/each}}
	</script>

	<script src="js/Controllers/Booking.js"></script>
	<script src="tabs/find-booking/js/script.js"></script>
</div>

