<div id="wrapper" class="clearfix">
	<div class="row">
		<div class="col-md-12">
			<ul class="nav nav-wizard" role="tablist">
				<li role="presentation" class="active">
					<a href="javascript:void(0)" class="selected" role="tab" data-toggle="tab" data-target="#source-tab">
						<span class="step-number">1</span>
						<span class="step-description">Sources</span>
					</a>
				</li>
				<li role="presentation">
					<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#ticket-tab">
						<span class="step-number">2</span>
						<span class="step-description">Tickets</span>
					</a>
				</li>
				<li role="presentation">
					<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#customer-tab">
						<span class="step-number">3</span>
						<span class="step-description">Customers</span>
					</a>
				</li>
				<li role="presentation">
					<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#session-tab">
						<span class="step-number">4</span>
						<span class="step-description">Trips</span>
					</a>
				</li>
				<li role="presentation">
					<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#addon-tab">
						<span class="step-number">5</span>
						<span class="step-description">Addons</span>
					</a>
				</li>
				<li role="presentation">
					<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#accommodation-tab">
						<span class="step-number">6</span>
						<span class="step-description">Accommodation</span>
					</a>
				</li>
				<li role="presentation">
					<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#extra-tab">
						<span class="step-number">7</span>
						<span class="step-description">Extra Info</span>
					</a>
				</li>
				<li role="presentation">
					<a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#summary-tab">
						<span class="step-number">8</span>
						<span class="step-description">Summary</span>
					</a>
				</li>
			</ul>

			<div class="row">
				<div class="col-md-9" id="booking-area-column">

					<div class="tab-content">

						<div role="tabpanel" class="tab-pane fade in active" id="source-tab">
							<?php require('1-sources.php'); ?>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="ticket-tab">
							<?php require('2-tickets.php'); ?>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="customer-tab">
							<?php require('3-customers.php'); ?>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="session-tab">
							<?php require('4-trips.php'); ?>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="addon-tab">
							<?php require('5-addons.php'); ?>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="accommodation-tab">
							<?php require('6-accommodation.php'); ?>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="extra-tab">
							<?php require('7-extra-info.php'); ?>
						</div>

						<div role="tabpanel" class="tab-pane fade" id="summary-tab">
							<?php require('8-summary.php'); ?>
						</div>

					</div>

				</div>

				<div class="col-md-3" id="booking-summary-column">
					<?php require('basket.php'); ?>
				</div>
			</div><!-- .row -->
		</div>
	</div><!-- .row -->

	<div id="modalWindows" style="height: 0;">
		<script type="text/x-handlebars-template" id="boatroom-select-modal-template">
			<div id="modal-boatroom-select" class="reveal-modal">
				<h4>Please select a cabin</h4>

				<p>The session you are assigning is overnight and there are multiple cabins available:</p>

				<div class="list-group">
					{{#each boatrooms}}
						<a href="javascript:void(0);" data-id="{{id}}" class="boatroom-select-option list-group-item list-group-radio">{{{name}}}</a>
					{{/each}}
				</div>

				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>
	</div>

	<!--Styling (Temporary)-->
	<link rel="stylesheet" href="/tabs/add-booking/css/style.css" type="text/css" />

	<!--Typeahead-->
	<script type="text/javascript" src="/common/vendor/typeahead/bootstrap3-typeahead.min.js"></script>

	<!--Basil LocalStorage Wrapper-->
	<script type="text/javascript" src="/common/vendor/basil.js/build/basil.min.js"></script>

	<!--My scripts-->
	<script src="/tabs/add-booking/js/script.js"></script>

</div>
