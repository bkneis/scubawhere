<div id="wrapper" class="clearfix">

    <div class="col-md-7">

        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <script type="text/x-handlebars-template" id="today-session-template">
                {{#each sessions}}
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-{{id}}">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" 
                            href="#session-{{id}}" aria-expanded="true" aria-controls="session-{{id}}">
                              {{trip.name}} 
                            </a><i class="pull-right">{{getTime start}}</i>
                        </h4>
                    </div>
                    <div id="session-{{id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{id}}">
                      <div class="panel-body">
                        <div style="width:200px; float:right">
                        <div class="progress">
                          <div class="progress-bar" role="progressbar" aria-valuenow="60" 
                          aria-valuemin="0" aria-valuemax="100" style="width: {{getPer capacity}};"> {{getPer capacity}}</div>
                        </div>
                        </div>
                        <p><strong>Boat :</strong> {{boat.name}} | Capacity : {{capacity}}</p>
                        <p id="locations-{{id}}"><strong>Location : </strong></p>
                        <div class="input-group"> <span class="input-group-addon">Filter</span>
                            <input id="filter" type="text" class="form-control" placeholder="Customer name or Booking code...">
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Lead Name</th>
                                    <th>Lead Phone Number</th>
                                    <th></th>
                                    <th>Lead Email</th>
                                </tr>
                            </thead>
                            <tbody id="customers-table-{{id}}" class="searchable">
                                <tr>
                                    <td>Code</td>
                                    <td>Bryan Kneis</td>
                                    <td>0769957463</td>
                                    <td>UK</td>
                                    <td>bryan@scuba.com</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                {{else}}
                    <p>No trips running today</p>
                {{/each}}
            </script>
        </div>

        <script type="text/x-handlebars-template" id="customer-details-template">
          <tr>
              <td>{{reference}}</td>
              <td>{{lead_customer.name}}</td>
              <td>{{lead_customer.phone}}</td>
              <td>{{lead_customer.email}}</td>
          </tr>
          /*<div style="display:none">
            <p>More info on customers</p>
          </div>*/
        </script>

    </div>

    <div class="col-md-5">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">Recent Bookings</h4>
      </div>
      <div class="panel-body">
        <table class="bluethead">
          <thead>
            <tr class="bg-primary">
              <!--<th width="10"></th> <!-- source icon -->
              <!--<th width="10"></th> <!-- saved/reserved/confirmed icon -->
              <!--<th width="10"></th> <!-- payments -->
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

          <a href="mailto:{{lead_customer.email}}" class="mailto"><button class="btn btn-default pull-right"><i class="fa fa-envelope"></i> &nbsp;Contact customer</button></a>
        </td>
      </tr>
      <tr class="accordion-spacer accordion-{{id}}"></tr>
    {{else}}
      <tr><td colspan="7" style="text-align: center;">You have no bookings yet.</td></tr>
    {{/each}}
  </script>

</div>

<script src="/dashboard/js/Controllers/Session.js"></script>
<script src="/dashboard/js/Controllers/Trip.js"></script>
<script src="tabs/dashboard/js/script.js"></script>
<script src="js/Controllers/Booking.js"></script>
<script src="tabs/manage-bookings/js/script.js"></script>



