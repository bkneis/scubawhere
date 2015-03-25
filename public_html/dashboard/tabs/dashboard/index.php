<div id="wrapper" class="clearfix">
<div class="row" id="row1">

  <div class="col-md-5">
    <div class="panel panel-default" id="todays-stats">
      <div class="panel-heading">
        <h4 class="panel-title">Today's Stats</h4>
      </div>
      <div style="min-height:250px;" class="panel-body">
      </div>
    </div>
  </div>

  <div class="col-md-5">
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

  <div class="col-md-7">
    <div class="panel panel-default" id="feedback-form">
      <div class="panel-heading">
        <h4 class="panel-title">Feedback Form</h4>
      </div>
      <div class="panel-body">
        <form id="feedback-form">

          <div class="form-row">
            <label class="field-label">Tab * : </label>
            <input style="width:100%" type="text" name="name">
          </div>

          <div class="form-row">
            <label class="field-label">Feature : </label>
            <input style="width:100%" type="text" name="name">
          </div>

          <div class="form-row">
            <label class="field-label">Issue * : </label>
            <textarea style="width:100%" name="branch_address" rows="3"></textarea>
          </div>

          <button class="btn btn-primary btn-lg text-uppercase pull-right" id="send-feedback">Submit Feedback</button>

        </form>
      </div>
    </div>
  </div>
  </div>
</div><!-- #wrapper -->

<script type="text/x-handlebars-template" id="todays-sessions-widget">
      <div class="col-md-7">
    <div class="panel panel-default" id="todays-sessions">
      <div class="panel-heading">
        <h4 class="panel-title">Todays Trips</h4>
      </div>
      <div style="min-height:250px;" class="panel-body">
        <table class="bluethead">
          <thead>
            <tr class="bg-primary">
              <th>Trip</th>
              <th>Boat</th>
              <th>Capacity</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody id="sessions-list">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </script>

  <script type="text/x-handlebars-template" id="setup-wizard">
    <div class="col-md-7">
      <div class="panel panel-default" id="setup-wizard">
        <div class="panel-heading">
          <h4 class="panel-title">Setup Wizard</h4>
        </div>
        <div style="min-height:250px;" class="panel-body">
          <p>Welcome to scubawhereRMS! So we can get you all set up with our system, 
          this wizard will take you through our system and ask that you fill in some 
          information about your dive operation.</p>
          <p>So lets get started. Click start wizard to begin the configuration.</p>
            <button id="start-wizard" class="btn btn-success text-uppercase">Start wizard</button>
        </div>
      </div>
    </div>
  </script>

  <script type="text/x-handlebars-template" id="today-session-template">
    {{#each sessions}}
      <tr class="accordion-header" data-id="{{id}}" id="today-session-{{id}}">
        <td>{{trip.name}}</td>
        <td>{{boat.name}}</td>
        <td>{{getPer capacity}}</td>
        <td>{{getTime start}} - {{getEnd start trip.duration}}</td>
      </tr>
      <tr class="accordion-body accordion-{{id}}">
        <td colspan="9" style="overflow: auto;">
          <table id="customers-{{id}}" class="table table-striped table-bordered cust-tbl" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th style="color:#313131">Name</th>
                <th style="color:#313131">Email</th>
                <th style="color:#313131">Country</th>
                <th style="color:#313131">Phone Number</th>
              </tr>
            </thead>
            <tbody id="customer-table-{{id}}">
            </tbody>
          </table>
        </td>
      </tr>
    {{else}}
      <tr><td colspan="7" style="text-align: center;">You have no sessions today.</td></tr>
    {{/each}}
  </script>

  <script type="text/x-handlebars-template" id="customer-details-template">
    {{#each customers}}
      <tr>
        <td>{{firstname}} {{lastname}}</td>
        <td>{{email}}</td>
        <td>{{country}}</td>
        <td>{{phone}}</td>
      </tr>
    {{/each}}
  </script>

  <script type="text/x-handlebars-template" id="tour-nav-wizard">
    <div class="col-md-10">
      <ul class="nav nav-wizard" role="tablist">
        <li role="presentation" class="tour-progress" data-position="1" data-target="#accommodations">
          <a id="acom-tab" href="javascript:void(0)" role="tab" class="selected" data-toggle="tab">
            <span class="step-description">Accommodations</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="2" data-target="#agents">
          <a id="agent-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Agents</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="3" data-target="#locations">
          <a id="location-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Locations</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="4" data-target="#boats">
          <a id="boat-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Boats</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="5" data-target="#trips">
          <a id="trip-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Trips</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="6" data-target="#tickets">
          <a id="ticket-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Tickets</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="7" data-target="#classes">
          <a id="class-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Classes</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="8" data-target="#courses">
          <a id="course-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Courses</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="9" data-target="#packages">
          <a id="package-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Packages</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="10" data-target="#add-ons">
          <a id="addon-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Add ons</span>
          </a>
        </li>
      </ul>
      </div>
      <div class="col-md-2" style="min-height:80px" id="tour-button">
        <button id="tour-next-step" style="margin-top:20px" class="btn btn-success text-uppercase">Next Step</button>
        <button id="tour-finish" style="display:none; margin-top:20px;" class="btn btn-success text-uppercase">Finish tour</button>
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
              </tr>
              {{#each payments}}
              <tr>
                <td>{{received_at}}</td>
                <td>{{currency}} {{amount}}</td>
                <td>{{paymentgateway.name}}</td>
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

<link rel="stylesheet" type="text/css" href="/common/css/datatables.css">

<script src="/js/Controllers/Session.js"></script>
<script src="/js/Controllers/Trip.js"></script>
<script src="/js/Controllers/Booking.js"></script>
<script src="/common/js/jquery/jquery.datatables.min.js"></script>
<script src="/tabs/dashboard/js/script.js"></script>
<script src="/tabs/dashboard/js/bookings.js"></script>
