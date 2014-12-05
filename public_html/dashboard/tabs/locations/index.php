<!-- <script type="text/x-handlebars-template" id="edit_marker_template">
	<h4>Edit Marker</h4>
	<form class="edit_marker" action="#" method="post" data-marker-index="{{index}}">
		<p>
			<label for="marker_lat">Latitude:</label>
			<input type="text" id="marker_lat" value="{{lat}}" />
		</p>
		<p>
			<label for="marker_lng">Longitude:</label>
			<input type="text" id="marker_lng" value="{{lng}}" />
		</p>
		<input type="submit" value="Update position" />
	</form>
</script> -->
<div id="wrapper">

	<div class="row">
		<div class="box50">
			<label class="dgreyb">
				<img src="http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1" style="height: 1.5em; margin-left: 0.5em; display: none;" id="legend-your-locations-icon" /><span class="loader" id="legend-your-locations-loader" style="margin-bottom: 0.3em;"></span>
				Your locations
				<img src="http://mt.googleapis.com/vt/icon?color=ff004C13&name=icons/spotlight/spotlight-waypoint-blue.png&scale=1" style="height: 1.5em; margin-left: 1em; display: none;" id="legend-available-locations-icon" /><span class="loader" id="legend-available-locations-loader" style="margin-bottom: 0.3em; margin-left: 2em;"></span>
				Available locations
				<img src="http://mt.googleapis.com/vt/icon?psize=30&font=fonts/arialuni_t.ttf&color=ff304C13&name=icons/spotlight/spotlight-waypoint-a.png&ax=43&ay=48&text=%E2%80%A2&scale=1" style="height: 1.5em; margin-left: 1em;" />
				New location
			</label>
		</div>

		<div class="box50">
			<!-- <div class="yellow-helper" style="margin-bottom: 0;">Click the map or an existing marker.</div> -->
			<div class="dgreyf" style="font-size: 17px; padding-top: 2px; text-align: right;">
				Lat: <input type="number" placeholder="Latitude" step="0.1" min="-90" max="90" id="newMarkerLatitude" style="width: 115px;" />
				Long: <input type="number" placeholder="Longitude" step="0.1" min="-180" max="180" id="newMarkerLongitude" style="width: 115px" />
				<button class="btn btn-default" style="margin-right: 5px;" id="showLocation">Show</button>
				<button class="btn btn-primary" style="margin-right: 10px;" id="createLocation">Create</button>
			</div>
			<!-- <div class="padder">
				<form id="save-location">
					<div>
						<input type="text" name="name" placeholder="Location Name" />
					</div>
					<div>
						<textarea name="description" placeholder="Description"></textarea>
					</div>
					<div>
						<input type="text" placeholder="Longitude" name="longitude" readonly>
						<input type="text" placeholder="Latitude" name="latitude" readonly>
					</div>
					<div>
						<input type="text" name="tags" id="tags" placeholder="Tags"></textarea>
					</div>

					<input type="hidden" name="_token">
					<div>
						<input type="submit" class="bttn blueb" value="Save Location" />
					</div>
				</form>
			</div> -->
		</div>
	</div>

	<div class="row">
		<div class="box100" id="map-container">
			<div id="map" style="height: 100%;"></div>
		</div>
	</div>

	<!-- <div class="yellow-helper">
		Please select a point on the map to set "Longitude" and "Latitude". Click on the marker to edit the Longitude or Latitude.
	</div> -->

	<!-- <div class="box100">
		<label class="dgreyb">Manage Locations</label>

		<table>
		<caption></caption>
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Longitude</th>
				<th>Latitude</th>
				<th> </th>
			</tr>
		</thead>

		<tbody id="locations">
			<script id="location-list-template" type="text/x-handlebars-template">

					<tr>
						<td>{{name}}</td>
						<td>{{{description}}}</td>
						<td>{{longitude}}</td>
						<td>{{latitude}}</td>
						<td><a onclick="detachLocation({{id}})">Remove</a></td>
					</tr>

			</script>
		</tbody>
	</table>
	</div>-->

	<div id="modalWindows" style="height: 0;">
		<script id="location-template" type="text/x-handlebars-template">
			<div id="modal-{{id}}" class="reveal-modal">
				<p style="text-transform: uppercase; float: right; line-height: 2.8em;">
					{{#if attached}}
						Your location
					{{else}}
						Available location
					{{/if}}
				</p>
				<h3>{{{name}}}</h3>
				<span>{{latitude}}, {{longitude}}</span>
				<h4>Description</h4>
				<p>{{{description}}}</p>
				<h4>Tags</h4>
				<div class="clearfix">
					{{#each tags}}
						<div class="tag"><strong>{{name}}</strong></div>
					{{else}}
						<h5 class="text-center text-muted" style="margin-bottom: 2em;">
							<img src="/common/img/lightsaber.png" style="margin-top: -1em; margin-left: -2em;">
							These aren't the tags you are looking for&#8230; <!-- &#8230; is an ellipsis (...) -->
						</h5>
					{{/each}}
				</div>

				<form>
					{{#if attached}}
						<input type="submit" value="Remove from your locations" class="detach-location btn btn-danger" />
					{{else}}
						<input type="submit" value="Add to your locations" class="attach-location btn btn-primary btn-lg" />
					{{/if}}
				</form>

				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>

		<script id="new-location-template" type="text/x-handlebars-template">
			<div id="modal-new" class="reveal-modal">
				<h4 class="margin-top: 0;">New location</h4>
				<form id="create-location-form">
					<div class="form-row">
						<label>Name</label>
						<input type="text" name="name" id="new-location-name" />
					</div>
					<div class="form-row">
						<label class="field-label">Description</label>
						<textarea name="description" id="description"></textarea>
					</div>
					<div class="form-row">
						<div class="field-label">Tags</div>
						<div class="clearfix">
							{{#each available_tags}}
								<label class="tag">
									<input type="checkbox" name="tags[]" value="{{id}}" onchange="changeParent(this)">
									<strong>{{name}}</strong>
								</label>
							{{/each}}
						</div>
					</div>

					<input type="hidden" name="latitude" value="{{latitude}}" />
					<input type="hidden" name="longitude" value="{{longitude}}" />

					<div style="margin-top: 1em; text-align: right">
						<a class="close-modal" title="Abort" style="margin-right: 2em;">Cancel</a>
						<input type="submit" value="Create" class="add-location btn btn-primary btn-lg" />
					</div>
				</form>

				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>
	</div>

	<script>
		function loadGoogleMaps() {
			console.log('Start loading');

			if( window.google && google.maps ) {
				console.log('Google Maps already loaded');
				initialise();

				return true;
			}

			var key = '';
			if(window.location.host === 'scubawhere.com') key = '&key=AIzaSyAzTfKvssUjEK4Reyg3RD7lBAT6vGZG4hk';

			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=initialise' + key;

			document.body.appendChild(script);

			console.log('Google Maps script appended');
		}
	</script>

	<script src="/common/js/jquery/jquery.reveal.js"></script>
	<script src="/common/js/jquery/jquery.serialize-object.min.js"></script>
	<script src="/common/js/rAF.js"></script>

	<script src="/dashboard/js/Controllers/Location.js"></script>
	<script src="tabs/locations/js/script.js"></script>
</div>
