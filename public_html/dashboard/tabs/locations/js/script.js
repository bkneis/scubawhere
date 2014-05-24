var map;
$(function(){
	map = new GMaps({
		div: '#map',
		lat: 0,
		lng: 0,
		width: '100%',
		height: '400px',
		zoom: 2
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

function addLocation(lng, lat){
	
}