<div id="wrapper">
		<div class="box100">
		<label class="dgreyb">Add booking</label>

		<div class="padder">

			<form id="add-booking-form">
				<div class="form-row">
					<label class="field-label">Name</label>
					<input type="text" name="booking_name">
				</div>

                <div class="form-row">
					<label class="field-label">Email</label>
					<input type="text" name="booking_email">
				</div>

                <div class="form-row">
					<label class="field-label">Contact number</label>
					<input type="text" name="contact_number">
				</div>

                <div class="form-row">
					<label class="field-label">Address</label>
					<input type="text" name="booking_address">
				</div>

                <div class="form-row">
					<label class="field-label">Country of origin</label>
					<input type="text" name="branch_email">
				</div>

				<div class="form-row">
					<label class="">Trip Type</label>
					<div class="box50" style="padding-left:4.5cm;">

						<select id="sob-select">
							<option>Please select..</option>
                            <option>PADI course</option>
                            <option>Fun dive</option>
                            <option>Night dive</option>
                            <option>Training</option>
						</select>
					</div>
				</div>

                                <div class="form-row">
					<label class="">Source of booking</label>
					<div class="box50" style="padding-left:4.5cm;">

						<select id="sob-select">
							<option>Please select..</option>
                            <option>Scuba Where</option>
                            <option>Website</option>
                            <option>Agent</option>
                            <option>In Person</option>
						</select>
					</div>
				</div>

                <div class="form-row">
					<label class="field-label">Additional customer names</label>
					<input type="text" name="customer_name">
				</div>

                <div class="form-row">
					<label class="field-label">Additional email addresses</label>
					<input type="text" name="customer_email">
				</div>

                <div class="form-row">
					<label class="field-label">Booking Comments</label>
					<textarea name="booking_comments"></textarea>
				</div>

				<input type="hidden" class="token" name="_token">
				<input type="submit" class="bttn blueb" id="add-booking" value="Add Booking">

			</form>
		</div>

	</div>
</div>
<script src="tabs/add-booking/js/script.js"></script>
