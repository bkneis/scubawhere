<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Extra Details <small>Is there anything else we should know?</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6 col-md-offset-3 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Extra Information (Optional)</h2>
			</div>
			<form id="extra-form" class="form-horizontal">
				<fieldset>
					<div class="panel-body">
						<div class="form-group">
							<label for="pick-up-location" class="col-sm-4 control-label">Pick Up Location</label>
							<div class="col-md-8">
								<div class="input-group">
									<input type="text" class="form-control" id="pick-up-location" name="pick_up_location" />
									<span class="input-group-addon"><i class="fa fa-search"></i></span>
								</div>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="pick-up-date" class="col-sm-4 control-label">Pick Up Date & Time</label>
							<div class="col-md-4">
								<input type="text" id="pick-up-date" name="pick_up_date" class="form-control pull-left datepicker" data-date-format="YYYY-MM-DD">
								<input type="text" id="pick-up-time" name="pick_up_time" class="form-control timepicker" data-date-format="HH:mm">
							</div>
						</div>
						<div class="form-group col-xs-12">
							<textarea id="comment" name="comment" class="form-control" rows="3" placeholder="Any extra comments?"></textarea>
						</div>
					</div>
					<div class="panel-footer">
						<div class="row">
							<div class="col-xs-12">
								<button type="submit" class="btn btn-primary pull-right" style="margin-left:5px;">Save & Next</button>
								<a href="javascript:void(0);" class="btn btn-warning clear-form pull-right">Clear</a>
							</div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>
