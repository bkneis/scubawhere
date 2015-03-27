<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Summary <small>Booking summary</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-10 col-lg-offset-1">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-7">
						<ul class="list-group">
							<li class="list-group-item active">
								<h4 class="list-group-item-heading">Trips &amp; Addon</h4>
							</li>
							<div id="summary-booking-details"></div>
							<script id="summary-booking-details-template" type="text/x-handlebars-template">
								{{#each bookingdetails}}
									<li class="list-group-item">
										<div class="row">
											<div class="col-md-6">
												<h4 class="list-group-item-heading">{{{customer.firstname}}} {{{customer.lastname}}}</h4>
												<p class="list-group-item-text">
													<strong>Ticket:</strong>
													{{#if packagefacade}}
														<span class="label label-warning">{{{packagefacade.package.name}}}</span>
													{{else}}
														<span class="label label-default">Ticket</span>
													{{/if}}
													<span class="ticket-name">{{{ticket.name}}}</span>
												</p>
												<p class="list-group-item-text"><strong>Trip:</strong> {{{session.trip.name}}}</p>
												<p class="list-group-item-text"><strong>Date:</strong> {{friendlyDate session.start}}</p>
											</div>
											<div class="col-md-6">
												<ul class="list-group">
													{{#each addons}}
														<li class="list-group-item">
															<p class="list-group-item-text"><strong>Addon Name:</strong> {{{name}}}</p>
															<p class="list-group-item-text"><strong>Quantity:</strong> {{{pivot.quantity}}}</p>
															<p class="list-group-item-text"><strong>Price:</strong> {{{decimal_price}}}</p>
														</li>
													{{/each}}
												</ul>
											</div>
										</div>
									</li>
								{{/each}}
							</script>
						</ul>
					</div>
					<div class="col-md-5">
						<ul class="list-group">
							<li class="list-group-item active">
								<h4 class="list-group-item-heading">Accommodations</h4>
							</li>
							<div id="summary-accommodations"></div>
							<script id="summary-accommodations-template" type="text/x-handlebars-template">
								{{#each accommodations}}
									<li class="list-group-item">
										<h4 class="list-group-item-heading">{{{customer.firstname}}} {{{customer.lastname}}}</h4>
										<p class="list-group-item-text"><strong>Name:</strong> {{{name}}}</p>
										<p class="list-group-item-text"><strong>Price:</strong> {{{decimal_price}}}</p>
										<p class="list-group-item-text"><strong>From:</strong> {{friendlyDate pivot.start}}</p>
										<p class="list-group-item-text"><strong>To:</strong> {{friendlyDate pivot.end}}</p>
									</li>
								{{/each}}
							</script>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">Lead Customer</h4>
							</div>
							<div class="panel-body" id="summary-lead"></div>
							<script id="summary-lead-template" type="text/x-handlebars-template">
								<p class="lead"><strong>{{{firstname}}} {{{lastname}}}</strong></p>
								<p><strong>Email:</strong> {{{email}}}</p>
								<p><strong>Phone Number:</strong> {{{phone}}}</p>
								<p><strong>Country:</strong> {{{countryName country_id}}}</p>
							</script>
						</div>
					</div>
					<div class="col-md-3 col-md-offset-5 summary-totals">
						<div class="row">
							<div class="col-md-6">
								<div class="text-right">
									<!--<p>Sub-total:</p>-->
									<!--<p>VAT:</p>-->
									<h4 class="text-success">Total:</h4>
								</div>
							</div>
							<div class="col-md-6">
								<div class="text-right" id="summary-price">
									<script id="summary-price-template" type="text/x-handlebars-template">
										{{!--<p>£800.00</p>
										<p>£200.00</p>--}}
										<h4 class="text-success">{{currency}} {{decimal_price}}</h4>
										(Status: {{status}})
									</script>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-success btn-block save-booking mb10"><i class="fa fa-save fa-fw"></i> Save For Later</button>
								<button class="btn btn-success btn-block confirm-booking mb10"><i class="fa fa-check fa-fw"></i> Confirm</button>
								<button onclick="addTransaction();" class="btn btn-primary btn-block add-transaction"><i class="fa fa-credit-card fa-fw"></i> Add Transaction</button>
								<h4 class="text-center">Reserve Booking</h4>
								<form id="reserve-booking" class="form-horizontal">
									<div class="form-group">
										<div class="radio col-md-12">
											<label>
												<input type="radio" name="email" id="email-yes" value="1" checked>
												Send confirmation email to customer
											</label>
										</div>
										<div class="radio col-md-12">
											<label>
												<input type="radio" name="email" id="email-no" value="0">
												Do not send email
											</label>
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-12">
											<button class="btn btn-warning btn-block"><i class="fa fa-clock-o fa-fw"></i> Reserve</button>
										</div>
									</div>
									<div class="form-group">
										<label for="reserve-until" class="col-sm-6 control-label">For (hours)</label>
										<div class="col-md-6">
											<input id="reserve-until" name="reserved" class="form-control timepicker" placeholder="hh:mm" data-date-format="hh:mm">
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
