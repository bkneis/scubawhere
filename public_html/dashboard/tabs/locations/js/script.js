var map;

$(function(){

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

	_token = $.ajax({
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

	$.ajax({
	url: "/company/locations",
	type: "GET",
	dataType: "json",
	data: {limit: 99999, latitude: 0, longitude: 0},
	async: false,
	success: function(data){
			$.each(data, function(){
				/* $("#locations").append(locTemplate(this)); */
				map.addMarker({
					  lat: this.latitude,
					  lng: this.longitude,
					  title: this.name,
					  icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
				  });

			});
		}
	});

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
	      }
	    });
	  });

	  $("form#save-location").submit(function(e){
	  		$.ajax({
			url: "/company/add-location",
			type: "POST",
			data: $("form#save-location").serialize(),
			dataType: "json",
			async: false,
			success: function(data){
				pageMssg("Location saved", true);
				}
			});
	  		e.preventDefault();
	  });

});
