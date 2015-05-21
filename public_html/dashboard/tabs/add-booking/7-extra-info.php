<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Extra Details <small>Is there anything else to know?</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 col-md-offset-2 col-xs-12" id="extra-info-container"></div>

	<script type="text/x-handlebars-template" id="extra-info-template">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Extra Information (Optional)</h2>
			</div>
			<form id="extra-form" class="form-horizontal">
				<fieldset>
					<div class="panel-body">
						<div class="form-group">
							<label for="discount" class="col-sm-4 control-label"><strong>Discount</strong></label>
							<div class="col-md-8">
								<div class="input-group pull-left" style="width: 39%; margin-right: 5px;">
									<span class="input-group-addon">%</span>
									<input type="text" class="form-control" id="discount-percentage" value="0.00" />
								</div>
								<div class="input-group" style="width: 59%;">
									<span class="input-group-addon"><i class="fa fa-money"></i></span>
									<input type="text" class="form-control" id="discount" name="discount" value="{{discount}}" />
								</div>
								<p style="margin-top: 5px; margin-bottom: 5px;">Full price: {{currency}} {{real_decimal_price}}</p>
								<p style="margin-top: 5px;">Discounted price: <span id="discounted-price" style="font-weight: bold;"></span></p>
							</div>
						</div>

						<div class="form-group">
							<label for="pick-up-location" class="col-sm-4 control-label">Pick Up Location</label>
							<div class="col-md-8">
								<div class="input-group">
									<input type="text" class="form-control" id="pick-up-location" name="pick_up_location" value="{{pick_up_location}}" />
									<span class="input-group-addon"><i class="fa fa-search"></i></span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="pick-up-date" class="col-sm-4 control-label">Pick Up Date & Time</label>
							<div class="col-md-8">
								<input type="text" id="pick-up-date" name="pick_up_date" class="form-control pull-left datepicker" data-date-format="YYYY-MM-DD" style="width: 49%;" value="{{pick_up_date}}">
								<input type="text" id="pick-up-time" name="pick_up_time" class="form-control timepicker" data-date-format="HH:mm" style="width: 49%;" value="{{pick_up_time}}">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<textarea id="comment" name="comment" class="form-control" rows="3" placeholder="Any extra comments?">{{comment}}</textarea>
							</div>
						</div>

					</div>
					<div class="panel-footer">
						<div class="row">
							<div class="col-xs-12">
								<button type="submit" class="btn btn-primary pull-right" style="margin-left:5px;">Save</button>
							</div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</script>
</div>
