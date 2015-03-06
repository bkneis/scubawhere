<div id="wrapper" class="clearfix">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Trip manifest</h4>
			</div>
			<div class="panel-body">
				<div class="row row-list">
				    <div class="col-xs-3">
				    	<div class="input-group">
							<label class="input-group-addon">From : </label>
							<input style="width:200px; float:left;" id="jump-date" type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="jumpto" placeholder="YYYY-MM-DD">
							<button id="remove-jump" style="display:none;" class="btn btn-danger">Clear</button>
						</div>
				    </div>
				    <div class="col-xs-3">
				    	<div class="input-group">
							<label class="input-group-addon">Until : </label>
							<input style="width:200px; float:left;" id="jump-date" type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="jumpto" placeholder="YYYY-MM-DD">
							<button id="remove-jump" style="display:none;" class="btn btn-danger">Clear</button>
						</div>
				    </div>
				    <div class="col-xs-6">
				    	<div class="pull-right">
					    	<div class="input-group">
								<label class="input-group-addon">Filter by : </label>
								<select id="trip-select">
									<option value="all">Please select ...</option>
									<script type="text/x-handlebars-template" id="trips-list-template">
									{{#each trips}}
										<option value="{{id}}">{{name}}</option>
									{{/each}}
									</script>
								</select>
							</div>
						</div>
				    </div>
				</div> 

				<div style="margin-top:20px"></div>

				<table class="table table-striped table-bordered reports-table" cellspacing="0" width="100%">
            		<thead>
              			<tr>
			                <th style="color:#313131">Name</th>
			                <th style="color:#313131">Refrence</th>
			                <th style="color:#313131">Tickets</th>
			                <th style="color:#313131">Balance</th>
			            </tr>
            		</thead>
            		<tbody>
            		</tbody>
        		</table>

			</div>
		</div>
	</div>
</div>

<script src="/js/Controllers/Trip.js"></script>
<script src="/common/js/jquery/jquery.datatables.min.js"></script>
<script type="text/javascript" src="tabs/trip-manifest/js/script.js"></script>
