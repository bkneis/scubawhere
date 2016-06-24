<!--<script type="text/javascript" src="js/social-media.js"></script>-->
<div id="wrapper" class="clearfix">
	<div id="company-form-container">
		<script type="text/x-handlebars-template" id="company-form-template">
			<form id="update-company-form">
				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Account Information</h4>
							</div>
							<div class="panel-body">

								<!--<div class="form-row">
									<label class="field-label">Username : </label>
									<input type="text" id="username" value="{{username}}" class="form-control" disabled></input>
								</div>-->

								<div class="form-row">
									<label class="field-label">Main person of contact : </label>
									<input type="text" name="contact" value="{{contact}}" class="form-control"></input>
								</div>

								<!--<div class="form-row">
									<label class="field-label">Contact phone number : </label>
									<input class="form-control" type="text" id="phone" name="phone" value="{{phone}}">
								</div>-->

								<div class="form-row">
									<label class="field-label">Dive operator name : </label>
									<input type="text" name="name" value="{{name}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">Dive operator website : </label>
									<input type="text" name="website" placeholder="http://" value="{{website}}" class="form-control">
								</div>

								<div class="form-row" style="overflow:auto;">
									<label>Accepted Diving Instuitions</label>
									<div id="agencies">
										{{#each agencies}}
										<label class="certify">
											<input type="checkbox" name="agencies[]" value="{{id}}" checked>
											<strong>{{abbreviation}}</strong>
										</label>
										{{/each}}
										<p style="clear: both;"></p>
										{{#each other_agencies}}
										<label class="certify">
											<input type="checkbox" name="agencies[]" value="{{id}}">
											<strong>{{abbreviation}}</strong>
										</label>
										{{/each}}
									</div>
								</div>

								<!--<div class="form-row">
									<label class="field-label">Dive center bio : </label>
									<textarea name="description" rows="3" value="{{description}}"></textarea>
								</div>-->

								<!--<div class="form-row">
									<label class="field-label">Contact email address : </label>
									<input type="text" name="email" value="{{email}}" class="form-control"></input>
								</div>-->

								<!--<div class="form-row">
									<label class="field-label">Change Password : </label>
									<button id="send-password" class="btn btn-default btn-sm">Send password reminder email</button>
								</div>-->

								<div id="start-tour-div" class="form-row">
									<label class="field-label">Restart the tour : </label>
									<button id="start-wizard" class="btn btn-default text-uppercase">Start wizard</button>
								</div>

								<input type="submit" class="update-settings btn btn-primary btn-lg pull-right" value="SAVE">

							</div>
						</div>
					</div>

					<div class="col-md-6" style="float: right;">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Business Information</h4>
							</div>
							<div class="panel-body">

								<div class="form-row">
									<label class="field-label">Business Address 1 : </label>
									<input type="text" name="address_1" value="{{address_1}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">Business Address 2 : </label>
									<input type="text" name="address_2" value="{{address_2}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">City : </label>
									<input type="text" name="city" value="{{city}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">County / State : </label>
									<input type="text" name="county" value="{{county}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">Post code / zip code : </label>
									<input type="text" name="postcode" value="{{postcode}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">Business phone : </label>
									<input class="form-control" type="text" id="business_phone" name="business_phone" value="{{business_phone}}">
								</div>

								<div class="form-row">
									<label class="field-label">Business email : </label>
									<input type="text" name="business_email" value="{{business_email}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">Registration number : </label>
									<input type="text" name="registration_number" value="{{registration_number}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">VAT number : </label>
									<input type="text" name="vat_number" value="{{vat_number}}" class="form-control">
								</div>

								<input type="hidden" name="_token">
								<input type="submit" class="update-settings btn btn-primary btn-lg pull-right" value="SAVE">
							</div>
						</div>
					</div>

					<!--<div class="col-md-6" style="float: left">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Terms and conditions</h4>
							</div>
							<div class="panel-body">
								<div class="form-row">
									<!--<textarea style="width:100%" rows="10" id="terms" name="terms">{{terms}}</textarea>-->
								<!--</div>

								<input type="button" id="upload-terms" class="btn btn-primary btn-md pull-right" value="UPLOAD">
							</div>
						</div>
					</div>-->

					<!-- The feature has been pushed back to hammerhead release as discussed in issue SCUBA-238 -->
					<!--<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">Social Media Integration</h4>
							</div>
							<div class="panel-body">
								<div class="fb-login-button" scope="public_profile,email,read_insights" onlogin="checkLoginState();" data-max-rows="1" data-size="large" data-show-faces="false" data-auto-logout-link="true"></div>
							</div>
						</div>
					</div>-->

				</div><!-- .row -->
			</form>
		</script>
	</div>

	<script type="text/x-handlebars-template" id="errors-template">
		<div class="yellow-helper errors" style="color: #E82C0C;">
			<strong>There are a few problems with the form:</strong>
			<ul>
				{{#each errors}}
					<li>{{this}}</li>
				{{/each}}
			</ul>
		</div>
	</script>

	<script type="text/x-handlebars-template" id="tour-nav-wizard">
  <div>
    <div style="width:88%; margin-left:20px; float:left" class="">
      <ul class="nav tnav-wizard" role="tablist">
        <li id="accomli" role="presentation" class="tour-progress" data-position="1" data-target="#accommodations">
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
        <li role="presentation" class="tour-progress" data-position="10" data-target="#add-ons">
          <a id="addon-tab" href="javascript:void(0)" role="tab" data-toggle="tab">
            <span class="step-description">Add ons</span>
          </a>
        </li>
        <li role="presentation" class="tour-progress" data-position="9" data-target="#packages">
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

	<script src="/tabs/settings/js/script.js"></script>
</div>
