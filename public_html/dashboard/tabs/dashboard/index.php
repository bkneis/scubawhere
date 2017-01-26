<div id="wrapper" class="clearfix">
  <div class="row" id="row1">

    <!-- <div class="col-md-5">
      <div class="panel panel-default" id="todays-stats">
        <div class="panel-heading">
          <h4 class="panel-title">Social Media Stats</h4>
        </div>
        <div style="min-height:250px;" class="panel-body">
          <div id="social-media-stats"></div>
          <h5>Social Media integration is coming soon</h5>
          Keep an eye on this space!
        </div>
      </div>
    </div> -->

  </div>

  <div class="row" id="row2">
    <div class="col-md-6">
      <div class="panel panel-default" id="recent-bookings">
        <div class="panel-heading">
          <h4 class="panel-title">Recent Bookings</h4>
        </div>
        <div class="panel-body">
          <table class="bluethead">
            <thead>
              <tr class="bg-primary">
                <th>Ref</th>
                <th>Customer</th>
                <th>Phone</th>
                <th></th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody id="booking-list">
              <tr><td colspan="9" style="text-align: center;"> </td></tr>
              <tr><td colspan="9" style="text-align: center;"><i class="fa fa-refresh fa-spin fa-lg fa-fw"></i></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="panel panel-default" id="feedback-div">
        <div class="panel-heading">
          <h4 class="panel-title">Feedback Form</h4>
        </div>
        <div class="panel-body">
          <form id="feedback-form">

            <div class="form-row">
              <label class="field-label">Tab * : </label>
              <select name="tab" style="width:100%">
                <option value="">Please select an area of the RMS</option>
                <option>General</option>
                <optgroup label="Tabs">
                  <option>Dashboard</option>
                  <option>Add Booking</option>
                  <option>Manage Bookings</option>
                  <option>Calendar</option>
                  <option>Scheduling</option>
                  <option>Pick-Up Schedule</option>
                  <option>Financial Reports</option>
                  <option>Help & FAQ</option>
                  <option>Settings</option>
                </optgroup>
                <optgroup label="Management Tabs">
                  <option>Accommodations</option>
                  <option>Add-Ons</option>
                  <option>Agents</option>
                  <option>Boats</option>
                  <option>Classes</option>
                  <option>Courses</option>
                  <option>Locations</option>
                  <option>Packages</option>
                  <option>Tickets</option>
                  <option>Trips</option>
                </optgroup>
              </select>
            </div>

            <div class="form-row">
              <label class="field-label">Feature : </label>
              <input style="width:100%" type="text" name="feature">
            </div>

            <div class="form-row">
              <label class="field-label">Issue * : </label>
              <textarea style="width:100%" name="issue" rows="3"></textarea>
            </div>

            <input type="hidden" name="_token">

            <button type="submit" class="btn btn-primary btn-lg text-uppercase pull-right">Submit Feedback</button>

          </form>
        </div>
      </div>
    </div>
  </div>

  <div id="modalWindows" style="height: 0;"></div>

<!-- Modal -->
<div class="modal fade" id="modal-intro" tabindex="-1" role="dialog" aria-labelledby="Welcome to scubawhere RMS">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
        	<div class="modal-header">
        		<h4 class="modal-title">Welcome to scubawhereRMS</h4>
      		</div>
      		<div class="modal-body">
				<p>
					To get started, you need to configure scubawhereRMS. We will take you through the setup, step by step, explaining each element on the pages to help you set the system up to your business needs.
			  	</p>
				<p>
					<strong>So, let's get started!</strong>
				</p>
			</div>
			<div class="modal-footer">
				<button id="btn-start-wizard" type="button" class="btn btn-primary">Let's Go</button>
			</div>
		</div>
	</div>
