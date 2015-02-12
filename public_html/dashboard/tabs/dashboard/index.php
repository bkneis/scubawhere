<div id="wrapper" class="clearfix">
  <div class="col-md-7">
<div class="row" id="row1">

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
        <p>Welcome to scub rms ...</p>
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
  </div> 

  <script type="text/x-handlebars-template" id="tour-nav-wizard">
  <div class="col-md-10">
    <ul class="nav nav-wizard" role="tablist">
      <li role="presentation" class="active">
        <a href="javascript:void(0)" id="prog-acom" role="tab" class="selected" data-toggle="tab" data-target="#source-tab">
          <span class="step-description">Accommodations</span>
        </a>
      </li>
      <li role="presentation">
        <a href="javascript:void(0)" role="tab" id="prog-agents" data-toggle="tab" data-target="#ticket-tab">
          <span class="step-description">Agents</span>
        </a>
      </li>
      <li role="presentation">
        <a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#customer-tab">
          <span class="step-description">Locations</span>
        </a>
      </li>
      <li role="presentation">
        <a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#session-tab">
          <span class="step-description">Boats</span>
        </a>
      </li>
      <li role="presentation">
        <a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#addon-tab">
          <span class="step-description">Trips</span>
        </a>
      </li>
      <li role="presentation">
        <a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#accommodation-tab">
          <span class="step-description">Tickets</span>
        </a>
      </li>
      <li role="presentation">
        <a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#extra-tab">
          <span class="step-description">Packages</span>
        </a>
      </li>
      <li role="presentation">
        <a href="javascript:void(0)" role="tab" data-toggle="tab" data-target="#summary-tab">
          <span class="step-description">Add ons</span>
        </a>
      </li>
    </ul>
    </div>
    <div class="col-md-2" style="min-height:80px" id="tour-button">
      <button id="tour-next-step" class="btn btn-success text-uppercase">Next Step</button>
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
        <div style="float: left; width: 180px; margin-right: 10px; border-right: 1px solid #C3D9F4;">
          {{#if payments}}
          <h4 class="text-center">Recieved Transactions</h4>
          <table style="width: 160px;" class="table">
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
              <td></td>
              <td class="table-sum">{{currency}} {{sumPaid}}</td>
              <td>{{remainingPay}}</td>
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
</div><!-- #wrapper -->

<link rel="stylesheet" type="text/css" href="/common/css/datatables.css">

<script src="/dashboard/js/Controllers/Session.js"></script>
<script src="/dashboard/js/Controllers/Trip.js"></script>
<!--<script src="/dashboard/js/tour.js"></script>-->
<script src="/common/js/jquery/jquery.datatables.min.js"></script>
<script src="tabs/dashboard/js/script.js"></script>
<script src="js/Controllers/Booking.js"></script>
<script src="tabs/dashboard/js/bookings.js"></script>





