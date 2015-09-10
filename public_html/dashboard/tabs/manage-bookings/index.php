<div id="wrapper" class="clearfix">
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Search for a booking</h4>
				</div>
				<div class="panel-body">
					<form id="find-booking-form">
						<div class="form-row">
							<label class="field-label">Reference</label>
							<input type="text" name="reference" class="form-control" style="text-transform: uppercase;">
						</div>

						<div class="form-row">
							<label class="field-label">Date</label>
							<input type="text" name="date" class="datepicker form-control" data-date-format="YYYY-MM-DD">
						</div>

						<div class="form-row">
							<label class="field-label">Customer's Last Name</label>
							<input type="text" name="lastname" class="form-control">
						</div>

						<!--<div class="form-row">
							<label class="">Ticket</label>
							<select id="type-product" class="form-control">
								<option>Loading..</option>
							</select>
							<script type="text/x-handlebars-template" id="ticket-list-template">
								<option>Please select...</option>
								{{#each tickets}}
									<option value="{{id}}">{{{name}}}</option>
								{{/each}}
							</script>
						</div>-->

						<input type="reset" class="btn btn-danger btn-sm" value="Clear">
						<button class="btn btn-primary pull-right" id="find-booking">Find Booking</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Bookings</h4>
				</div>
				<div class="panel-body">
					<div id="booking-types" class="btn-group col-md-offset-3" role="group">
						<button id="filter-confirmed" type="button" display="confirmed" class="btn btn-default btn-primary btn-switch">Confirmed</button>
						<button id="filter-cancelled" type="button" display="cancelled" class="btn btn-default btn-switch">Cancelled</button>
						<button id="filter-reserved" type="button" display="reserved" class="btn btn-default btn-switch">Reserved</button>
						<button id="filter-saved" type="button" display="saved" class="btn btn-default btn-switch">Saved</button>
						<button id="filter-all" type="button" display="all" class="btn btn-default btn-switch">All</button>
					</div>
					<div style="margin-bottom:10px"></div>
					<div id="booking-table-div"></div>
				</div>
			</div>
		</div>
	</div><!-- .row -->

	<script type="text/x-handlebars-template" id="booking-list-item-template">
		<table id="bookings-table" class="bluethead">
			<thead>
				<tr class="bg-primary">
					<th style="width:30px;"></th> <!-- icons -->
					<th>Ref</th>
					<th>Arrival</th>
					<th>Customer</th>
					<th>Email</th>
					<th>Phone</th>
					<th></th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody id="booking-list">

				{{#each bookings}}
					<tr class="accordion-header" data-id={{id}}>
						<td>{{sourceIcon}} {{statusIcon}}</td>
						<td>{{reference}}</td>
						<td>{{arrivalDate}}</td>
						<td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
						<td>{{lead_customer.email}}</td>
						<td>{{lead_customer.phone}}</td>
						<td>{{lead_customer.country.abbreviation}}</td>
						<td>{{price}}</td>
					</tr>
				{{else}}
					<tr><td colspan="7" style="text-align: center;">You have no bookings yet.</td></tr>
				{{/each}}

			</tbody>
		</table>
	</script>

	<div id="modalWindows" style="height: 0;"></div>

	<div class="modal fade" id="email-customer-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Email Customer</h4>
				</div>
				<form id="email-customer-form" class="form-horizontal" role="form">
					<div class="modal-body">
						<fieldset id="email-customer-details"></fieldset>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Send Email</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="text/x-handlebars-template" id="cancellation-fee-template">
		<div id="modal-cancellation-fee" class="reveal-modal">
			<h4>Do you incurr a cancellation fee?</h4>

			<form class="form cancellation-form">
				<div class="form-group">
					<label>
						<input type="radio" name="thisorthat" value="fee">
						Yes:
						<div class="input-group">
							<span class="input-group-addon">{{currency}}</span>
							<input type="text" class="form-control" name="cancellation_fee" placeholder="00.00">
						</div>
					</label>
				</div>
				<div class="form-group">
					<label>
						<input type="radio" name="thisorthat" value="percentage">
						Yes, a percentage:
						<div class="input-group">
							<span class="input-group-addon">%</span>
							<input type="text" class="form-control" name="fee_percentage" placeholder="00.0">
						</div>
					</label>
				</div>
				<div class="form-group">
					<label>
						<input type="radio" name="thisorthat" value="no" checked>
						No cancellation fee
					</label>
				</div>
				<div class="form-group">
					<a class="btn btn-default pull-left close-modal" title="Abort">Abort</a>
					<button class="btn btn-primary pull-right cancel-booking">Cancel Booking</button>
				</div>
			</form>

			<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
		</div>
	</script>

	<script type="text/x-handlebars-template" id="email-customer-template">
		<div class="form-group">
			<div class="col-md-12">
				<label for="subject" class="control-label">Subject</label>
				<input type="text" name="subject" class="form-control" placeholder="Subject">
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<label for="message" class="control-label">Message</label>
				<textarea rows="6" name="message" class="form-control" placeholder="Message"></textarea>
			</div>
		</div>
		<input type="hidden" name="to" class="form-control" value="{{email}}">
		<input type="hidden" name="customer_name" class="form-control" value="{{firstname}} {{lastname}}">
	</script>

	<script type="text/x-handlebars-template" id="booking-details-template">
		{{#each bookingDetails}}
			<div style="float: left; width: 360px; margin-right: 10px; border-right: 1px solid #C3D9F4; height: 100%;">
				{{#if payments}}
					<h5 class="text-center">Transactions</h5>
					<table style="width: 350px;" class="table">
						<tr>
							<th>Date</th>
							<th>Amount</th>
							<th>Via</th>
						</tr>
						{{#each payments}}
							<tr>
								<td>{{received_at}}</td>
								<td>{{currency}} {{amount}}</td>
								<td>{{paymentgateway.name}}</td>
							</tr>
						{{/each}}
						{{#each refunds}}
							<tr>
								<td>{{received_at}}</td>
								<td class="text-danger">{{currency}} -{{amount}}</td>
								<td>{{paymentgateway.name}} (refund)</td>
							</tr>
						{{/each}}
						<tr>
							<td></td>
							<td class="table-sum">{{currency}} {{sumPaid}}</td>
							<td>{{#unless cancelled}}{{remainingPay}}{{/unless}}</td>
						</tr>
					</table>
				{{else}}
					<h5 class="text-center text-muted">No transactions yet</h5>
				{{/if}}
			</div>

			<div style="margin-bottom: 1em;">
				{{addTransactionButton id}}
				{{editButton id}}

				<a onclick="emailCustomer({{lead_customer.id}})"><button class="btn btn-default pull-right"><i class="fa fa-envelope fa-fw"></i> Email customer</button></a>
			</div>
			<div>
				{{cancelButton}}
			</div>
		{{/each}}
	</script>

	<script src="/tabs/manage-bookings/js/script.js"></script>
</div>