</div>

  <script type="text/x-handlebars-template" id="todays-sessions-widget">
    <div class="col-md-12">
      <div class="panel panel-default" id="todays-sessions">
        <div class="panel-heading">
          <h4 class="panel-title">Upcoming Trips & Classes</h4>
        </div>
        <div style="min-height:250px;" class="panel-body">
          <table class="bluethead">
            <thead>
              <tr class="bg-primary">
                <th></th>
                <th>Session</th>
                <th>Boat</th>
                <th>Availability</th>
                <th>Date</th>
                <th>Trip Manifest</th>
              </tr>
            </thead>
            <tbody id="sessions-list">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </script>

  <script type="text/x-handlebars-template" id="social-media-template">
    {{#each facebook}}
      <p><span style="font-weight:bold; font-size:22px">{{data}}</span> {{title}}</p>
    {{/each}}
  </script>

  <script type="text/x-handlebars-template" id="today-session-template">
    {{#each sessions}}
      <tr class="accordion-header" data-id="{{id}}" data-type="{{#if trip}}trip{{else}}class{{/if}}" id="today-session-{{id}}">
        <td>{{#if trip}}<i class="fa fa-ship"></i>{{else}}<i class="fa fa-graduation-cap"></i>{{/if}}</td>
        <td>{{#if trip}}{{{trip.name}}}{{else}}{{{training.name}}}{{/if}}</td>
        <td>{{{boat.name}}}</td>
		<td>{{getPer capacity}}</td>
        <td>{{friendlyDate start}} - {{tripFinish start trip.duration}}</td>
        <td><a onclick="showModalWindowManifest({{id}}, {{#if trip}}'trip' {{else}}'training' {{/if}})">View Manifest</a> </td>
      </tr>
      <tr class="accordion-body accordion-{{id}}">
        <td colspan="9" style="overflow: auto;">
          <table id="customers-{{id}}" class="table table-striped table-bordered cust-tbl" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th style="color:#313131">Booking Ref</th>
                <th style="color:#313131">Booking Paid / Booking Amount</th>
                <th style="color:#313131">Name</th>
                <th style="color:#313131">Phone Number</th>
                <th style="color:#313131">Ticket</th>
              </tr>
            </thead>
            <tbody id="customer-table-{{id}}">
            </tbody>
          </table>
        </td>
      </tr>
    {{else}}
      <tr><td colspan="7" style="text-align: center;">No upcoming trips or classes.</td></tr>
    {{/each}}
  </script>

  <script type="text/x-handlebars-template" id="customer-details-template">
    {{#each customers}}
      <tr>
		<td><a class="view-booking">{{pivot.reference}}</a></td>
		<td>{{getRemainingBalance pivot}}</td>
        <td>{{{firstname}}} {{{lastname}}}</td>
        <td>{{phone}}</td>
		{{#if pivot.ticket_id}}
			<td>{{getTicketName pivot.ticket_id}}</td>
		{{else}}
			<td>{{getCourseName pivot.course_id}}</td>
		{{/if}}
      </tr>
    {{/each}}
  </script>

  <script type="text/x-handlebars-template" id="tour-nav-wizard">
    <div>
      <div style="width:88%; margin-left:20px; float:left" class="">

        <ul class="nav tnav-wizard" role="tablist">

          <li role="presentation" class="tour-progress" data-position="1" data-target="#settings">
            <a id="setting-tab" href="javascript:void(0)" role="tab" class="selected" data-toggle="tab">
              <span class="step-description">Settings</span>
            </a>
          </li>
          <li id="accomli" role="presentation" class="tour-progress" data-position="2" data-target="#accommodations">
            <a id="acom-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Accommodations</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="3" data-target="#agents">
            <a id="agent-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Agents</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="4" data-target="#locations">
            <a id="location-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Locations</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="5" data-target="#boats">
            <a id="boat-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Boats</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="6" data-target="#trips">
            <a id="trip-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Trips</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="7" data-target="#tickets">
            <a id="ticket-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Tickets</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="8" data-target="#classes">
            <a id="class-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Classes</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="9" data-target="#courses">
            <a id="course-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Courses</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="10" data-target="#add-ons">
            <a id="addon-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Add ons</span>
            </a>
          </li>
          <li role="presentation" class="tour-progress" data-position="11" data-target="#packages">
            <a id="package-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
              <span class="step-description">Packages</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="" style="min-height:80px; width:10%; float:left" id="tour-button">
        <button id="tour-next-step" style="margin-top:20px" class="btn btn-success text-uppercase">Next Step</button>
        <button id="tour-finish" style="display:none; margin-top:20px;" class="btn btn-success text-uppercase">Finish tour</button>
      </div>
    </div>
  </script>

  <script type="text/x-handlebars-template" id="booking-list-item-template">
    {{#each bookings}}
      <tr class="accordion-header" data-id={{id}}>

        <td>{{reference}}</td>
        <td>{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}</td>
        <td>{{lead_customer.phone}}</td>
        <td>{{lead_customer.country.abbreviation}}</td>
        <td>{{currency}} {{decimal_price}}</td>
      </tr>
      <tr class="accordion-body accordion-{{id}}">
        <td colspan="5" style="overflow: auto;">
          <div>
            {{#if payments}}
            <p class="text-center"><strong>Recieved Transactions</strong></p>
            <table class="table">
              <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Via</th>
                <th>Ref</th>
              </tr>
              {{#each payments}}
              <tr>
                <td>{{received_at}}</td>
                <td>{{currency}} {{amount}}</td>
                <td>{{paymentgateway.name}}</td>
                <td>{{card_ref}}</td>
              </tr>
              {{/each}}
              {{#each refunds}}
                <tr>
                  <td>{{received_at}}</td>
                  <td class="text-danger">{{currency}} -{{amount}}</td>
                  <td>{{paymentgateway.name}} (refund)</td>
                  <td>{{card_ref}}</td>
                </tr>
                {{/each}}
              <tr>
                <td><strong>Total</strong></td>
                <td class="table-sum">{{currency}} {{sumPaid}}</td>
                <td><strong>Remaining</strong> {{remainingPay}}</td>
              </tr>
            </table>
            {{else}}
            <h5 class="text-center text-muted">No transactions yet</h5>
            {{/if}}
          </div>
          {{addTransactionButton}}
          {{editButton}}
        </td>
      </tr>
      <tr class="accordion-spacer accordion-{{id}}"></tr>
    {{else}}
      <tr><td colspan="7" style="text-align: center;">You have no bookings yet.</td></tr>
    {{/each}}
  </script>

  <script type="text/x-handlebars-template" id="manifest-template">
    <div id="modal-{{id}}" class="reveal-modal xxlarge">
      <h3>{{{trip.name}}} - Trip Manifest</h3>
      <table style="margin-top: 2em;" id="customer-data-table" class="table table-striped">
        <thead>
        <tr>
          <th style="color:#313131">Booking Ref</th>
          <th style="color:#313131; width: 20%">Payments made</th>
          <th style="color:#313131;">Name</th>
          <th style="color:#313131"><span style="display: none;">Country</span></th>
          <th style="color:#313131">Phone</th>
          <th style="color:#313131">Ticket</th>
          <th style="color:#313131">Last Dive</th>
          <th style="color:#313131; width:15%;">Notes</th>
          <th style="color:#313131">Fins</th>
          <th style="color:#313131">BCD</th>
          <th style="color:#313131">Wetsuit</th>
        </tr>
        </thead>
        <tbody id="customers-table">
        </tbody>
      </table>
      <a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
    </div>
  </script>

  <script type="text/x-handlebars-template" id="class-manifest-template">
    <div id="modal-{{id}}" class="reveal-modal xxlarge">
      <h3>{{{training.name}}} - Class Manifest</h3>
      <table style="margin-top: 2em;" id="customer-data-table" class="table table-striped">
        <thead>
        <tr>
          <th style="color:#313131">Booking Ref</th>
          <th style="color:#313131; width:15%;">Payments made</th>
          <th style="color:#313131">Name</th>
          <th style="color:#313131"><span style="display: none;">Country</span></th>
          <th style="color:#313131">Phone</th>
          <th style="color:#313131">Course</th>
          <th style="color:#313131">Last Dive</th>
          <th style="color:#313131">Notes</th>
          <th style="color:#313131">Fins</th>
          <th style="color:#313131">BCD</th>
          <th style="color:#313131">Wetsuit</th>
        </tr>
        </thead>
        <tbody id="customers-table">
        </tbody>
      </table>
      <a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
    </div>
  </script>

  <script src="/dashboard/tabs/dashboard/js/script.js"></script>
  <script src="/dashboard/tabs/dashboard/js/bookings.js"></script>
</div><!-- #wrapper -->
