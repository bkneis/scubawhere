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
			<div class="panel-body">
				<form id="extra-form" class="form-horizontal">
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
							<p style="margin-top: 5px; margin-bottom: 5px;">Full price: {{currency}} {{fullPrice}}</p>
							<p style="margin-top: 5px;">Discounted price: <span id="discounted-price" style="font-weight: bold;"></span></p>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">
							Comments:
							<textarea id="comment" name="comment" class="form-control" rows="3" placeholder="Any extra comments?">{{comment}}</textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary pull-right" style="margin-left:5px;">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">Pick-ups</h2>
			</div>
			<div class="panel-body">
				<div id="pick-up-list">
					{{> pick-up-list}}
				</div>

				<form class="form-horizontal alert alert-warning" id="add-pick-up-form" style="margin-top: 25px;">
					<h4>Add a pick-up</h4>
					<div class="form-group">
						<label class="col-sm-4 control-label">Pick Up Location</label>
						<div class="col-md-8">
							<div class="input-group">
								<input type="text" class="form-control" id="pick-up-location-input" name="location">
								<span class="input-group-addon"><i class="fa fa-search"></i></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-4 control-label">Pick Up Date & Time</label>
						<div class="col-md-8">
							<input type="text" name="date" class="form-control pull-left datepicker" data-date-format="YYYY-MM-DD" style="width: 49%;">
							<input type="text" name="time" id="pick-up-time" class="form-control timepicker" data-date-format="HH:mm" style="width: 49%;">
						</div>
					</div>

					<div class="form-group" style="margin-bottom: 0;">
						<div class="col-md-12">
							<input type="submit" value="Add" class="btn btn-primary pull-right" style="margin-left:5px;">
						</div>
					</div>
				</form>
			</div>
		</div>
	</script>

	<script type="text/x-handlebars-template" id="pick-up-list-partial">
		{{#each pick_ups}}
			<div class="row" style="line-height: 2.5;">
				<div class="col-md-2 col-md-offset-2" style="border-bottom: 1px solid lightgrey">{{friendlyDateNoTime date}}</div>
				<div class="col-md-1" style="border-bottom: 1px solid lightgrey">{{trimSeconds time}}</div>
				<div class="col-md-5" style="border-bottom: 1px solid lightgrey">{{location}}</div>
				<div class="col-md-1"><button class="btn btn-sm btn-danger removePickUp" data-id="{{id}}">&times;</button></div>
			</div>
		{{else}}
			<center>No pick-ups yet.</center>
		{{/each}}
	</script>
</div>
