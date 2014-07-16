<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<div id="wrapper">
    <div class="row">
    	<div class="box30">
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
    					<p><label class="">Type of Product</label></p>
    					<select id="type-product">
    						<option>Please select..</option>
                            <option>PADI course</option>
                            <option>Fun dive</option>
                            <option>More options will be added</option>
    					</select>
    				</div>

    				<input type="hidden" class="token" name="_token">
    				<input type="submit" class="bttn blueb" id="find-booking" value="Find Booking">
    			</form>
    		</div>
    	</div>

    	<div class="box70">
    		<label class="dgreyb">Bookings</label>

    		<div class="padder">

    			<table>
                	<tr>
                		<th scope="col">Booking Refrence</th>
                    	<th scope="col">Customer Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Contact Number</th>
                        <th scope="col">Source of booking</th>
                        <th scope="col">Country of origin</th>
                        <th scope="col">More info</th>
                    </tr>
                	<tr>
                    	<td>BK230501A</td>
                        <td>Bryan Kneis</td>
                        <td>bryan@iqwebcreations.com</td>
                        <td>0766565047</td>
                        <td>Scuba where</td>
                        <td>England</td>
                        <td><a href="#">More info..</a></td>
                    </tr>
                    <tr>
                    	<td>BK230501A</td>
                        <td>Thomas Paris</td>
                        <td>tom@scubawhere.com</td>
                        <td>0745455047</td>
                        <td>In person</td>
                        <td>England</td>
                        <td><a href="#">More info..</a></td>
                    </tr>
                    <tr>
                    	<td>BK230501A</td>
                        <td>Soren Schwert</td>
                        <td>soren@scubawhere.com</td>
                        <td>0766917247</td>
                        <td>Scuba where</td>
                        <td>England</td>
                        <td><a href="#">More info..</a></td>
                    </tr>
                </table>
    		</div>
        </div>
	</div>
</div>
<script src="tabs/find-booking/js/script.js"></script>
