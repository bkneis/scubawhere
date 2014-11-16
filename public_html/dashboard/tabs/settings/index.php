<div id="wrapper">
	<div class="row">
		<div id="company-form-container">
			<script type="text/x-handlebars-template" id="company-form-template">
				<div class="box50">
				<label class="dgreyb">Account Information</label>
					<div class="padder">
						<form id="update-company-form">

						<div class="form-row">
							<label class="field-label">Username : </label>
							<input type="text" name="email" value="{{username}}"></input>
						</div>

						<div class="form-row">
							<label class="field-label">Change Password : </label>
							<button id="send-password" class="bttn blueb">Send password remainder email</button>
						</div>

						<div class="form-row">
							<label class="field-label">Main person of contact : </label>
							<input type="text" name="contact" value="{{contact}}"></input>
						</div>
						<div class="form-row">
							<label class="field-label">Contact phone number : </label>
							<input type="text" name="phone" value="{{phone}}"></input>
						</div>

						<div class="form-row">
							<label class="field-label">Contact email address : </label>
							<input type="text" name="email" value="{{email}}"></input>
						</div>

						<div class="form-row" style="overflow:auto;">
							<label>Accepted Diving Instuitions</label>
							<div id="agencies">
								<div id="save-loader" class="loader"></div>
							</div>
						</div>

						<div class="form-row">
							<label class="field-label">Dive operator name : </label>
							<input type="text" name="name" id="{{name}}"></input>
						</div>

						<div class="form-row">
							<label class="field-label">Dive operator website : </label>
							<input type="text" name="website" placeholder="http://" value="{{website}}"></input>
						</div>

						<div class="form-row">
							<label class="field-label">Dive center bio : </label>
							<textarea name="description" rows="3" value="{{description}}"></textarea>
						</div>
					</div>
				</div>

			<div class="box50">
			<label class="dgreyb">Business Information</label>
				<div class="padder">

					<div class="form-row">
						<label class="field-label">Business Address 1 : </label>
						<input type="text" name="address_1" value="{{address_1}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Business Address 2 : </label>
						<input type="text" name="address_2" value="{{address_2}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">City : </label>
						<input type="text" name="city" value="{{city}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">County / State : </label>
						<input type="text" name="county" value="{{county}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Post code / zip code : </label>
						<input type="text" name="postcode" value="{{postcode}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Business phone : </label>
						<input type="text" name="business_phone" value="{{business_phone}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Business email : </label>
						<input type="text" name="business_email" value="{{business_email}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Registration number : </label>
						<input type="text" name="registration_number" value="{{registration_number}}"></input>
					</div>

					<div class="form-row">
						<label class="field-label">VAT number : </label>
						<input type="text" name="vat_number" value="{{vat_number}}"></input>
					</div>

					<div class="form-row">
						<input type="submit" class="submit register-bttn bttn blueb" value="Update Account">
					</div>

					<input type="hidden" name="_token">
					
				</div>
			</form>
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

		</div>

	</div>
</div>

<script src="/dashboard/tabs/settings/js/script.js"></script>