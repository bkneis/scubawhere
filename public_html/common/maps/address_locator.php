<?php
        $root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/places.php");
        
        
        
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.1&sensor=false&callback=initialize"></script>

<script>

    
$(document).ready(function(){
    $("#region").attr("disabled", "disabled");
    $("#country").change(function(){
       var id = $("#country option:selected").attr('value');
       $.post("/engine/core/db/ajax/get_region_options_by_country.php", {countryID : id}, function(data){
           if (data) {
               $("#region").html(data);
               if($('#region option').size() > 1){
                   $("#region").removeAttr("disabled");
               } else {
                   $("#region").attr("disabled", "disabled")
               }               
            } else {
                $("#region").attr("disabled", "disabled");
            }
        
       });
    });
    
    
    $(".map-text").focusout(function() {
        codeAddress();
    });
    
    $(".map-select").focusout(function() {
        codeAddress();
    });
    
});
    
    
    var geocoder;
    var map;
    var marker;
    
    function initialize() {
      geocoder = new google.maps.Geocoder();
      var latlng = new google.maps.LatLng(55.378051, -3.435972999999994);
      var mapOptions = {
        zoom: 3,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
      
    }

    function codeAddress() {
           
       var country = document.getElementById('country').options[document.getElementById('country').selectedIndex].text;
       var region = document.getElementById('region').options[document.getElementById('region').selectedIndex].text;
       var addr1 = document.getElementById('addr1').value;
       var addr2 = document.getElementById('addr2').value;
       var city = document.getElementById('city').value;
       var county = document.getElementById('county').value;
       var postcode = document.getElementById('postcode').value;
       
        
       var address = (addr1 != "" ? addr1 + " " : "")
              + (addr2 != "" ? addr2 + " " : "")
              + (city != "" ? city + " " : "")
              + (county != "" ? county + " " : "")
              + (postcode != "" ? postcode + " " : "")
              + (region != "" && region != "Unknown" ? region + " " : "")
              + (country != "" && country != "Unknown" ? country : "");
      
        var zoom = 3;
        if (addr1 != "") {zoom+=2;}
        if (addr2 != "") {zoom+=2;}
        if (city != "") {zoom+=2;}
        if (county != "") {zoom+=1;}
        if (postcode != "") {zoom+=3;}
        if (region != "" && region != "Unknown") {zoom+=1;}
        if (country != "" && country != "Unknown") {zoom+=1;}
      
        geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          
            map.panTo(results[0].geometry.location);
          
          //Create a new marker if required
          if (marker == undefined){         
              marker = new google.maps.Marker({
                  map: map,
                  icon: '../engine/core/maps/turtle50x50reversed.png',
                  position: results[0].geometry.location,
                  draggable: true,
                  animation: google.maps.Animation.DROP,
                  title: address
              });

              marker.setDraggable (true);
              updatePositionInfo(marker.getPosition());
              
              google.maps.event.addListener(marker, 'dragend', function(){
                  updatePositionInfo(marker.getPosition());                  
                   map.panTo(marker.getPosition());
              });
              google.maps.event.addListener(marker, 'click', function(){
                  if (marker.getAnimation() != null) {
                    marker.setAnimation(null);
                  } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                  }
              });
      map.setZoom(zoom);
              
          } else {
              map.panTo(results[0].geometry.location);
              map.setZoom(zoom);
              marker.setPosition(results[0].geometry.location);
              marker.setTitle(address);
              marker.setAnimation(google.maps.Animation.DROP);
              updatePositionInfo(marker.getPosition());
          }
          
        } else {
            //NOT FOUND..... DO NOTHING!          
        }
      });
    }

    function updatePositionInfo(position){
        document.getElementById('lat').value = position.lat();
        document.getElementById('long').value = position.lng();
    }
    
   

    //google.maps.event.addDomListener(window, 'load', initialize);



</script>


<div id="map_panel">
    
        
    <div id="location_fields">
        
        <label class="map-label">Country</label>        
        <select class="map-select" name="country" id="country">
            <option value="0">Unknown</option>
            <?php foreach (get_all_COUNTRY_CONSTANTS() as $country){ ?>
                <option value="<?php echo $country['id']?>"><?php echo $country['name']?></option>
            <?php  } ?>
        </select>
        
        <label class="map-label">Region</label>        
        <select class="map-select" name="region" id="region">
            <option value="0">Unknown</option>';
        </select>
        
        <label class="map-label">Address line 1</label>
        <input class="map-text" name="addr1" id="addr1" type="textbox" value="" placeholder="Address.....">
        <label class="map-label">Address line 2</label>
        <input class="map-text" name="addr2" id="addr2" type="textbox" value="" placeholder="Address.....">       
        <label class="map-label">Town/City</label>
        <input class="map-text" name="city" id="city" type="textbox" value="" placeholder="Town/City.....">
        <label class="map-label">State/Province</label>
        <input class="map-text" name="county" id="county" type="textbox" value="" placeholder="State/Province.....">
        <label class="map-label">Postal/ZIP code</label>
        <input class="map-text" name="postcode" id="postcode" type="textbox" value="" placeholder="Postal/ZIP code.....">
        
        <input class="map-button" type="button" value="Search" onclick="codeAddress()">
                
        <input type="hidden" name="lat" id='lat'>
        <input type="hidden" name="long" id='long'>
        
    </div>
    
    <div id="map_canvas"></div>
</div>
   