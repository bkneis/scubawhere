var map;
$(document).ready(function(){
  var map = new GMaps({
    el: '#map',
    lat: 0,
    lng: 0,
    width: '100%',
    height: '400px',
    zoom: 6
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
      //alert("Done!");
    }
  });

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

});