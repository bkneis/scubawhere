<script type="text/x-handlebars-template" id="booking-summary-template">
	<div class="row tab-nav">
		<div class="col-xs-6">
			<button class="btn btn-primary btn-prev"{{#compare mode 'view'}} disabled="disabled"{{/compare}}><li class="fa fa-angle-left"></li> Prev</button>
		</div>
		<div class="col-xs-6">
			<button class="btn btn-primary btn-next pull-right">Next <li class="fa fa-angle-right"></li></button>
		</div>
	</div>

	<ul class="list-group" id="booking-summary">

		<li class="list-group-item active">
			<h4 class="list-group-item-heading">Summary<span class="pull-right">{{currency}} {{decimal_price}}</h4>
		</li>
		<li class="list-group-item summary-references">
			<p class="clearfix"><strong>Booking Reference</strong> <span class="pull-right">{{reference}}</span></p>
			{{#if agent_reference}}
				<p><strong>Agent Reference</strong> <span class="pull-right"><small>{{agent_reference}}</small></span></p>
			{{/if}}
		</li>
		{{#notEmptyObj selectedTickets}}
			<li class="list-group-item" id="selected-tickets">
				<h5>Selected Tickets</h5>
				{{#each selectedTickets}}
					<p>
						<i class="fa fa-ticket fa-fw"></i>&nbsp; {{{name}}} <span class="badge badge-default small">{{qty}}</span>
						{{#compare ../mode 'edit'}}<a href="javascript:void(0);" class="remove-ticket pull-right" data-id="{{id}}"><i class="fa fa-times fa-lg"></i></a>{{/compare}}
					</p>
				{{/each}}
			</li>
		{{/notEmptyObj}}
		{{#notEmptyObj selectedPackages}}
			<li class="list-group-item" id="selected-packages">
				<h5>Selected Packages</h5>
				{{#each selectedPackages}}
					<div class="panel panel-warning">
						<div class="panel-heading" role="tab">
							<h4 class="panel-title">
								<a class="accordion-heading" data-toggle="collapse" href="#booking-summary-{{UID}}">
									<i class="fa fa-tags fa-fw"></i>&nbsp; {{{name}}}
									<i class="fa fa-plus-square-o expand-icon pull-right"></i>
								</a>
							</h4>
						</div>
						<div id="booking-summary-{{UID}}" class="panel-collapse collapse" role="tabpanel">
							<div class="panel-body">
								{{#compare ../mode 'edit'}}<a href="javascript:void(0);" class="remove-package pull-right" data-uid="{{UID}}"><i class="fa fa-times fa-lg"></i></a>{{/compare}}
								{{#if courses}}
									<strong>Courses</strong>
									{{#each courses}}
										<p>
											<i class="fa fa-graduation-cap fa-fw"></i> {{{name}}}
										</p>
									{{/each}}
								{{/if}}
								{{#if tickets}}
									<strong>Tickets</strong>
									{{#each tickets}}
										<p>
											<i class="fa fa-ticket fa-fw"></i> {{{name}}} <span class="badge badge-default small">{{qty}}</span>
										</p>
									{{/each}}
								{{/if}}
								{{#if addons}}
									<strong>Addons</strong>
									{{#each addons}}
										<p>
											<i class="fa fa-cubes fa-fw"></i> {{{name}}} <span class="badge badge-default small">{{qty}}</span>
										</p>
									{{/each}}
								{{/if}}
								{{#if accommodations}}
									<strong>Accommodations</strong>
									{{#each accommodations}}
										<p>
											<i class="fa fa-bed fa-fw"></i> {{{name}}} <span class="badge badge-default small">{{qty}}</span>
										</p>
									{{/each}}
								{{/if}}
							</div>
						</div>
					</div>
				{{/each}}
			</li>
		{{/notEmptyObj}}
		{{#notEmptyObj selectedCourses}}
			<li class="list-group-item" id="selected-courses">
				<h5>Selected Courses</h5>
				{{#each selectedCourses}}
					<div class="panel panel-info">
						<div class="panel-heading" role="tab">
							<h4 class="panel-title">
								<a class="accordion-heading" data-toggle="collapse" href="#booking-summary-{{UID}}">
									<i class="fa fa-graduation-cap fa-fw"></i>&nbsp; {{{name}}}
									<i class="fa fa-plus-square-o expand-icon pull-right"></i>
								</a>
							</h4>
						</div>
						<div id="booking-summary-{{UID}}" class="panel-collapse collapse" role="tabpanel">
							<div class="panel-body">
								{{#compare ../mode 'edit'}}<a href="javascript:void(0);" class="remove-course pull-right" data-uid="{{UID}}"><i class="fa fa-times fa-lg"></i></a>{{/compare}}
								{{#if trainings}}
									<strong>Class</strong>
									{{#each trainings}}
										<p>
											<i class="fa fa-graduation-cap fa-fw"></i> {{{name}}} <span class="badge badge-default small">{{qty}}</span>
										</p>
									{{/each}}
								{{/if}}
								{{#if tickets}}
									<strong>Tickets</strong>
									{{#each tickets}}
										<p>
											<i class="fa fa-ticket fa-fw"></i> {{{name}}} <span class="badge badge-default small">{{qty}}</span>
										</p>
									{{/each}}
								{{/if}}
							</div>
						</div>
					</div>
				{{/each}}
			</li>
		{{/notEmptyObj}}
		{{#notEmptyObj selectedCustomers}}
			<li class="list-group-item" id="selected-customers">
				<h5>Customers</h5>
				{{#each selectedCustomers}}
					<p>
					    {{#isLead this}}
							<i class="fa fa-user fa-fw"></i>&nbsp; {{{firstname}}} {{{lastname}}} <small><span class="label label-warning">LEAD</span></small>
						{{else}}
							<i class="fa fa-user fa-fw"></i>&nbsp; {{{firstname}}} {{{lastname}}}{{#compare ../mode 'edit'}} <small><span class="label label-unselected lead-customer" data-id="{{id}}">LEAD</span></small>{{/compare}}
						{{/isLead}}
						{{#compare ../mode 'edit'}}<a href="javascript:void(0);" class="remove-customer pull-right" data-id="{{id}}"><i class="fa fa-times fa-lg"></i></a>{{/compare}}
					</p>
				{{/each}}
			</li>
		{{/notEmptyObj}}
		{{#notEmptyObj bookingdetails}}
			<li class="list-group-item">
				<h5>Trips & Classes</h5>
				<div class="panel-group" id="booking-summary-trips" role="tablist" aria-multiselectable="true">
					{{#each bookingdetails}}
						<div class="panel panel-default">
							<div class="panel-heading" role="tab">
								<h4 class="panel-title">
									<a class="accordion-heading" data-toggle="collapse" data-parent="#booking-summary-trips" href="#booking-summary-trips-{{id}}">
										{{#if ticket}}
											<i class="fa fa-ship fa-fw visible-lg-inline-block"></i>
										{{else}}
											<i class="fa fa-graduation-cap fa-fw visible-lg-inline-block"></i>
										{{/if}}
										&nbsp;{{{firstChar customer.firstname}}}. {{{customer.lastname}}} | {{#if session}}{{{session.trip.name}}}{{else}}{{#if training_session}}{{{training.name}}}{{else}}-{{/if}}{{/if}} <i class="fa fa-plus-square-o expand-icon pull-right"></i>
									</a>
								</h4>
							</div>
							<div id="booking-summary-trips-{{id}}" class="panel-collapse collapse" role="tabpanel">
								<div class="panel-body">
									<p>
										<strong>{{{customer.firstname}}} {{{customer.lastname}}}</strong>
										{{#compare ../mode 'edit'}}<a href="javascript:void(0);" class="unassign-session pull-right" title="Unassign Session" data-id="{{id}}">
											<i class="fa fa-times fa-lg"></i>
										</a>{{/compare}}
									</p>
									{{#if packagefacade}}
										<p class="bg-warning">
											<i class="fa fa-tags fa-fw"></i> {{{packagefacade.package.name}}}
										</p>
									{{/if}}
									{{#if course}}
										<p class="bg-info">
											<i class="fa fa-graduation-cap fa-fw"></i> {{{course.name}}}
										</p>
									{{/if}}
									{{#if ticket}}
										<p>
											<i class="fa fa-ticket fa-fw"></i> {{{ticket.name}}}
										</p>
									{{/if}}
									{{#if training}}
										<p>
											<i class="fa fa-graduation-cap fa-fw"></i> {{{training.name}}}
										</p>
									{{/if}}

									{{#if session}}
										<p><i class="fa fa-ship fa-fw"></i> {{{session.trip.name}}}</p>
										<p><i class="fa fa-calendar fa-fw"></i> {{friendlyDate session.start}} - {{tripFinish session.start session.trip.duration}}</p>
										{{#unless packagefacade}}{{#unless course}}
											<p><i class="fa fa-money fa-fw"></i> {{currency}} {{ticket.decimal_price}}</p>
										{{/unless}}{{/unless}}
									{{/if}}
									{{#if training_session}}
										<p><i class="fa fa-calendar fa-fw"></i> {{friendlyDate training_session.start}} - {{tripFinish training_session.start training.duration}}</p>
										{{#unless packagefacade}}
											<p><i class="fa fa-money fa-fw"></i> {{currency}} {{course.decimal_price}}</p>
										{{/unless}}
									{{/if}}
									{{#if temporary}}
										<p><i class="fa fa-calendar fa-fw"></i> no date set</p>
										{{#unless packagefacade}}{{#unless course}}
											<p><i class="fa fa-money fa-fw"></i> {{currency}} {{ticket.decimal_price}}</p>
										{{/unless}}{{/unless}}
									{{/if}}
									{{#notEmptyObj addons}}
										<div class="panel panel-default">
											<div class="panel-heading" role="tab">
												<h4 class="panel-title">
													<a class="accordion-heading" data-toggle="collapse" href="#booking-summary-addons-{{../id}}">
														<i class="fa fa-cubes fa-fw"></i>&nbsp; Addons <i class="fa fa-plus-square-o expand-icon pull-right"></i>
													</a>
												</h4>
											</div>
											<div id="booking-summary-addons-{{../id}}" class="panel-collapse collapse" role="tabpanel">
												<div class="panel-body">
													{{#each addons}}
														<p>
															<i class="fa fa-cart-plus fa-fw"></i>

															{{{name}}} |

															{{#unless pivot.packagefacade_id}}
																{{currency}} {{decimal_price}}
															{{else}}
																<i class="fa fa-tags"></i>
															{{/unless}}

															<span class="badge badge-default"><small>{{pivot.quantity}}</small></span>

															{{#compare ../mode 'edit'}}{{#unless compulsory}}<a class="remove-addon pull-right" href="javascript:void(0);" title="Remove Addon" data-id="{{id}}" data-bookingdetail-id="{{../../id}}" data-packagefacade-id="{{pivot.packagefacade_id}}"><i class="fa fa-times fa-lg"></i></a>{{else}} <small>(compulsory)</small>{{/unless}}{{/compare}}
														</p>
													{{/each}}
												</div>
											</div>
										</div>
									{{/notEmptyObj}}
								</div>
							</div>
						</div>
					{{/each}}
				</div>
			</li>
		{{/notEmptyObj}}
		{{#notEmptyObj accommodations}}
			<li class="list-group-item">
				<h5>Accommodation</h5>
				<div class="panel-group" id="booking-summary-accommodation" role="tablist" aria-multiselectable="true">
					{{#each accommodations}}
						<div class="panel panel-default">
							<div class="panel-heading" role="tab">
								<h4 class="panel-title">
									<a class="accordion-heading" data-toggle="collapse" data-parent="#booking-summary-accommodation" href="#booking-summary-accommodation-{{id}}-{{customer.id}}-{{pivot.start}}">
										<i class="fa fa-bed fa-fw visible-lg-inline-block"></i>&nbsp; {{{firstChar customer.firstname}}}. {{{customer.lastname}}} | {{{name}}} <i class="fa fa-plus-square-o expand-icon pull-right"></i>
									</a>
								</h4>
							</div>
							<div id="booking-summary-accommodation-{{id}}-{{customer.id}}-{{pivot.start}}" class="panel-collapse collapse" role="tabpanel">
								<div class="panel-body">
									{{#compare ../mode 'edit'}}<a href="javascript:void(0);" class="remove-accommodation pull-right" title="Remove Accommodation" data-id="{{id}}" data-customer-id="{{pivot.customer_id}}" data-start="{{pivot.start}}"><i class="fa fa-times fa-lg"></i></a>{{/compare}}

									<p><i class="fa fa-calendar fa-fw"></i> {{friendlyDateNoTime pivot.start}} - {{friendlyDateNoTime pivot.end}}</p>

									{{#unless pivot.packagefacade_id}}
										<p><i class="fa fa-money fa-fw"></i> {{currency}} {{decimal_price}}</p>
									{{else}}
										<i class="fa fa-tags"></i> (packaged)
									{{/unless}}

								</div>
							</div>
						</div>
					{{/each}}
				</div>
			</li>
		{{/notEmptyObj}}

	</ul>
</script>
