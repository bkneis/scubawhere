<div id="wrapper">
	<div class="box20" style="float:left">
            <label class="dgreyb">Search for a booking:</label>
            <div class="padder">
                <form id="find-booking-form">
    				<div class="form-row">
    					<label class="field-label">Booking Reference</label>
    					<input type="text" name="booking_reference">
    				</div>

                    <div class="form-row">
    					<label class="field-label">Date</label>
    					<input type="text" id="datepicker" name="date">
    				</div>

                    <div class="form-row">
                        <label class="field-label">Customer's Last Name</label>
                        <input type="text" name="last_name">
                    </div>

                    <div class="form-row">
    					<p><label class="">Trip Type</label></p>
    					<select id="type-product">
    						<option>Please select..</option>
                            <option>PADI course</option>
                            <option>Fun dive</option>
                            <option>More options will be added</option>
    					</select>
    				</div>

    				<input type="hidden" class="token" name="_token">
    				<input type="submit" class="bttn blueb" id="find-booking" value="Find Booking">
    				<button onclick="check()">Test</button>
    			</form>
    		</div>
    	</div>
    	<div class="box80" style="float:left">
	<table>
		<caption></caption>
		<thead>
			<tr>
				<th>Booking refrence</th>
				<th>Lead Customer</th>
				<th>Email</th>
				<th>Phone number</th>
				<th>Tickets</th>
				<th>Packages</th>
				<th>Booking total</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		
	</div>
</div>

<script src="tabs/manage-booking/js/main.js"></script>
<script src="/dashboard/js/Controllers/Booking.js"></script>
