<div id="wrapper" class="clearfix">
	<div id="company-form-container">
		<script type="text/x-handlebars-template" id="company-form-template">
			<form id="update-company-form">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">Account Information</h4>
						</div>
						<div class="panel-body">

							<div class="form-row">
								<label class="field-label">Username : </label>
								<input type="text" name="username" value="{{username}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">Change Password : </label>
								<button id="send-password" class="btn btn-default btn-sm">Send password reminder email</button>
							</div>

							<div class="form-row">
								<label class="field-label">Main person of contact : </label>
								<input type="text" name="contact" value="{{contact}}" class="form-control"></input>
							</div>
							<div class="form-row">
								<label class="field-label">Contact phone number : </label>
								<input class="form-control" type="text" id="phone" name="phone" value="{{phone}}">
							</div>

							<div class="form-row">
								<label class="field-label">Contact email address : </label>
								<input type="text" name="email" value="{{email}}" class="form-control"></input>
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

							<div class="form-row">
								<label class="field-label">Dive operator name : </label>
								<input type="text" name="name" value="{{name}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">Dive operator website : </label>
								<input type="text" name="website" placeholder="http://" value="{{website}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">Dive center bio : </label>
								<textarea name="description" rows="3" value="{{description}}"></textarea>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">Business Information</h4>
						</div>
						<div class="panel-body">

							<div class="form-row">
								<label class="field-label">Business Address 1 : </label>
								<input type="text" name="address_1" value="{{address_1}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">Business Address 2 : </label>
								<input type="text" name="address_2" value="{{address_2}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">City : </label>
								<input type="text" name="city" value="{{city}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">County / State : </label>
								<input type="text" name="county" value="{{county}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">Post code / zip code : </label>
								<input type="text" name="postcode" value="{{postcode}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">Business phone : </label>
								<input class="form-control" type="text" id="business_phone" name="business_phone" value="{{business_phone}}">
							</div>

							<div class="form-row">
								<label class="field-label">Business email : </label>
								<input type="text" name="business_email" value="{{business_email}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">Registration number : </label>
								<input type="text" name="registration_number" value="{{registration_number}}" class="form-control"></input>
							</div>

							<div class="form-row">
								<label class="field-label">VAT number : </label>
								<input type="text" name="vat_number" value="{{vat_number}}" class="form-control"></input>
							</div>

							<input type="hidden" name="_token">
							<input type="submit" class="update-settings btn btn-primary btn-lg pull-right" value="SAVE">
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">Terms and conditions</h4>
						</div>
						<div class="panel-body">
							<div class="form-row">
								<textarea style="width:100%" rows="10" id="terms" name="terms">{{terms}}</textarea>
							</div>

							<input type="submit" class="update-settings btn btn-primary btn-lg pull-right" value="SAVE">
						</div>
					</div>
				</div>
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

	<script src="/js/Controllers/Company.js"></script>
	<script src="/tabs/settings/js/script.js"></script>
</div>
