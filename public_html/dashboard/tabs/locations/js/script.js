var map;

if(!window.token) {
	$.get("/token", null, function(data) {
		window.token = data;
	});
}

$(function() {

	// Update position
	$(document).on('submit', '.edit_marker', function(e) {
		e.preventDefault();

		var $index = $(this).data('marker-index');

		$lat = $('#marker_' + $index + '_lat').val();
		$lng = $('#marker_' + $index + '_lng').val();

		var template = $('#edit_marker_template').text();

		// Update form values
		var content = template.replace(/{{index}}/g, $index).replace(/{{lat}}/g, $lat).replace(/{{lng}}/g, $lng);

		map.markers[$index].setPosition(new google.maps.LatLng($lat, $lng));
		map.markers[$index].infoWindow.setContent(content);

		$marker = $('#markers-with-coordinates').find('li').eq(0).find('a');
		$marker.data('marker-lat', $lat);
		$marker.data('marker-lng', $lng);
	});

	// Update center
	$(document).on('click', '.pan-to-marker', function(e) {
		e.preventDefault();

		var lat, lng;

		var $index = $(this).data('marker-index');
		var $lat = $(this).data('marker-lat');
		var $lng = $(this).data('marker-lng');

		if ($index != undefined) {
			// using indices
			var position = map.markers[$index].getPosition();
			lat = position.lat();
			lng = position.lng();
		}
		else {
			// using coordinates
			lat = $lat;
			lng = $lng;
		}

		map.setCenter(lat, lng);
	});

	map = new GMaps({
		div: '#map',
		lat: 0,
		lng: 0,
		width: '100%',
		height: '400px',
		zoom: 8
	});

	google.maps.event.addListener(map.map, 'bounds_changed', function() {
		// Trigger the map resize after the map has loaded, to fix the map not showing properly
		// console.log("Resize now!");
		google.maps.event.trigger(map.map, 'resize');
	});

	GMaps.geolocate({
		success: function(position){
			map.setCenter(position.coords.latitude, position.coords.longitude);
		}
		//,error: function(error){
		//alert('Geolocation failed: '+error.message);
		//},
		//not_supported: function(){
		// alert("Your browser does not support geolocation");
		//},
		//always: function(){
		//alert("Done!");
		//}
	});

	$('#tags').tagsInput({width:'502px', height: "60px"});

	_token = window.token || $.ajax({
		url: "/token",
		type: "GET",
		dataType: "html",
		async: false,
		success: function(data){
			$("[name='_token']").val(data)
		}
	});

	/*
	var locSource = $("#location").html();
	var locTemplate = Handlebars.compile(locSource);
	*/

	var locationSource = $("#location-list-template").html();
	var locationTemplate = Handlebars.compile(locationSource);

	Place.getLocationsAround({latitude: 0, longitude: 0, limit: 9999}, function success(data) {
		$.each(data, function(){
			//$("#locations").append(locTemplate(this));
			map.addMarker({
				lat: this.latitude,
				lng: this.longitude,
				title: this.name,
				color: "blue",
				icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
			});
		});

		Place.getAttachedLocations(function success(data) {
			$.each(data, function(){
				 //$("#locations").append(locTemplate(this));
				map.addMarker({
					lat: this.latitude,
					lng: this.longitude,
					title: this.name,
					color: "red",
					icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
				});

				$("#locations").append(locationTemplate(this));
			});
		});
	});

	/*Locations.getAttachedLocations(function success(data){
		$.each(data, function(){
				/* $("#locations").append(locTemplate(this));
				map.addMarker({
					  lat: this.latitude,
					  lng: this.longitude,
					  title: this.name,
					  icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
				  });

				$("#locations").append(locationTemplate(this));
	});
	});*/

	var newMarker;

	/* 	ADD MARKER */
	GMaps.on('click', map.map, function(event) {
		var index = map.markers.length;
		var lat = event.latLng.lat();
		var lng = event.latLng.lng();

		$("[name='latitude']").val(lat);
		$("[name='longitude']").val(lng);

		//map.removeMarker('Marker #' + (index - 1));
		map.removeMarkers();

		var template = $('#edit_marker_template').text();

		var content = template.replace(/{{index}}/g, index).replace(/{{lat}}/g, lat).replace(/{{lng}}/g, lng);

		map.addMarker({
			lat: lat,
			lng: lng,
			title: 'Marker #' + index,
			infoWindow: {
				content : content
			},
			icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
		});
	});

	$("form#save-location").submit(function(event) {
		event.preventDefault();

		Place.create( $("form#save-location").serialize(), function success(data){
			pageMssg(data.status, true);
			console.log(data);
		});
	});
});

function detachLocation(id) {

	var params = {
		_token : window.token,
		id : id
	};

	console.log(id);

	Place.detach(params, function success(data) {
		pageMssg(data.status, true);
	});

	// e.preventDefault();
}
