<div id="wrapper">
	<div class="row">

		<div class="box50">
			<label class="dgreyb">Account Information</label>
			<div class="padder">
				<script type="text/x-handlebars-template" id="company-form-template">
				<form id="settings-form">

					<div class="form-row">
						<label class="field-label">Username : </label>
						<input type="text" name="email"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Password : </label>
						<input type="password" name="password"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Re-enter password : </label>
						<input type="password" name="re-passwd"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Main person of contact : </label>
						<input type="text" name="contact"></input>
					</div>
					<div class="form-row">
						<label class="field-label">Contact phone number : </label>
						<input type="text" name="phone"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Contact email address : </label>
						<input type="text" name="email"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Contact email address : </label>
						<input type="text" name="email"></input>
					</div>

					<div class="form-row" style="overflow:auto;">
						<label>Accepted Diving Instuitions</label>
						<div id="agencies">
							<div id="save-loader" class="loader"></div>
						</div>
					</div>

					<div class="form-row">
						<label class="field-label">Dive operator name : </label>
						<input type="text" name="name"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Dive operator website : </label>
						<input type="text" name="email" placeholder="http://"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Dive center bio : </label>
						<textarea name="description"></textarea>
					</div>

			</div>
		</div>

		<div class="box50">
			<label class="dgreyb">Business Information</label>
				<div class="padder">

					<div class="form-row">
						<label class="field-label">Business Address 1 : </label>
						<input type="text" name="address1"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Business Address 2 : </label>
						<input type="text" name="address2"></input>
					</div>

					<div class="form-row">
						<label class="field-label">City : </label>
						<input type="text" name="city"></input>
					</div>

					<div class="form-row">
						<label class="field-label">County / State : </label>
						<input type="text" name="county"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Post code / zip code : </label>
						<input type="text" name="postcode"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Business phone : </label>
						<input type="text" name="business_phone"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Business email : </label>
						<input type="text" name="business_email"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Registration number : </label>
						<input type="text" name="registration_number"></input>
					</div>

					<div class="form-row">
						<label class="field-label">VAT number : </label>
						<input type="text" name="vat_number"></input>
					</div>

					<div class="form-row">
						<label class="field-label">Country : </label>
						<select id="country_id"><option value=""></option></select>
					</div>

					<div class="form-row">
						<label class="field-label">Currency : </label>
						<select id="currency_id"><option value=""></option></select>
					</div>

					<div class="form-row">
						<button id="update-account" class="bttn big-bttn blueb">Update Account</button>
					</div>

					<input type="hidden" name="_token">
					
				</div>
			</form>
			</script>
		</div>

	</div>
</div>

<script src="/dashboard/tabs/settings/js/script.js"></script>