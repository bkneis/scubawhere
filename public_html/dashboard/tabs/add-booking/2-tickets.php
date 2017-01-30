<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Tickets, Packages & Courses <small>Please select the items you want to book</small></h2>
		</div>
	</div>
</div>
<div class="row ticket-search">
	<div class="col-sm-12">
		<div class="input-group">
			<div class="input-group-addon"><i class="fa fa-search"></i></div>
			<input type="search" class="form-control" placeholder="Search..." id="ticket-search-box">
		</div>
	</div>
</div>
<div class="row" id="tickets-ticket-list">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-12" id="tickets-list">
				<span class="loader"></span>
				<script type="text/x-handlebars-template" id="tickets-list-template">
					{{#each tickets}}
						<div class="col-sm-6 col-md-4">
							<div class="panel panel-default">
								<div class="panel-body">
									<p class="text-center ticket-icon"><i class="fa fa-ticket fa-4x"></i></p>
									<p class="text-center ticket-name"><strong>{{{name}}}</strong></p>
									<p class="text-center ticket-price">{{pricerange base_prices prices}}</p>
									<a role="button" class="btn btn-primary btn-block btn-sm add-ticket" data-id="{{id}}">Add</a>
								</div>
							</div>
						</div>
						{{ticket-list-clearfix @index}}
					{{/each}}
				</script>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12" id="package-list">
				<span class="loader"></span>
				<script type="text/x-handlebars-template" id="package-list-template">
					{{#each packages}}
						<div class="col-sm-6 col-md-4">
							<div class="panel panel-default">
								<div class="panel-body">
									<p class="text-center ticket-icon"><i class="fa fa-tags fa-4x"></i></p>
									<p class="text-center ticket-name"><strong>{{{name}}}</strong></p>
									<p class="text-center ticket-price">{{pricerange base_prices prices}}</p>
									<div class="panel-group panel-packages" role="tablist">
										<div class="panel panel-warning">
											<div class="panel-heading" role="tab">
												<h5 class="panel-title" id="-collapsible-list-group-">
													<a class="collapsed" data-toggle="collapse" href="#package-collapse-{{id}}" aria-expanded="false" aria-controls="package-collapse-{{id}}">
														Package Details <span class="caret"></span>
													</a>
													<a class="anchorjs-link" href="#-collapsible-list-group-"><span class="anchorjs-icon"></span></a>
												</h5>
											</div>
											<div id="package-collapse-{{id}}" class="panel-collapse collapse" role="tabpanel" aria-expanded="false" style="height: 0px;">
												{{#if courses}}
													<h5>Courses</h5>
													<ul class="list-group">
														{{#each courses}}
															<li class="list-group-item package-ticket-item">
																<span class="badge">{{pivot.quantity}}</span>
																<i class="fa fa-graduation-cap fa-fw"></i> {{{name}}}
															</li>
														{{/each}}
													</ul>
												{{/if}}
												{{#if tickets}}
													<h5>Tickets</h5>
													<ul class="list-group">
														{{#each tickets}}
															<li class="list-group-item package-ticket-item">
																<span class="badge">{{pivot.quantity}}</span>
																<i class="fa fa-ticket fa-fw"></i> {{{name}}}
															</li>
														{{/each}}
													</ul>
												{{/if}}
												{{#if addons}}
													<h5>Addons</h5>
													<ul class="list-group">
														{{#each addons}}
															<li class="list-group-item package-ticket-item">
																<span class="badge">{{pivot.quantity}}</span>
																<i class="fa fa-cubes fa-fw"></i> {{{name}}}
															</li>
														{{/each}}
													</ul>
												{{/if}}
												{{#if accommodations}}
													<h5>Accommodations</h5>
													<ul class="list-group">
														{{#each accommodations}}
															<li class="list-group-item package-ticket-item">
																<span class="badge">{{pivot.quantity}}</span>
																<i class="fa fa-bed fa-fw"></i> {{{name}}}
															</li>
														{{/each}}
													</ul>
												{{/if}}
											</div>
										</div>
									</div>
									<a role="button" class="btn btn-warning btn-block btn-sm add-package" data-id="{{id}}">Add</a>
								</div>
							</div>
						</div>
					{{/each}}
				</script>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12" id="course-list">
				<span class="loader"></span>
				<script type="text/x-handlebars-template" id="course-list-template">
					{{#each courses}}
						<div class="col-sm-6 col-md-4">
							<div class="panel panel-default">
								<div class="panel-body">
									<p class="text-center ticket-icon"><i class="fa fa-graduation-cap fa-4x"></i></p>
									<p class="text-center ticket-name"><strong>{{{name}}}</strong></p>
									<p class="text-center ticket-price">{{pricerange base_prices prices}}</p>
									<div class="panel-group panel-packages" role="tablist">
										<div class="panel panel-info">
											<div class="panel-heading" role="tab">
												<h5 class="panel-title" id="-collapsible-list-group-">
													<a class="collapsed" data-toggle="collapse" href="#course-collapse-{{id}}" aria-expanded="false" aria-controls="course-collapse-{{id}}">
														Course Details <span class="caret"></span>
													</a>
													<a class="anchorjs-link" href="#-collapsible-list-group-"><span class="anchorjs-icon"></span></a>
												</h5>
											</div>
											<div id="course-collapse-{{id}}" class="panel-collapse collapse" role="tabpanel" aria-expanded="false" style="height: 0px;">
												{{#if trainings}}
													<h5>Class</h5>
													<ul class="list-group">
														{{#each trainings}}
															<li class="list-group-item package-ticket-item">
															<span class="badge">{{pivot.quantity}}</span>
																<i class="fa fa-graduation-cap fa-fw"></i> {{{name}}}
															</li>
														{{/each}}
													</ul>
												{{/if}}
												{{#if tickets}}
													<h5>Tickets</h5>
													<ul class="list-group">
														{{#each tickets}}
															<li class="list-group-item package-ticket-item">
																<span class="badge">{{pivot.quantity}}</span>
																<i class="fa fa-ticket fa-fw"></i> {{{name}}}
															</li>
														{{/each}}
													</ul>
												{{/if}}
											</div>
										</div>
									</div>
									<a role="button" class="btn btn-info btn-block btn-sm add-course" data-id="{{id}}">Add</a>
								</div>
							</div>
						</div>
					{{/each}}
				</script>
			</div>
		</div>
	</div>
</div>
