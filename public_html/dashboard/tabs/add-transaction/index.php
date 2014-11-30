<div id="wrapper" style="width: 50%; margin: 20px auto;">
	<div class="row">
		<div class="box100">
			<label class="dgreyb">Booking info</label>
			<div class="padder" id="booking-details-container">
				<script type="text/x-handlebars-template" id="booking-details-template">
					<table>
						<tr>
							<td style="vertical-align: top; font-weight: bold;">Lead customer</td>
							<td>
								{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}<br>
								<a href="mailto:{{lead_customer.email}}">{{lead_customer.email}}</a><br>
								{{lead_customer.country.abbreviation}} {{lead_customer.phone}}
							</td>
							<td><h2 style="float: right; margin-top: 0;">{{reference}}</h2></td>
						</tr>
						<tr>
							<td style="vertical-align: top; font-weight: bold;">Status</td>
							<td>
								<strong>{{status}}</strong><br>
								<div style="display: inline-block;">{{currency}} {{sumPayed}}</div>
								<div style="display: inline-block; width: 200px; position: relative; top: 5px;">{{remainingPay}}</div>
								<div style="display: inline-block;">{{currency}} {{decimal_price}}</div>
							</td>
							<td></td>
						</tr>
					</table>
				</script>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="box100">
			<label class="dgreyb">Add transaction</label>
			<div class="padder">
				Add a transaction here
			</div>
		</div>
	</div>

	<script src="js/Controllers/Booking.js"></script>
	<script src="tabs/add-transaction/script.js"></script>
</div>
