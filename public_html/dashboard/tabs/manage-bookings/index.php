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
                    <!--<a href="#" class="button big icon edit">Find Booking</a>-->
    			</form>
    		</div>
    	</div>
    	<div class="box80" style="float:left">
	<table style="text-align:center">
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
                <th>Comfirmed</th>
				<th></th>
				<th></th>
			</tr>
		</thead>

        <tbody id="bookings">
            <script id="booking-list-template" type="text/x-handlebars-template">
                {{#each booking}}
                    <tr>
                        <td data-id='{{id}}'>{{reference}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>$ {{price}}</td>
                        <td class="confirm">{{confirmed}}</td>
                        <td><a href="#" class="button icon search">View</a></td>
                        <td><a href="#" class="button icon edit">Edit</a></td>
                    </tr>
                {{/each}}
            </script>
        </tbody>
		
	</div>
</div>

<script src="tabs/manage-bookings/js/main.js"></script>
<script src="/dashboard/js/Controllers/Booking.js"></script>

<link rel="stylesheet" href="tabs/manage-bookings/css/gh-buttons.css">
