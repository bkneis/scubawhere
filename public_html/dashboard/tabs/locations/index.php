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
				<img src="http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1" style="height: 1.5em; margin-bottom: -0.4em;" />
				Your locations
				<img src="http://mt.googleapis.com/vt/icon?color=ff004C13&name=icons/spotlight/spotlight-waypoint-blue.png&scale=1" style="height: 1.5em; margin-bottom: -0.4em; margin-left: 2em;" />
				Available locations
			</label>
		</div>

		<div class="box50">
			<div class="yellow-helper" style="margin-bottom: 0;">Click on the map to create a new location</div>
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

	<div class="box100" id="map-container">
		<div id="map" style="height: 100%;"></div>
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
</div>

<script>
function loadGoogleMaps() {
	console.log('Start loading');
	if( window.google && google.maps ) {
		console.log('Google Maps already loaded');
		initialise();

		return true;
	}

	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=initialise';

	document.body.appendChild(script);

	console.log('Google Maps script appended');
}
</script>

<script src="/common/js/jquery.tagsinput.min.js"></script>

<script src="/common/js/jquery.reveal.js"></script>

<script src="/dashboard/js/Controllers/Location.js"></script>
<script src="tabs/locations/js/script.js"></script>

