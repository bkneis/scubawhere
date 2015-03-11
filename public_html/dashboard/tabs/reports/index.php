<div id="wrapper" class="clearfix">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 id="report-title" class="panel-title">Transactions Report</h4>
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
					<div class="col-xs-6">
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
				<th style="color:#313131; width:25%">Date</th>
				<th style="color:#313131; width:33%">Name</th>
				<th style="color:#313131">Type</th>
				<th style="color:#313131">Amount</th>
			</tr>
		</thead>
		<tbody>
			{{#each entries}}
			{{#if refund}}
			<tr style="color:#F00">
				<td>{{received_at}}</td>
				<td>{{{booking.lead_customer.firstname}}} {{{booking.lead_customer.lastname}}}</td>
				<td>{{paymentgateway.name}} REFUND</td>
				<td>- {{currency.symbol}} {{amount}}</td>
			</tr>
			{{else}}
			<tr>
				<td>{{received_at}}</td>
				<td>{{{booking.lead_customer.firstname}}} {{{booking.lead_customer.lastname}}}</td>
				<td>{{paymentgateway.name}}</td>
				<td>{{currency.symbol}} {{amount}}</td>
			</tr>
			{{/if}}
			{{else}}
			<tr><td colspan="4" class="text-center">There are no transactions between these dates</td></tr>
			{{/each}}
		</tbody>
		
	</table>
	<table>
		<thead style="font-weight: bold;">
			<tr>
				<td></td>
				<td></td>
				<td class="pull-right" id="transactions-date-range">Total </td>
				<td id="transactions-cash">
					Cash
					<div style="width:100%" class="percentage-bar-container bg-success border-success">	
						<div id="transactions-cash-percentage" class="percentage-bar" style="background-color: #5cb85c;">&nbsp;</div>	
						<span id="transactions-totalCash" class="percentage-left"></span>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td id="transactions-credit">
					Credit card
					<div style="width:100%" class="percentage-bar-container bg-success border-success">	
						<div id="transactions-credit-percentage" class="percentage-bar" style="background-color: #5cb85c;">&nbsp;</div>	
						<span id="transactions-totalCredit" class="percentage-left"></span>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td id="transactions-cheque">
					Cheques
					<div style="width:100%" class="percentage-bar-container bg-success border-success">	
						<div id="transactions-cheque-percentage" class="percentage-bar" style="background-color: #5cb85c;">&nbsp;</div>	
						<span id="transactions-totalCheque" class="percentage-left"></span>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td id="transactions-bank">
					Bank
					<div style="width:100%" class="percentage-bar-container bg-success border-success">	
						<div id="transactions-bank-percentage" class="percentage-bar" style="background-color: #5cb85c;">&nbsp;</div>	
						<span id="transactions-totalBank" class="percentage-left"></span>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td id="transactions-paypal">
					Paypal
					<div style="width:100%" class="percentage-bar-container bg-success border-success">	
						<div id="transactions-paypal-percentage" class="percentage-bar" style="background-color: #5cb85c;">&nbsp;</div>	
						<span id="transactions-totalPaypal" class="percentage-left"></span>
					</div>
				</td>
			</tr>
		</thead>
	</table>
</script>
<script type="text/x-handlebars-template" id="agents-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="color:#313131">Date</th>
				<th style="color:#313131">Name</th>
				<th style="color:#313131">Agent name</th>
				<th style="color:#313131">Total balance</th>
				<th style="color:#313131">Commission</th>
				<th style="color:#313131">Cleared balance</th>
			</tr>
		</thead>
		<tbody>
			{{#each entries.bookings}}
			<tr>
				<td>{{arrival_date}}</td>
				<td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
				<td>{{{agent.name}}}</td>
				<td>{{currency}} {{decimal_price}}</td>
				<td>{{agent.commission}}%</td>
				<td></td>
			</tr>
			{{else}}
			<tr><td colspan="6" class="text-center">There are no agent bookings between these dates</td></tr>
			{{/each}}
		</tbody>
	</table>
	{{!--<table>
		<thead style="font-weight: bold;">
			<tr>
				<td></td>
				<td></td>
				<td class="pull-right">Date range </td>
				<td>
					Total
					<div style="width:100%" class="percentage-bar-container bg-success border-success">	
					<div id="agents-percentage-total" class="percentage-bar" style="background-color: #5cb85c;">&nbsp;</div>	
					<span id="agents-total" class="percentage-left"></span>
					</div>
				</td>
			</tr>
		</thead>
	</table>--}}
</script>
<script type="text/x-handlebars-template" id="booking-history-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="color:#313131">Date</th>
				<th style="color:#313131">Name</th>
				<th style="color:#313131">Method of booking</th>
				<th style="color:#313131">Country</th>
				<th style="color:#313131">Refrence</th>
				<th style="color:#313131">Total cost</th>
			</tr>
		</thead>
		<tbody>
			{{#each entries.bookings}}
			<tr>
				<td>{{arrival_date}}</td>
				<td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
				<td>{{#if source}} {{source}} {{else}} Agent - {{{agent.name}}} {{/if}}</td>
				<td>{{getCountry lead_customer.country_id}}</td>
				<td>{{reference}}</td>
				<td>{{currency}} {{decimal_price}}</td>
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
				<td>{{{name}}}</td>
				<td>

					<div style="width:100%" class="percentage-bar-container bg-success border-success">	
						<div class="percentage-bar" style="background-color: #5cb85c; width: {{getUtil capacity unassigned}}%;">{{getRemaining capacity unassigned}}</div>	
						<span class="percentage-left">{{capacity}}</span>
					</div>
				</td>
			</tr>
			{{else}}
			<tr><td colspan="3" class="text-center">There are no trips between these dates</td></tr>
			{{/each}}
		</tbody>
		<table>
			<thead style="font-weight: bold;">
				<tr>
					<td id="utilisation-date-range" style="width:30%">Average</td>
					<td>
						<div style="width:100%" class="percentage-bar-container bg-success border-success">	
							<div id="utilisation-average" class="percentage-bar" style="background-color: #5cb85c;">{{getRemaining entries.utilisation_total.capacity entries.utilisation_total.unassigned}}</div>	
							<span id="utilisation-total-capacity" class="percentage-left"></span>
						</div>
					</td>
				</thead>
			</table>
		</table>
	</script>
	<script type="text/x-handlebars-template" id="agents-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select>
				<option>Please select ...</option>
				{{#each agents}}
					<option value="{{id}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="transactions-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select>
				<option>Please select ...</option>
				{{#each gateways}}
					<option value="{{id}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="booking-history-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select>
				<option>Please select ...</option>
				{{#each sources}}
				<option value="{{name}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="utilisation-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select id="utilisation-filter">
				<option>Please select ...</option>
				{{#each trips}}
					<option value="{{id}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/javascript" src="js/Controllers/Agent.js"></script>
	<script type="text/javascript" src="js/Controllers/Trip.js"></script>
	<script type="text/javascript" src="js/Controllers/Report.js"></script>
	<script src="/common/js/jquery/jquery.datatables.min.js"></script>
	<script type="text/javascript" src="tabs/reports/js/script.js"></script>
