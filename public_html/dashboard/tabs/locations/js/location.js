var map;
$(document).ready(function(){
  var map = new GMaps({
    el: '#map',
    lat: 0,
    lng: 0,
    width: '100%',
    height: '400px',
    zoom: 2
  });

  GMaps.geolocate({
    success: function(position){
      map.setCenter(position.coords.latitude, position.coords.longitude);
    },
    error: function(error){
      alert('Geolocation failed: '+error.message);
    },
    not_supported: function(){
      alert("Your browser does not support geolocation");
    },
    always: function(){
      alert("Done!");
    }
  });

  GMaps.on('click', map.map, function(event) {
    //var index = map.markers.length;
    var lat = event.latLng.lat();
    var lng = event.latLng.lng();

    $("[name='latitude']").val(lat);
    $("[name='longitude']").val(lng);

    map.removeMarkers();

    map.addMarker({
      lat: lat,
      lng: lng,
      //icon: "/img/marker.png",
      title: 'dive_point',
      infoWindow: {
        content : 'Adjustment of the coordinates'
      }
    });
  });
});
