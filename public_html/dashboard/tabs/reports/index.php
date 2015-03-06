<div id="wrapper" class="clearfix">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Transactions Report</h4>
			</div>
			<div class="panel-body">
				<div class="btn-group col-md-offset-3" role="group">
					<button type="button" class="btn btn-primary">Transactions</button>
					<button type="button" class="btn btn-default">Agents</button>
					<button type="button" class="btn btn-default">Booking History</button>
					<button type="button" class="btn btn-default">Trip Utiisation</button>
					<button type="button" class="btn btn-default">Ticket / Packages</button>
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
                <th style="color:#313131">Name</th>
                <th style="color:#313131">Cash</th>
                <th style="color:#313131">Credit card</th>
                <th style="color:#313131">Cheque</th>
                <th style="color:#313131">Bank</th>
                <th style="color:#313131">PayPal</th>
            </tr>
		</thead>
		<tbody>
			{{#each transactions}}
				<tr>
					<td>{{booking.lead_customer}}</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
			{{/each}}
		</tbody>
	</table>
</script>
<script src="/common/js/jquery/jquery.datatables.min.js"></script>
<script type="text/javascript" src="tabs/reports/js/script.js"></script>
