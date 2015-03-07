<div id="wrapper" class="clearfix">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Transactions Report</h4>
			</div>
			<div class="panel-body">
				<div id="report-type-btns" class="btn-group col-md-offset-3" role="group">
					<button type="button" data-report="transactions" data-api="/api/payment/filter" class="btn btn-default btn-primary">Transactions</button>
					<button type="button" data-report="agents" data-api="/api/booking/filter-confirmed-by-agent" class="btn btn-default">Agents</button>
					<button type="button" data-report="booking-history" data-api="/api/booking/filter-confirmed" class="btn btn-default">Booking History</button>
					<button type="button" data-report="utilisation" data-api="/api/report/utilisation" class="btn btn-default">Trip Utilisation</button>
					<button type="button" data-report="tickets" class="btn btn-default">Ticket / Packages</button>
				</div>

				<div style="margin-top:20px"></div>

				<div class="row row-list">
				    <div class="col-xs-3">
				    	<div class="input-group">
							<label class="input-group-addon">From : </label>
							<input style="width:200px; float:left;" id="start-date" type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="jumpto" placeholder="YYYY-MM-DD">
						</div>
				    </div>
				    <div class="col-xs-3">
				    	<div class="input-group">
							<label class="input-group-addon">Until : </label>
							<input style="width:200px; float:left;" id="end-date" type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="jumpto" placeholder="YYYY-MM-DD">
						</div>
				    </div>
				    <div class="col-xs-3">
				    	<div id="report-filters" class="pull-right"></div>
				    </div>
				</div> 

				<div style="margin-top:20px"></div>

				<div id="reports"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/x-handlebars-template" id="transactions-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
  			<tr>
                <th style="color:#313131; width:33%">Name</th>
                <th style="color:#313131">Type</th>
                <th style="color:#313131">Amount</th>
            </tr>
		</thead>
		<tbody>
			{{#each entries}}
				<tr>
					<td>{{booking.lead_customer.firstname}} {{booking.lead_customer.lastname}}</td>
					<td>{{paymentgateway.name}}</td>
					<td>{{amount}}</td>
				</tr>
			{{else}}
				<tr><td colspan="3" class="text-center">There are no transactions between these dates</td></tr>
			{{/each}}
		</tbody>
		<tfoot>
			<tr>
				<td><strong>Total</strong></td>
				<td>Cash</td>
				<td id="transactions-totalCash"></td>
			</tr>
			<tr>
				<td></td>
				<td>Credit Card</td>
				<td id="transactions-totalCard"></td>
			</tr>
			<tr>
				<td></td>
				<td>Cheque</td>
				<td id="transactions-totalCheque"></td>
			</tr>
			<tr>
				<td></td>
				<td>Bank</td>
				<td id="transactions-totalBank"></td>
			</tr>
			<tr>
				<td></td>
				<td>Paypal</td>
				<td id="transactions-totalPaypal"></td>
			</tr>
		</tfoot>
	</table>
</script>
<script type="text/x-handlebars-template" id="agents-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
  			<tr>
                <th style="color:#313131">Name</th>
                <th style="color:#313131">Agent name</th>
                <th style="color:#313131">Date</th>
                <th style="color:#313131">Total balance</th>
                <th style="color:#313131">Commission</th>
                <th style="color:#313131">Cleared balance</th>
            </tr>
		</thead>
		<tbody>
			{{#each entries.bookings}}
				<tr>
					<td>{{lead_customer.firstname}} {{lead_customer.lastname}}</td>
					<td>{{agent}}</td>
					<td>{{arrival_date}}</td>
					<td>{{decimal_price}}</td>
					<td></td>
					<td></td>
				</tr>
			{{else}}
				<tr><td colspan="6" class="text-center">There are no agent bookings between these dates</td></tr>
			{{/each}}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3"><strong>Total</strong></td>
				<td></td>
				<td></td>
			</tr>
		</tfoot>
	</table>
</script>
<script type="text/x-handlebars-template" id="booking-history-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
  			<tr>
                <th style="color:#313131">Name</th>
                <th style="color:#313131">Date</th>
                <th style="color:#313131">Method of booking</th>
                <th style="color:#313131">Country</th>
                <th style="color:#313131">Refrence</th>
                <th style="color:#313131">Total cost</th>
            </tr>
		</thead>
		<tbody>
			{{#each entries.bookings}}
				<tr>
					<td>{{lead_customer.firstname}} {{lead_customer.lastname}}</td>
					<td>{{arrival_date}}</td>
					<td>{{source}}</td>
					<td>{{lead_customer.country_id}}</td>
					<td>{{reference}}</td>
					<td>{{decimal_price}}</td>
				</tr>
			{{else}}
				<tr><td colspan="6" class="text-center">There are no bookings between these dates</td></tr>
			{{/each}}
		</tbody>
	</table>
</script>
<script type="text/x-handlebars-template" id="utilisation-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
  			<tr>
                <th style="color:#313131; width:15%">Date</th>
                <th style="color:#313131; width:15%">Trip name</th>
                <th style="color:#313131">Utilisation</th>
            </tr>
		</thead>
		<tbody>
			{{#each entries.utilisation}}
				<tr>
					<td>{{getDate date}}</td>
					<td>{{name}}</td>
					<td>
						<div class="progress">
							<div class="progress-bar progress-bar-success" style="width: {{getUtil capacity unassigned}}%">{{getUtil capacity unassigned}}%</div>
						</div>
					</td>
				</tr>
			{{else}}
				<tr><td colspan="3" class="text-center">There are no trips between these dates</td></tr>
			{{/each}}
		</tbody>
	</table>
</script>
<script type="text/x-handlebars-template" id="agents-filter-template">
	<div class="input-group">
		<label class="input-group-addon">Filter by : </label>
		<select>
			<option>Please select ...</option>
			{{#each agents}}
				<option value="{{id}}">{{name}}</option>
			{{/each}}
		</select>
	</div>
</script>
<script type="text/javascript" src="js/Controllers/Agent.js"></script>
<script src="/common/js/jquery/jquery.datatables.min.js"></script>
<script type="text/javascript" src="tabs/reports/js/script.js"></script>
