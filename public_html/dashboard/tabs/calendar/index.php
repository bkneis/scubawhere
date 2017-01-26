<div id="wrapper" class="clearfix">
   <div class="row">
      <div class="col-md-4">
         <div class="panel panel-default" id="calendar-filters">
            <div class="panel-heading">
               <h4 class="panel-title">Filters</h4>
				<!--<i class="fa fa-minus pull-right" data-toggle="collapse" data-target="#filters"></i>-->
            </div>
            <div class="panel-body collapse in" id="filters">
               <div id="filter-types" class="btn-group" role="group">
                  <button id="filter-all"            display="all"            type="button" class="btn btn-default filter-type">All</button>
                  <button id="filter-trips"          display="trips"          type="button" class="btn btn-default filter-type btn-primary">Trips</button>
                  <button id="filter-accommodations" display="accommodations" type="button" class="btn btn-default filter-type">Accommodations</button>
                  <button id="filter-classes"        display="classes"        type="button" class="btn btn-default filter-type">Classes</button>
               </div>
               <div style="clear:both; padding-top:10px;"></div>
               <div id="jump-to-date" class="form-row">
                  <div class="input-group">
                     <label class="input-group-addon">Jump to : </label>
                     <input style="width:200px; float:left;" id="jump-date" type="text" class="form-control datepicker" data-date-format="YYYY-MM-DD" name="jumpto" placeholder="YYYY-MM-DD">
                     <!--<button id="remove-jump" style="display:none;" class="btn btn-danger">Clear</button>-->
                  </div>
               </div>
               <div style="clear:both"></div>
               <div id="filter-settings" class="form-row"></div>
               <script type="text/x-handlebars-template" id="trip-filter-template">
                  <div class="input-group">
                  		<label class="input-group-addon">Add Filter : </label>
                  		<select id="filter-options" onchange="addTripFilter(this.value)">
                  				<option value="all">Please Select ..</option>
                  				<option value="boat">Boats</option>
                  				<option value="trip">Trips</option>
                  		</select>
                  </div>
               </script>
               <script type="text/x-handlebars-template" id="accom-list-template">
                  <div class="input-group">
                  		<label class="input-group-addon">Filter by : </label>
                  		<select id="accoms" class="filter">
                  				<option value="all">Please Select ..</option>
                  				{{#each accoms}}
                  					<option value="{{id}}">{{{name}}}</option>
                  				{{/each}}
                  		</select>
                  </div>
               </script>
               <script type="text/x-handlebars-template" id="class-list-template">
                  <div class="input-group">
                  		<label class="input-group-addon">Filter by : </label>
                  		<select id="classes" class="filter">
                  				<option value="all">Please Select ..</option>
                  				{{#each classes}}
                  					<option value="{{id}}">{{{name}}}</option>
                  				{{/each}}
                  		</select>
                  </div>
               </script>
               <script type="text/x-handlebars-template" id="boats-list-template">
                  <p>
                  		<div class="input-group">
                  				<label class="input-group-addon">Boat : </label>
                  				<select class="filter" id="boats">
                  						<option value="all"></option>
                  						{{#each boats}}
                  							<option value="{{id}}">{{{name}}}</option>
                  						{{/each}}
                  				</select>
                  				<button id="remove-boats-filter" class="btn-danger remove-room" style="height:100%;">&#215;</button>
                  		</div>
                  </p>
               </script>
               <script type="text/x-handlebars-template" id="trips-list-template">
                  <p>
                  		<div class="input-group">
                  				<label class="input-group-addon">Trip : </label>
                  				<select class="filter" id="trips">
                  						<option value="all"></option>
                  						{{#each trips}}
                  						<option value="{{id}}">{{{name}}}</option>
                  						{{/each}}
                  				</select>
                  				<button id="remove-trips-filter" style="height:100%;" class="btn-danger remove-room">&#215;</button>
                  		</div>
                  </p>
               </script>
               <div id="filter"></div>
            </div>
            <!-- .panel-body -->
         </div>
         <!-- .panel -->
      </div>
      <!-- .col-md-4 -->

		<div class="col-md-8">
			<div id='calendar'></div>
		</div>
   </div>
   <!-- .row -->
   <div id="modalWindows" style="height: 0;">
      <script type="text/x-handlebars-template" id="session-template">
         <div id="modal-{{id}}" class="reveal-modal">
         		<h3>{{{trip.name}}}</h3>
         		<table style="margin-top: 2em;" class="striped">
         				{{#if sameDay}}
         				<tr>
         						<td><strong>Date</strong></td>
         						<td>{{date start}}</td>
         				</tr>
         				<tr>
         						<td><strong>Start - End time</strong></td>
         						<td>
         								<span class="enddatetime">{{hours start}}:{{minutes start}} - {{hours end}}:{{minutes end}}</span>
         						</td>
         				</tr>
         				{{else}}
         				<tr>
         						<td><strong>Starting</strong></td>
         						<td>{{date start}} - {{hours start}}:{{minutes start}}</td>
         				</tr>
         				<tr>
         						<td><strong>Ending</strong></td>
         						<td>
         								{{date end}} - {{hours end}}:{{minutes end}}
         						</td>
         				</tr>
         				{{/if}}
         				<tr>
         						<td><strong>Boat</strong></td>
         						<td>
         								{{{session.boat.name}}}
         						</td>
         				</tr>
         				<tr>
         						<td><strong>Tickets Available</strong></td>
         						<td>{{#unless ticketsLeft}}
         								<span class="soldout">SOLD OUT</span>
         								{{else}}
         									{{capacityString}} | <a href="#add-booking">BOOK NOW</a>
         								{{/unless}}
         						</td>
         				</tr>
         				<tr>
         						<td><strong>Customer Information</strong></td>
         						<td><a class="close-modal" title="Abort" onclick="showModalWindowManifest({{session.id}}, 'trip')">View trip manifest</a></td>
         			</table>
         			<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
         	</div>
      </script>
      <script type="text/x-handlebars-template" id="accommodation-template">
         <div id="modal-{{id}}" class="reveal-modal">
         		<h3>{{{title}}}</h3>
         		<table style="margin-top: 2em;">
         				<tr>
         						<td><strong>Date</strong></td>
         						<td>{{date start}}</td>
         				</tr>
         				<tr>
         						<td><strong>Rooms Available</strong></td>
         						<td>
         								{{#unless available}}
         									<span class="soldout">SOLD OUT</span>
         								{{else}}
         								{{getRemaining available booked}} out of {{available}}</a>
         							{{/unless}}
         					</td>
         			</tr>
					<tr>
						<td><strong>Customer Information</strong></td>
						<td><a class="close-modal" title="Abort" onclick="showModalWindowManifest({{accommodation_id}}, 'accommodation', '{{convertDate start}}')">View manifest</a></td>
					</tr>
         	</table>
         	<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
         </div>
      </script>
      <script type="text/x-handlebars-template" id="class-template">
         <div id="modal-{{id}}" class="reveal-modal">
         		<h3>{{{title}}}</h3>
         		<table style="margin-top: 2em;" class="striped">
         				<tr>
         						<td><strong>Date</strong></td>
         						<td>{{date start}}</td>
         				</tr>
         				<tr>
         						<td><strong>Start - End time</strong></td>
         						<td>
         								<span class="enddatetime">{{hours start}}:{{minutes start}} - {{hours end}}:{{minutes end}}</span>
         						</td>
         				</tr>
         				<tr>
         						<td><strong>Number of students</strong></td>
         						<td>{{session.capacity.[0]}}</td>
         				</tr>
         				<tr>
         						<td><strong>Customer Information</strong></td>
         						<td><a class="close-modal" title="Abort" onclick="showModalWindowManifest({{session.id}}, 'class')">View trip manifest</a></td>
         			</table>
         			<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
         	</div>
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
						  <th style="color:#313131">Addons</th>
						<th style="color:#313131">Last Dive</th>
						<th style="color:#313131; width:15%;">Notes</th>
						<th style="color:#313131">Fins</th>
						<th style="color:#313131">BCD</th>
						<th style="color:#313131">Wetsuit</th>
                  		<th style="color:#313131">Cylinder Size</th>
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
                     <th style="color:#313131">Cylinder Size</th>
         			</tr>
         	</thead>
         	<tbody id="customers-table">
         	</tbody>
         		</table>
         		<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
         </div>
      </script>
	   <script type="text/x-handlebars-template" id="accommodation-manifest-template">
		   <div id="modal-{{accommodation.0.id}}-{{jFriendly date}}" class="reveal-modal xxlarge">
			   <h3>{{{accommodation.0.name}}} - Accommodation Manifest</h3>
			   <table style="margin-top: 2em;" id="customer-data-table" class="table table-striped">
				   <thead>
				   <tr>
					   <th style="color:#313131">Booking Ref</th>
					   <th style="color:#313131; width:15%;">Payments made</th>
					   <th style="color:#313131">Name</th>
					   <th style="color:#313131"><span style="display: none;">Country</span></th>
					   <th style="color:#313131">Phone</th>
					   <th style="color:#313131">Notes</th>
				   </tr>
				   </thead>
				   <tbody id="customers-table">
				   </tbody>
			   </table>
			   <a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
		   </div>
	   </script>
      <script type="text/x-handlebars-template" id="customer-rows-template">
         {{#each customers}}
         	<tr>
         <th>{{{firstname}}} {{{lastname}}}</th>
         <th>{{email}}</th>
         <th>{{country}}</th>
         <th>{{last_dive}}</th>
         <th>{{phone}}</th>
         </tr>
         {{else}} <tr><th colspan="4">No customers</th></tr>
         {{/each}}
      </script>
   </div>
   <script src="/dashboard/common/js/fullcalendar.min.js"></script>
   <script type="text/javascript" src="/dashboard/common/js/jquery/jquery.cookie.js"></script>
   <script type="text/javascript" src="/dashboard/common/js/jquery/jquery.collapsible.js"></script>
   <script src="/dashboard/tabs/calendar/js/script.js" type="text/javascript"></script>
</div>

