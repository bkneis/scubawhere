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
							<div id="account-info" class="panel-body">

								<div class="form-row">
									<label class="field-label">Main Person of Contact : </label>
									<input type="text" name="contact" value="{{contact}}" class="form-control" required></input>
								</div>

								<div class="form-row">
									<label class="field-label">Dive Operator Name : </label>
									<input type="text" name="name" value="{{name}}" class="form-control" required>
								</div>

								<div class="form-row">
									<label id="business-website" class="field-label">Dive Operator Website : </label>
									<input type="text" name="website" placeholder="http://" value="{{website}}" class="form-control">
								</div>

								<div id="agencies-list" class="form-row" style="overflow:auto;">
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
									<label class="field-label">Contact Email Address : </label>
									<input type="text" name="email" value="{{email}}" class="form-control" required></input>
								</div>-->

								<!--<div class="form-row">
									<label class="field-label">Change Password : </label>
									<button id="send-password" class="btn btn-default btn-sm">Send password reminder email</button>
								</div>-->

								<div id="credit-info"></div>

								<div class="form-row">
									<label class="field-label">Customer facing web portal :</label>
									<input type="text" style="width: 30%;" name="alias" value="{{alias}}" {{#if alias}} disabled {{/if}}> .scubawhere.com
								</div>

								<!--<div id="start-tour-div" class="form-row">
									<label class="field-label">Restart the Tour : </label>
									<a id="start-wizard" class="btn btn-default text-uppercase">Start wizard</a>
								</div>-->

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
									<input type="text" name="address_1" value="{{address_1}}" class="form-control" required>
								</div>

								<div class="form-row">
									<label class="field-label">Business Address 2 : </label>
									<input type="text" name="address_2" value="{{address_2}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">City : </label>
									<input type="text" name="city" value="{{city}}" class="form-control" required>
								</div>

								<div class="form-row">
									<label class="field-label">County / State : </label>
									<input type="text" name="county" value="{{county}}" class="form-control" required>
								</div>

								<div id="postcode-div" class="form-row">
									<label class="field-label">Post Code / Zip Code : </label>
									<input type="text" name="postcode" value="{{postcode}}" class="form-control" required>
								</div>

								<div class="form-row">
									<label class="field-label">Business Phone : </label>
									<input class="form-control" type="text" id="business_phone" name="business_phone" value="{{business_phone}}">
								</div>

								<div class="form-row">
									<label class="field-label">Business Email : </label>
									<input type="text" name="business_email" value="{{business_email}}" class="form-control">
								</div>

								<div id="legal-info" class="form-row">
									<label class="field-label">Registration Number : </label>
									<input type="text" name="registration_number" value="{{registration_number}}" class="form-control">
								</div>

								<div class="form-row">
									<label class="field-label">VAT Number : </label>
									<input type="text" name="vat_number" value="{{vat_number}}" class="form-control">
								</div>

								<div class="form-row">
									<label for="country_id">Country : </label>
									<select id="country_id" name="country_id" style="width: 100%;" required>
										<option value="">Please select</option>
									</select>
								</div>

								<div class="form-row">
									<label for="currency_id">Currency : </label>
									<select id="currency_id" name="currency_id" style="width: 100%;" required>
										<option value="">Please select</option>
									</select>
								</div>

								<div class="form-row">
									<label class="field-label">Upload Terms and Conditions File : </label>
									<input id="terms-file" style="margin-bottom:10px" type="file" accept="application/pdf">
									
                                    <!--<input id="terms-file" name="terms_file" type="file" accept="application/pdf" />-->
									<a id="upload-terms" class="btn btn-default text-uppercase">Upload Terms</a>
								</div>

								<input type="hidden" name="_token">
								<input id="save-company-info" type="submit" class="update-settings btn btn-primary btn-lg pull-right" value="SAVE">
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

	<script type="text/x-handlebars-template" id="credit-info-template">
		<div class="form-row">
			<label class="field-label">Licence Expires on : </label>
			<input type="text" name="licence_renewal" value="{{trimDate renewal_date}}" class="form-control" disabled>
		</div>
		<div class="form-row">
			<label class="field-label">Booking credits used : {{bookings.used}} / {{bookings.total}}</label>
			<div style="width:100%" class="percentage-bar-container bg-success border-success">
				<div class="percentage-bar" style="background-color: #5cb85c; width: {{getUtil bookings.total bookings.used}}%;">&nbsp</div>
			</div>
		</div>
		<div class="form-row">
			<label class="field-label">Email credits used : {{emails.used}} / {{emails.total}}</label>
			<div style="width:100%; background-color: #ffe4b2;" class="percentage-bar-container border-success" >
				<div class="percentage-bar" style="background-color: #FFA500; width: {{getUtil emails.total emails.used}}%;">&nbsp</div>
			</div>
		</div>
		<div class="form-row">
			<label class="field-label">Running low on credits? Please click the button below to buy more</label>
			<a href="http://scubawhere.com/bolt-ons" target="_blank" style="color:white;" class="btn btn-primary">Click here</a>
		</div>
	</script>

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

    <link rel="stylesheet" type="text/css" href="/css/bootstrap-tour-standalone.min.css">
    <script type="text/javascript" src="/js/bootstrap-tour-standalone.min.js"></script>    
	<script type="text/javascript" src="/js/tour.js"></script>
	<!--<script src="/tabs/campaigns/email-templates/js/jquery.min.js"></script>-->
	<!--<script src="/tabs/campaigns/email-templates/js/jquery.ui.widget.js"></script>
	<script src="/tabs/campaigns/email-templates/js/jquery.iframe-transport.js"></script>
	<script src="/tabs/campaigns/email-templates/js/jquery.fileupload.js"></script>-->
	<script src="/tabs/settings/js/script.js"></script>
</div>
