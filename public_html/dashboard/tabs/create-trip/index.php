<script type='text/javascript'>

(function()
{
  if( window.localStorage )
  {
    if( !localStorage.getItem( 'firstLoad' ) )
    {
      localStorage[ 'firstLoad' ] = true;
      window.location.reload();
    }  
    else
      localStorage.removeItem( 'firstLoad' );
  }
})();

</script>
<div id="wrapper">
	<form id="trip-form">
		<div class="row">
			<div class="box50">
				<label class="blueb">Name & Duration</label>
				<div class="padder">
					<div class="form-row">
						<label class="field-label">Trip Name</label>
						<input type="text" name="name" placeholder="Trip Name" />
					</div>
				
				
					<div class="form-row">
						<label class="field-label">Trip Duration</label>
						<input type="text" data-tooltip="Please enter the total amount of hours for this trip." name="duration" placeholder="Duration in hours" />
						
						<div>

							<div id="duration-readable" class="bluef"></div>
							<button id="remove24" class="bttn small-bttn lgreyb">Minus 24</button>
							<button id="add24" class="bttn small-bttn lgreyb">Add 24</button>

						</div>
						
						
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
				<textarea name="description" id="description"></textarea>
			</div>
		</div>
		
		<!-- 		LOCATIONS		 -->
		
		<div class="row">
			<div class="box50">
				<label class="dgreyb">Dive Spots</label>
				<div class="padder">	
					<div class="yellow-helper">
						Click a marker to remove a location from the map.
					</div>				
					<div id="map"></div>					
				</div>
				<span id="hidden-spots"></span>			
			</div>
			
			<div class="box50">
				<label class="dgreyb">Select Your Dive Spots</label>
				<div class="padder">
					<div class="yellow-helper">
						Click a spot from this list to add it to your map. You can remove markers by clicking them on the map.
					</div>
					
					
					<script id="locations" type="text/x-handlebars-template">
							
						<li class="add-location" data-location="{{name}},{{id}},{{latitude}},{{longitude}}">
							
							<div>{{name}}</div>
							<div>Long: {{longitude}}</div>
							<div>Lat: {{latitude}}</div>
							
						</li>
						
					</script>
					
					<ul id="locations-list">
						
					</ul>
					
				</div>			
			</div>
		</div>
		
		<div class="row">
			<div class="box100">
				<label class="dgreyb">Selected Spots</label>
				
				<ul id="selected-spots">
					<script id="selected-spot" type="text/x-handlebars-template">
							
						<li class="spot box33 remove-spot" data-location="{{name}},{{id}},{{latitude}},{{longitude}}">
							<div>{{name}}</div>
							<div>Long: {{longitude}}</div>
							<div>Lat: {{latitude}}</div>
							<div class="link select-pickup">Set As Pick Up</div>
							<input type="hidden" name="locations[]" value="{{id}}" />
						</li>
						
					</script>
				</ul> 
				<div class="padder" id="selected-pickup"><div id="pu-error"></div>Pick up: <span>Please select a location.</span></div>
			</div>
		</div>
		
			
		<div class="row">
			<div class="box100">
				<label class="dgreyb">Trip Types</label>
				
					<div class="padder">
						<div id="type-error"></div>
						<ul id="trip-types">
							<script id="trip-type" type="text/x-handlebars-template">
								<div class="box33">
									<li>
										
										<input type="checkbox" name="triptypes[]" id="{{id}}" value="{{id}}"> <label for="{{id}}">{{name}}</label>
										
									</li>
								</div>
							</script>
						</ul>
					</div>
				
			</div>
		</div>

		<div class="row">
			<div class="box100">
				<label class="dgreyb">Tickets</label>
				
					<div class="padder">
						
						<!-- Add new tickets -->
						<form>
							<div class="form-row">
								<label class="field-label">Ticket Name</label>
								<input type="text" name="name">
							</div>

							<div class="form-row">
								<label class="field-label">Ticket Price</label>
								<input type="text" name="price">
							</div>

							<div class="form-row">
								<label>Ticket Description</label>
								<textarea name="description"></textarea>
							</div>

							<div class="form-row">
								<label class="field-label">Currency</label>
								<select name="currency">
									<option value="GBP">GBP</option>
								</select>
							</div>

							<div class="form-row">
								<label>Select one or more boats Boat (Optional)</label>
								<div>
									<div class="box33" id="">[Boats]</div>
									<div class="box33" id="">[Boats]</div>
									<div class="box33" id="">[Boats]</div>
								</div>
							</div>
						</form>
					</div>
				
			</div>
		</div>

		<div class="row">
			<div class="box100">
				<label class="dgreyb">Your Saved Tickets</label>
				
					<div class="padder">
						
						<!-- Display all newly added tickets -->
						
				
			</div>
		</div>
		<input type="hidden" name="_token" value=""/>
		<input type="submit" value="Create Trip" id="create-trip" class="bttn blueb" />
	</form>
</div>

	
<script src="tabs/create-trip/js/dive-locator.js"></script>
<script src="tabs/create-trip/js/script.js"></script>