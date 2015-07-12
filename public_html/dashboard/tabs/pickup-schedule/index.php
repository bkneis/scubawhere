<div id="wrapper" class="clearfix">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Today's Pickup Schedule</h4>
				</div>
				<div class="panel-body">
					<div class="row row-list">
					    <div class="col-xs-3">
					    	<div class="input-group">
								<label class="input-group-addon">Select date : </label>
								<input style="width:200px; float:left;" id="date-select" type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="jumpto" placeholder="YYYY-MM-DD">
							</div>
					    </div>
					</div>

					<div style="margin-top:20px"></div>

					<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
	            		<thead>
	              			<tr style="color:#313131">
	              				<th>Reference</th>
				                <th>Name</th>
				                <th>Phone</th>
				                <th># of customers</th>
				                <th>Location</th>
				                <th>Time</th>
				            </tr>
	            		</thead>
	            		<tbody id="pickup-table">

	            		</tbody>
	        		</table>

				</div>
			</div>
		</div>
	</div><!-- .row -->
	<script type="text/x-handlebars-template" id="pick-up-schedule-template">
		{{#each pickups}}
			<tr>
				<td>{{reference}}</td>
				<td>{{lead_customer.firstname}} {{lead_customer.lastname}}</td>
				<td>{{lead_customer.phone}}</td>
				<td>{{number_of_customers}}</td>
				<td>{{pick_up_location}}</td>
				<td>{{pick_up_time}}</td>
			</tr>
		{{else}}
			<tr><tdcolspan="6" class="text-center">There are no customers that need picking up today </td></tr>
		{{/each}}
	</script>
	<script type="text/javascript" src="js/Controllers/Report.js"></script>
	<script src="/common/js/jquery/jquery.datatables.min.js"></script>
	<script type="text/javascript" src="tabs/pickup-schedule/js/script.js"></script>
</div>
