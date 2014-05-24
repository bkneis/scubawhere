<?php 
	/*
$root = $_SERVER['DOCUMENT_ROOT']; 
	require_once($root."/engine/core/db/interface/places.php");
*/
?>

<div id="wrapper">
	<form>
		<div class="row">
			<div class="box50">
				<label class="blueb">Name & Duration</label>
				<div class="padder">
					<div class="form-row">
						<label class="field-label">Trip Name</label>
						<input type="text" name="tripName" placeholder="Boat Name" />
					</div>
				
				
					<div class="form-row">
						<label class="field-label">Trip Duration</label>
						<input type="text" data-tooltip="this is my tooltip Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam." name="tripDuration" placeholder="Duration in days" />
					</div>
				</div>
			</div>
			
			<div class="box50">
				<label class="redb">!</label>
				<div class="padder">
					In order to create a trip you must first add at least one boat - if you don't run trips from boats, simply log a beach or pontoon. Tickets can also be created in the ticket tab, however you can create them here as well.
				</div>
			</div>
		</div><!-- row -->
		
		<div class="box100">
			<label class="blueb">Description</label>
			<div class="padder">
				<textarea id="description"></textarea>
			</div>
		</div>
		
		<!-- 		LOCATIONS		 -->
		<?php /* include($root."/engine/core/maps/dive_locator.php"); */ ?>
		
		<div class="box100">
			<label class="blueb">Tickets</label>
			<table>
				<caption></caption>
				<thead>
					<tr>
						<th class="col30">Duration</th>
						<th class="col50">Name</th>
						<th class="col10">Cap.</th>
						<th class="col10"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="col30c">2 days</td>
						<td class="col50">This is a random ticket name.</td>
						<td class="col10c">30</td>
						<td class="col10c">
							<div class="check-wrap">
								<input type="checkbox" value="1" id="one" name="name" />
								<label for="one"></label>
							</div>
						</td>
					</tr>
					
					<tr>
						<td class="col30c">2 days</td>
						<td class="col50">This is a random ticket name.</td>
						<td class="col10c">30</td>
						<td class="col10c">
							<div class="check-wrap">
								<input type="checkbox" value="1" id="two" name="name" />
								<label for="two"></label>
							</div>
						</td>
					</tr>
					
					<tr>
						<td class="col30c">2 days</td>
						<td class="col50">This is a random ticket name.</td>
						<td class="col10c">30</td>
						<td class="col10c">
							<div class="check-wrap">
								<input type="checkbox" value="1" id="three" name="name" />
								<label for="three"></label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
		</div>
	</form>
</div>

<script src="tabs/create-trip/js/script.js"></script>