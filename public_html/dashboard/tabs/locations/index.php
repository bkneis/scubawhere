<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
  <script type="text/javascript" src="tabs/locations/js/gmaps.js"></script>
  <link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.3.0/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="tabs/locations/location.css" />
  <script type="text/javascript" src="tabs/locations/js/location.js"></script>
<div id="wrapper">
	<div class="box100">
		<div id="map"></div>
	</div>

	<div class="yellow-helper">
		Please select a point on the map to set "Longitude" and "Latitude". Click on the point to adjust co-ordinates
	</div>

	<div class="box100">
		<label class="dgreyb">New Location</label>
		<div class="padder">
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
		</div>
	</div>
</div>

<script type="text/html" id="edit_marker_template">
    <h4>Edit Marker</h4>
    <form class="edit_marker" action="#" method="post" data-marker-index="{{index}}">
      <p>
        <label for="marker_{{index}}_lat">Latitude:</label>
        <input type="text" id="marker_{{index}}_lat" value="{{lat}}" />
      </p>
      <p>
        <label for="marker_{{index}}_lng">Longitude:</label>
        <input type="text" id="marker_{{index}}_lng" value="{{lng}}" />
      </p>
      <input type="submit" value="Update position" />
    </form>
  </script>
