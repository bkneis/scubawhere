<div id="wrapper" class="clearfix">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 id="report-title" class="panel-title">Transactions Report</h4>
			</div>
			<div class="panel-body">
				<div id="report-type-btns" class="btn-group col-md-offset-1" role="group">
					<button type="button" data-report="transactions" class="btn btn-default btn-primary">Transactions</button>
					<button type="button" data-report="agents" class="btn btn-default">Agents</button>
					<button type="button" data-report="booking-history" class="btn btn-default">Booking History</button>
					<button type="button" data-report="utilisation" class="btn btn-default">Trip Utilisation</button>
					<button type="button" data-report="class-utilisation" class="btn btn-default">Class Utilisation</button>
					<button type="button" data-report="revenue" class="btn btn-default">Revenue Analysis</button>
					<!--<button type="button" data-report="demographics" class="btn btn-default">Demographics Analysis</button>-->
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

<script type="text/x-handlebars-template" id="transactions-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Date</th>
				<th>Name</th>
				<th>Reference</th>
				<th>Type</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			{{#each entries}}
				<tr{{#if refund}} style="color: red;"{{/if}}>
					<td>{{received_at}}</td>
					<td>{{{booking.lead_customer.firstname}}} {{{booking.lead_customer.lastname}}}</td>
					<td>{{booking.reference}}</td>
					<td>{{paymentgateway.name}}{{#if refund}} REFUND{{/if}}</td>
					<td>{{#if refund}}- {{/if}}{{currency.symbol}} {{amount}}</td>
				</tr>
			{{else}}
				<tr><td colspan="4" class="text-center">There are no transactions between these dates</td></tr>
			{{/each}}
		</tbody>

	</table>
	<table id="transactions-summary">
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
				<td id="transactions-online">
					Online
					<div style="width:100%" class="percentage-bar-container bg-success border-success">
						<div id="transactions-online-percentage" class="percentage-bar" style="background-color: #5cb85c;">&nbsp;</div>
						<span id="transactions-totalOnline" class="percentage-left"></span>
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
				<th>Date</th>
				<th>Name</th>
				<th>Reference</th>
				<th>Agent Name</th>
				<th>Agent Ref</th>
				<th>Commission</th>
				<th>Net</th>
				<th>Gross</th>
			</tr>
		</thead>
		<tbody>
			{{#each entries.bookings}}
				<tr>
					<td>{{getDate created_at_local}}</td>
					<td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
					<td>{{reference}}</td>
					<td>{{{agent.name}}}</td>
					<td>{{agent_reference}}</td>
					<td>{{currency}} {{getCommissionAmount}} ({{agent.commission}}%)</td>
					<td>{{currency}} {{getNetAmount}}</td>
					<td>{{currency}} {{decimal_price}}</td>
				</tr>
			{{else}}
				<tr><td colspan="6" class="text-center">There are no agent bookings between these dates</td></tr>
			{{/each}}
		</tbody>
	</table>

	<div class="text-right" style="font-weight: bold;">
		<p>&nbsp;</p>
		<p>Total commission: {{currency}} {{entries.totals.commission}}</p>
		<p>Total revenue: {{currency}} {{entries.totals.revenue}}</p>
		<p>Total invoicable: {{currency}} {{entries.totals.invoicable}}</p>
	</div>
</script>
<script type="text/x-handlebars-template" id="booking-history-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="color:#313131">Date</th>
				<th style="color:#313131">Name</th>
				<th style="color:#313131">Reference</th>
				<th style="color:#313131">Source</th>
				<th style="color:#313131">Country</th>
				<th style="color:#313131">Net Revenue</th>
			</tr>
		</thead>
		<tbody>
			{{#each entries.bookings}}
				<tr>
					<td>{{getDate created_at_local}}</td>
					<td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
					<td>{{reference}}</td>
					<td>{{sourceName}}</td>
					<td>{{getCountry lead_customer.country_id}}</td>
					<td>{{currency}} {{#if agent}}{{getNetAmount}}{{else}}{{decimal_price}}{{/if}}</td>
				</tr>
			{{else}}
				<tr><td colspan="6" class="text-center">There are no bookings between these dates</td></tr>
			{{/each}}
		</tbody>
	</table>

	<div class="text-right" style="font-weight: bold;">
		<p>&nbsp;</p>
		<p>Total revenue: {{currency}} {{entries.totals.revenue}}</p>
	</div>
</script>
<script type="text/x-handlebars-template" id="utilisation-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="color:#313131; width: 100px">Date</th>
				<th style="color:#313131; width: 250px">Trip name</th>
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
						<div class="percentage-bar" style="background-color: #5cb85c; width: {{getUtil capacity assigned}}%;">{{assigned}}</div>
						<span class="percentage-left">{{capacity}}</span>
					</div>
				</td>
			</tr>
			{{else}}
			<tr><td colspan="3" class="text-center">There are no trips between these dates</td></tr>
			{{/each}}
		</tbody>
		</table>
		<table id="utilisation-summary">
			<thead style="font-weight: bold;">
				<tr>
					<td id="utilisation-date-range" style="width:30%">Average</td>
					<td>
						<div style="width:100%" class="percentage-bar-container bg-success border-success">
							<div id="utilisation-average" class="percentage-bar" style="background-color: #5cb85c; width: {{getUtil entries.utilisation_total.capacity entries.utilisation_total.assigned}}%;">{{entries.utilisation_total.assigned}}</div>
							<span id="utilisation-total-capacity" class="percentage-left">{{entries.utilisation_total.capacity}}</span>
						</div>
					</td>
				</thead>
			</table>
	</script>
	<script type="text/x-handlebars-template" id="class-utilisation-report-template">
	<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="color:#313131; width: 100px">Date</th>
				<th style="color:#313131; width: 250px">Class name</th>
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
						<div class="percentage-bar" style="background-color: #5cb85c; width: {{getUtil capacity assigned}}%;">{{getRemaining capacity assigned}}</div>
						<span class="percentage-left">{{capacity}}</span>
					</div>
				</td>
			</tr>
			{{else}}
			<tr><td colspan="3" class="text-center">There are no classes between these dates</td></tr>
			{{/each}}
		</tbody>
		</table>
		<table id="class-utilisation-summary">
			<thead style="font-weight: bold;">
				<tr>
					<td id="class-utilisation-date-range" style="width:30%">Average</td>
					<td>
						<div style="width:100%" class="percentage-bar-container bg-success border-success">
							<div id="class-utilisation-average" class="percentage-bar" style="background-color: #5cb85c;">{{getRemaining entries.utilisation_total.capacity entries.utilisation_total.assigned}}</div>
							<span id="class-utilisation-total-capacity" class="percentage-left"></span>
						</div>
					</td>
				</thead>
			</table>
	</script>
	<script type="text/x-handlebars-template" id="revenue-report-template">
	<div class="col-md-6 text-center">
		<canvas id="revenue-chart" width="400" height="400"></canvas>
	</div>
	<div class="col-md-6">
		<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th style="color:#313131">Revenue Name</th>
					<th style="color:#313131">Color</th>
					<th style="color:#313131; text-align:center;">No. Sold</th>
					<th style="color:#313131; text-align:center;">Total</th>
				</tr>
			</thead>
			<tbody>
				{{#each entries.streams}}
				<tr>
					<td>{{{name}}}</td>
					<td style="width:20px;" bgcolor="{{statColor}}"></td>
					<td style="text-align:center;">{{quantity}}</td>
					<td style="text-align:center;">{{currency}} {{revenue}}</td>
				</tr>
				{{else}}
				<tr><td colspan="3" class="text-center">There has been no revenue between these dates</td></tr>
				{{/each}}
			</tbody>
		</table>

		<div class="alert alert-warning">
			<h4><!--<i class="fa fa-exclamation-triangle fa-fw"></i> -->About these numbers</h4>
			<p>The revenue analysis is a <em><strong>qualitative</strong></em> report. Due to discounts being applied and rounded at different times for this report than when calculating the total price for a booking, the individual revenue numbers shown here can slightly differ from your actual revenue.</p>
			<p>For accounting, please refer to the <em>transactions</em>, <em>agents</em> or <em>booking history</em> reports.</p>
		</div>
	</div>
</script>
<script type="text/x-handlebars-template" id="demographics-report-template">
	<div class="col-md-6 text-center">
		<canvas id="demographics-chart" width="400" height="400"></canvas>
	</div>
	<div class="col-md-6">
		<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th style="color:#313131">Country</th>
					<th style="color:#313131">Color</th>
					<th style="color:#313131; text-align:center;">Total Revenue</th>
				</tr>
			</thead>
			<tbody>
				{{#each countries}}
				<tr>
					<td>{{@key}}</td>
					<td style="width:20px;" id="{{getStatID @key}}-colour"></td>
					<td style="text-align:center;">{{currency}} {{this}}</td>
				</tr>
				{{else}}
				<tr><td colspan="3" class="text-center">There has been no revenue between these dates</td></tr>
				{{/each}}
			</tbody>
		</table>
	</div>
</script>
	<script type="text/x-handlebars-template" id="agents-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select onchange="filterReport('agents', this.value)">
				<option value="0">Please select ...</option>
				{{#each agents}}
					<option value="{{id}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="transactions-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select onchange="filterReport('transactions', this.value)">
				<option value="0">Please select ...</option>
				{{#each gateways}}
					<option value="{{id}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="booking-history-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select onchange="filterReport('booking-history', this.value)">
				<option value="0">Please select ...</option>
				{{#each sources}}
					<option value="{{source}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="utilisation-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select onchange="filterReport('utilisation', this.value)">
				<option value="0">Please select ...</option>
				{{#each trips}}
					<option value="{{name}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="class-utilisation-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select onchange="filterReport('class-utilisation', this.value)">
				<option value="0">Please select ...</option>
				{{#each classes}}
					<option value="{{name}}">{{{name}}}</option>
				{{/each}}
			</select>
		</div>
	</script>
	<script type="text/x-handlebars-template" id="revenue-filter-template">
		<div class="input-group">
			<label class="input-group-addon">Filter by : </label>
			<select onchange="filterReport('revenue', this.value)">
				<option value="0">Please select ...</option>
				{{#each types}}
					<option value="{{this}}">{{this}}</option>
				{{/each}}
			</select>
		</div>
	</script>

	<script type="text/javascript" src="/common/js/Chart.js"></script>
	<script type="text/javascript" src="tabs/reports/js/script.js"></script>

</div>
