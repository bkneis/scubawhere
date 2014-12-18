<div id="wrapper" style="width: 60%; max-width: 600px; margin: 20px auto;">
	<div id="booking-details-container"></div>

	<script type="text/x-handlebars-template" id="booking-details-template">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Booking info</h3>
			</div>
			<div class="panel-body">
				<table>
					<tr>
						<td style="vertical-align: top; font-weight: bold;">Lead customer</td>
						<td>
							{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}<br>
							<a href="mailto:{{lead_customer.email}}">{{lead_customer.email}}</a><br>
							{{lead_customer.country.abbreviation}} {{lead_customer.phone}}
						</td>
						<td style="vertical-align: top;"><h3 style="float: right; margin-top: 0;">{{reference}}</h3></td>
					</tr>
					<tr>
						<td style="vertical-align: top; font-weight: bold;">Status</td>
						<td>
							<strong>{{status}}</strong><br>
							<div style="display: inline-block;">{{currency}} {{sumPayed}}</div>
							<div style="display: inline-block; width: 200px; position: relative; top: 4px;">{{remainingPayBar}}</div>
							<div style="display: inline-block;">{{currency}} {{decimal_price}}</div>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Add transaction</h3>
			</div>
			<div class="panel-body" id="add-transaction-panel">
				<form role="form" id="add-transaction-form" class="form-horizontal">

					<div class="form-group">
						<label class="col-sm-3 col-sm-offset-1 control-label">Amount</label>
						<div class="col-sm-6">
							<div class="input-group">
								<div class="input-group-addon">{{currency}}</div>
								<input name="amount" type="number" min="0.01" max="{{remainingPay}}" step="0.01" placeholder="0.00" class="form-control">
							</div>
						</div>
					</div>

					<div class="form-group" id="paymentgateways-select-container">
						<div id="save-loader" class="loader"></div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 col-sm-offset-1 control-label">Received at</label>
						<div class="col-sm-6">
							<input name="received_at" type="text" class="form-control" id="received-at-input" data-date-format="YYYY-MM-DD">
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-6 col-sm-offset-4">
							<button class="btn btn-primary" id="add-transaction-submit">Add Transaction</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Received transactions</h3>
			</div>
			<div class="panel-body">
				{{#if payments}}
					<table class="table" style="margin-bottom: 0;">
						<thead>
							<tr>
								<th>Date</th>
								<th>Amount</th>
								<th>Via</th>
							</tr>
						</thead>
						<tbody>
							{{#each payments}}
								<tr>
									<td>{{received_at}}</td>
									<td>{{currency}} {{amount}}</td>
									<td>{{paymentgateway.name}}</td>
								</tr>
							{{/each}}
						</tbody>
					</table>
				{{else}}
					<h5 class="text-muted text-center">No transactions yet</h5>
				{{/if}}
			</div>
		</div>
	</script>

	<script type="text/x-handlebars-template" id="paymentgateways-select-template">
		<label class="col-sm-3 col-sm-offset-1 control-label">Payment Gateway</label>
		<div class="col-sm-6">
			<select name="paymentgateway_id" class="form-control">
				{{#each paymentgateways}}
					<option value="{{id}}">{{name}}</option>
				{{/each}}
			</select>
		</div>
	</script>

	<script src="/common/js/jquery/jquery.serialize-object.min.js"></script>

	<!-- <script src="js/Controllers/Booking.js"></script> -->
	<script src="js/Controllers/Payment.js"></script>
	<script src="tabs/add-transaction/script.js"></script>
</div>
