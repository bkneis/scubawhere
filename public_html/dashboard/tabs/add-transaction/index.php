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
						<td style="vertical-align: top; font-weight: bold; width: 150px;">Lead customer</td>
						<td>
							{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}<br>
							<a href="mailto:{{lead_customer.email}}">{{lead_customer.email}}</a><br>
							{{lead_customer.country.abbreviation}} {{lead_customer.phone}}
						</td>
						<td style="vertical-align: top;">
							<h3 style="float: right; margin-top: 0;">{{reference}}</h3>
							<a id="view-summary">View booking summary</a>
						</td>
					</tr>
				</table>
				<table style="width: auto;">
					<tr>
						<td style="vertical-align: top; font-weight: bold; width: 150px;">Status</td>
						<td style="width: 120px;">
							<strong>{{status}}</strong>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top; font-weight: bold;">
							Payments
						</td>
						<td>
							{{#unless cancelled}}<div style="display: inline-block; width: 200px; position: relative; top: 4px;">{{remainingPayBar}}</div>{{/unless}}
						</td>
					</tr>
					{{#if cancelled}}
						<tr>
							<td style="vertical-align: top; font-weight: bold;">
								Booking value
							</td>
							<td>
								{{currency}} {{decimal_price}}
							</td>
						</tr>
						<tr>
							<td style="vertical-align: top; font-weight: bold;">
								Received payments
							</td>
							<td>
								{{currency}} {{sumPayed}}
							</td>
						</tr>
						<tr>
							<td style="vertical-align: top; font-weight: bold;">
								Refunded
							</td>
							<td>
								{{currency}} {{sumRefunded}}
							</td>
						</tr>
						<tr>
							<td style="vertical-align: top; font-weight: bold;">
								Cancellation fee
							</td>
							<td>
								{{currency}} {{decimalise cancellation_fee}}
							</td>
						</tr>
						<tr>
							<td style="vertical-align: top; font-weight: bold;">
								{{necessaryTransaction}}
							</td>
							<td style="border-top: 3px double;">
								{{necessaryRefundFormated}}
							</td>
						</tr>
					{{/if}}
				</table>
			</div>
		</div>

		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Add Payment</h3>
			</div>
			<div class="panel-body" id="add-payment-panel">
				{{#ifRequiresPayment}}
					<form role="form" id="add-payment-form" class="form-horizontal">

						<div class="form-group">
							<label class="col-sm-3 col-sm-offset-1 control-label">Amount : <span class="text-danger">*</span></label>
							<div class="col-sm-6">
								<div class="input-group">
									<div class="input-group-addon">{{currency}}</div>
									{{#compare status '!==' 'cancelled'}}
										<input name="amount" type="number" min="0.01" max="{{remainingPay}}" step="0.01" placeholder="0.00" class="form-control" value="{{remainingPay}}">
									{{/compare}}
									{{#compare status '==' 'cancelled'}}
										<input name="amount" type="number" min="0.01" max="{{remainingFee}}" step="0.01" placeholder="0.00" class="form-control" value="{{remainingFee}}">
									{{/compare}}
								</div>
							</div>
						</div>

						<div class="form-group paymentgateways-select-container">
							<div id="save-loader" class="loader"></div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 col-sm-offset-1 control-label">Card Ref : </label>
							<div class="col-sm-6">
								<input name="card_ref" type="number" placeholder="0673" class="form-control no-arrows" value="{{card_ref}}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 col-sm-offset-1 control-label">Notes : </label>
							<div class="col-sm-6">
								<textarea name="notes" class="form-control">{{notes}}</textarea>
							</div>
						</div>

						{{!--<div class="form-group">
							<label class="col-sm-3 col-sm-offset-1 control-label">Received at</label>
							<div class="col-sm-6">
								<input name="received_at" type="text" class="form-control" id="received-at-input" data-date-format="YYYY-MM-DD">
							</div>
						</div>--}}

						<div class="form-group">
							<div class="col-sm-6 col-sm-offset-4">
								<button class="btn btn-primary" id="add-payment-submit">Add Payment</button>
							</div>
						</div>
					</form>
				{{else}}
					<h5 class="text-muted text-center text-normal" style="margin-bottom: 1.4em;">Adding payments is not allowed because the booking does not require any.</h5>
				{{/ifRequiresPayment}}
			</div>
		</div>

		<div class="panel panel-danger">
			<div class="panel-heading">
				<h3 class="panel-title">Add Refund</h3>
			</div>
			<div class="panel-body" id="add-refund-panel">
				<form role="form" id="add-refund-form" class="form-horizontal">

					<div class="form-group">
						<label class="col-sm-3 col-sm-offset-1 control-label">Amount : <span class="text-danger">*</span></label>
						<div class="col-sm-6">
							<div class="input-group">
								<div class="input-group-addon">{{currency}}</div>
								{{#ifRequiresRefund}}
									<input name="amount" type="number" min="0.01" max="{{necessaryRefund}}" step="0.01" placeholder="0.00" class="form-control" value="{{necessaryRefund}}">
								{{else}}
									<input name="amount" type="number" min="0.01" step="0.01" placeholder="0.00" class="form-control">
								{{/ifRequiresRefund}}
							</div>
						</div>
					</div>

					<div class="form-group paymentgateways-select-container">
						<div id="save-loader" class="loader"></div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 col-sm-offset-1 control-label">Card Ref : </label>
						<div class="col-sm-6">
							<input name="card_ref" type="number" placeholder="0673" class="form-control no-arrows" value="{{card_ref}}">
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 col-sm-offset-1 control-label">Notes : </label>
						<div class="col-sm-6">
							<textarea name="notes" class="form-control">{{notes}}</textarea>
						</div>
					</div>

					{{!--<div class="form-group">
						<label class="col-sm-3 col-sm-offset-1 control-label">Received at</label>
						<div class="col-sm-6">
							<input name="received_at" type="text" class="form-control" id="received-at-input" data-date-format="YYYY-MM-DD">
						</div>
					</div>--}}

					<div class="form-group">
						<div class="col-sm-6 col-sm-offset-4">
							<button class="btn btn-primary" id="add-refund-submit">Add Refund</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Transactions</h3>
			</div>
			<div class="panel-body">
				{{#if payments}}
					<table class="table" style="margin-bottom: 0;">
						<thead>
							<tr>
								<th>Date</th>
								<th>Amount</th>
								<th>Via</th>
								<th>Ref</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							{{#each payments}}
								<tr>
									<td>{{received_at}}</td>
									<td>{{currency}} {{amount}}</td>
									<td>{{paymentgateway.name}}</td>
									<td>{{card_ref}}</td>
									<td>
										{{#if notes}}
											<a href="#" class="see-notes" data-type="Payment" data-notes="{{notes}}">See Notes</a>
										{{/if}}
									</td>
								</tr>
							{{/each}}
							{{#each refunds}}
								<tr>
									<td>{{received_at}}</td>
									<td class="text-danger">{{currency}} -{{amount}}</td>
									<td>{{paymentgateway.name}} (refund)</td>
									<td>{{card_ref}}</td>
									<td>
										{{#if notes}}
											<a href="#" class="see-notes" data-type="Refund" data-notes="{{notes}}">See Notes</a>
										{{/if}}
									</td>
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
		<label class="col-sm-4 control-label">Payment Gateway : <span class="text-danger">*</span></label>
		<div class="col-sm-6">
			<select name="paymentgateway_id" class="form-control">
				{{#each paymentgateways}}
					<option value="{{id}}">{{name}}</option>
				{{/each}}
			</select>
		</div>
	</script>

	<!--Basil LocalStorage Wrapper-->
	<script type="text/javascript" src="/common/vendor/basil.js/build/basil.min.js"></script>

	<script src="/tabs/add-transaction/script.js"></script>
</div>
