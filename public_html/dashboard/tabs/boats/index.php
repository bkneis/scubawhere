<div id="wrapper">
	<div class="yellow-helper">Please enter all your boat details. You can divide a boats capacity up by room types. If you don't dive from boats then you could add a boat named "Beach" or "Pontoon" as appropriate.</div>
	<form id="saveBoatsAndRooms" class="validate">
		<div id="boats-wrap" class="box100">
			<label class="blueb expand-box">Existing Boats & Rooms <span class="expand-box-arrow">&#8595;</span></label>
			<div class="padder expandable hidden">
				<!--list the boats with handlebars -->
				<script id="boat" type="text/x-handlebars-template">
					
						<div class="boat-wrap box100" data-boat-id="{{id}}">
							<h3 class="expand-box">{{newBoat}}{{name}} <span class="expand-box-arrow">&#8595;</span></h3>
							<div class="padder expandable hidden">
								<p>{{description}}</p>
								<span>Capacity: {{capacity}}</span>
								
								<input type="hidden" name="boats[{{id}}][name]" value="{{name}}">
								<input type="hidden" name="boats[{{id}}][capacity]" value="{{capacity}}">
								<input type="hidden" name="boats[{{id}}][description]" value="{{description}}">
								
								<ul>
									{{#accommodations}}
										<li>
											<span>{{name}}</span>
											<span>Capacity: {{pivot.capacity}}</span>
											<input type="hidden" name="boats[{{pivot.boat_id}}][accommodations][{{id}}]" value="{{pivot.capacity}}">
										</li>
									{{/accommodations}}
									
									<li class="boat-room-row">
										<span class="newBoatRoom">
											<select name="newBoatRoomName" class="newRoomTypeSelect">
												<option value="">Add new room type..</option>
											</select>
											<div>
												<input class="valid" name="newBoatRoomCapacity" type="text" placeholder="No. beds.">
												<input type="submit" id="saveBoatRoom" data-boat-id="{{id}}" value="Save" class="bttn small-bttn blueb">
											</div>
										</span>
									</li>
								</ul>
							</div>
						</div>
				</script>
				
				<div class="box100">
					<label class="blueb">Room Types</label>
					<table id="rooms-table">
						<thead>
							<tr>
								<th>Name</th>
								<th>Description</th>
								<th></th>
							</tr>
							
						</thead>
						
									
						<tbody id="accom-body">
							<!--list the room types with handlebars -->
							<script id="rooms" type="text/x-handlebars-template">
								<tr>
									<td class="colc">{{name}}</td>
									<td class="colc">{{description}}</td>
									<td class="colc">
										<input type="hidden" name="accommodations[{{id}}][name]" value="{{name}}">
										<input type="hidden" name="accommodations[{{id}}][description]" value="{{description}}">
									</td>
								</tr>
							</script>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="newBoat-wrap" class="box100">
			<label class="blueb">Add New Boat</label>
			<div class="padder">
				<input type="text" class="valid" name="newBoatName" placeholder="Boat name" />
				<input type="text" class="valid" name="newBoatCapacity" placeholder="Capacity" />
				<textarea name="newBoatDescription" class="valid" placeholder="Boat description"></textarea>
				<input type="submit" class="bttn blueb" value="Save" id="saveBoat" />
			</div>
		</div>
		
		
		
		<div class="box100">
			<label class="blueb">Add New Room Type</label>
			<div class="padder" id="new-room-type">
				<input name="newRoomName" type="text" placeholder="Room name">
				<textarea name="newRoomDescription" placeholder="Room description"></textarea>
				<input type="submit" class="bttn blueb" value="Save" id="saveRoom">
			</div>
		</div>
		
		<input type="hidden" name="_token" value="" />
		<input type="submit" id="saveAll" class="bttn blueb validate" value="Save All">
	</form>
	<script src="tabs/boats/js/boats.js"></script>	
</div>



