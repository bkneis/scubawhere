<style>
.location-select {
	cursor: pointer;
}
.location-name {
	font-size: 12pt;
	cursor: pointer;
}
.location-details:hover {
	background-color: rgb(230, 245, 255);;
}
.add-location {
	list-style-type: none;
}
</style>
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
				<label class="dgreyb">Select Your Dive Spots</label>
				<div class="padder">
					<div class="yellow-helper">
						Click a spot from this list to add it to your map. You can remove markers by clicking them on the map.
					</div>

					<script id="locations" type="text/x-handlebars-template">

						<li class="add-location" data-location="{{name}},{{id}},{{latitude}},{{longitude}}">

							<div class="location-details">
							<div class="location-name"><p>{{name}}</p></div>
							<div>Long: {{longitude}}</div>
							<div>Lat: {{latitude}}</div>
							</div>

						</li>

					</script>

					<ul id="locations-list">

					</ul>

				</div>
			</div>

			<div class="box50">
				<label class="dgreyb">Selected Spots</label>

				<ul id="selected-spots">
					<script id="selected-spot" type="text/x-handlebars-template">

						<li id='{{name}}' class="spot box33 remove-spot" data-location="{{name}},{{id}},{{latitude}},{{longitude}}">
							<div class="location-select">{{name}}</div>
							<div class="location-select">Long: {{longitude}}</div>
							<div class="location-select">Lat: {{latitude}}</div>
							<!--<div class="link remove-location">Remove location from trip</div>-->
							<a style="cursor:pointer"class="remove-location" data-location="{{name}},{{id}},{{latitude}},{{longitude}}">Remove location</a>
							<input type="hidden" name="locations[]" value="{{id}}" />
						</li>

					</script>
				</ul>
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

		<input type="hidden" name="_token" value=""/>
		<input type="submit" value="Create Trip" id="create-trip" class="bttn blueb" />
	</form>
</div>


<script src="tabs/create-trip/js/dive-locator.js"></script>
<script src="tabs/create-trip/js/script.js"></script>
