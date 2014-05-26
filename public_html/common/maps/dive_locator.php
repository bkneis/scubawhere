<?php
        $root = $_SERVER['DOCUMENT_ROOT'];
		require_once($root."/engine/core/db/interface/places.php");
    
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.1&sensor=false&callback=initialize"></script>
<script>


$(document).ready(function(){
    
    $("#region").attr("disabled", "disabled");
    
    $("#sea").attr("disabled", "disabled");
    
    $("#country").change(function(){
       var country_id = $("#country option:selected").attr('value');
       $.post("/engine/core/db/ajax/get_region_options_by_country.php", {countryID : country_id}, function(data){
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
    
    
    $("#ocean").change(function(){
       var ocean_id = $("#ocean option:selected").attr('value');
       $.post("/engine/core/db/ajax/get_sea_options_by_ocean.php", {oceanID : ocean_id}, function(data){
           if (data) {
               $("#sea").html(data);
               if($('#sea option').size() > 1){
                   $("#sea").removeAttr("disabled");
               } else {
                   $("#sea").attr("disabled", "disabled")
               }               
            } else {
                $("#sea").attr("disabled", "disabled");
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
        
        var locationName = document.getElementById('locationName').value;
        var search = document.getElementById('search').value;
        var zoom = 3;
        
        if (search != "") {zoom+=5;}
        var address = (search != "" ? search + " " : "")
        
        geocoder.geocode({'address': address}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {

                map.panTo(results[0].geometry.location);

                //Create a new marker if required
                if (marker === undefined) {
                    marker = new google.maps.Marker({
                        map: map,
                        icon: '../engine/core/maps/turtle50x50.png',
                        position: results[0].geometry.location,
                        draggable: true,
                        animation: google.maps.Animation.DROP,
                        title: locationName
                    });

                    marker.setDraggable(true);
                    updatePositionInfo(marker.getPosition());

                    google.maps.event.addListener(marker, 'dragend', function() {
                        updatePositionInfo(marker.getPosition());
                        map.panTo(marker.getPosition());
                    });
                    google.maps.event.addListener(marker, 'click', function() {
                        if (marker.getAnimation() !== null) {
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
                    marker.setTitle(locationName);
                    marker.setAnimation(google.maps.Animation.DROP);
                    updatePositionInfo(marker.getPosition());
                }

            } else {
                //NOT FOUND..... DO NOTHING!
            }
        });
    }


    function jumpTo() {
        
        var locationName = document.getElementById('locationName').value;
        var search = document.getElementById('search').value;
        var latitude = document.getElementById('lat').value;
        var longitude = document.getElementById('long').value;

        var address = (search != "" ? search + " " : "")
      
                var zoom = 3;
                if (search != "") {zoom+=3;}
                if (latitude != "") {zoom+=1;}
                if (longitude != "") {zoom+=1;}
                
        var latlong = new google.maps.LatLng(latitude, longitude);

        map.panTo(latlong);

        if (marker === undefined) {
            marker = new google.maps.Marker({
                map: map,
                icon: '../engine/core/maps/turtle50x50.png',
                position: latlong,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: locationName
            });

            marker.setDraggable(true);
            updatePositionInfo(marker.getPosition());

            google.maps.event.addListener(marker, 'dragend', function() {
                updatePositionInfo(marker.getPosition());
                map.panTo(marker.getPosition());
            });
            google.maps.event.addListener(marker, 'click', function() {
                if (marker.getAnimation() !== null) {
                    marker.setAnimation(null);
                } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            });
map.setZoom(zoom);

        } else {
            map.panTo(latlong);
            map.setZoom(zoom);
            marker.setPosition(latlong);
            marker.setTitle(locationName);
            marker.setAnimation(google.maps.Animation.DROP);
            updatePositionInfo(marker.getPosition());
        }

    }

    function updatePositionInfo(position) {
        document.getElementById('lat').value = position.lat();
        document.getElementById('long').value = position.lng();
    }

//    google.maps.event.addDomListener(window, 'load', initialize);
	$(function(){
		
	
		$("#save-location").click(function(e){
			var locationName = $('[name="locationName"]').val();
			var lat = $('[name="lat"]').val();
			var longi = $('[name="longi"]').val();
			var locationTags = $('[name="locationTags"]').val();
			
			$("#saved-locations > .padder").append(
			'<div class="single-saved-location" style="display:none">' +
				'<span class="del-loc">X</span>' +
				
				'<span class="loc-name">Location: <span>'+locationName+'</span></span>' +
				'<span class="loc-long">Longitude<span>'+longi+'</span></span>' +
				'<span class="loc-lat">Latitude: <span>'+lat+'</span></span>' +
				'<span class="loc-tags">Tags: <span>'+locationTags+'</span></span>' +
				
				'<input type="hidden" name="locationName[]" value="' + locationName + '" />' +
				'<input type="hidden" name="locationLong[]" value="' + longi + '" />' +
				'<input type="hidden" name="locationLat[]"  value="' + lat +  '" />' +
				'<input type="hidden" name="locationTags[]" value="' + locationTags + '" />' +
			'</div>');
		    
		    $(".single-saved-location").filter(function() { return $(this).css("display") == "none" }).slideDown("1000");
		    e.preventDefault();
		    
		});
        		
    	$("#guts").delegate(".del-loc", "click", function(){
        	var delThis = $(this).parent();
        	delThis.slideUp("normal", function() { $(this).remove(); } );
    	});	
        	
    });

	</script>
     <div class="box100">
			<label class="blueb">Locations</label>
			<div class="padder">
				<div class="box-mssg">Select all of the locations that this trip will navigate to/through.</div>       
				<div id="map_canvas"></div>
		</div>
    </div>
    
    <div class="row" id="location_fields">        
        <div class="box50">
        	<label class="blueb">Search the Map</label>
        	<div class="padder">
        		<div class="loc-row">
			        <label class="">Name</label>
			        <input class="" name="locationName" id="locationName" type="textbox" value="" placeholder="Name" />
        		</div>
        		
				<div class="loc-row">
			        <h5 class="bluef">Search</h5>
			        
			        <input class="" name="search" id="search" type="textbox" value="" placeholder="Search" />
			        <input class="bttn small-bttn greyb" type="button" value="By Name" onclick="codeAddress()" />
				</div>
				
				<div class="loc-row">
			        <label class="">Latitude</label>
			        <input class="" name="lat" id="lat" type="textbox" value="" placeholder="Latitude" />
				
			        <label class="">Longitude</label>
			        <input class="" name="longi" id="long" type="textbox" value="" placeholder="Longitude" />        
			        <input class="bttn small-bttn greyb" type="button" value="By Bearing" onclick="jumpTo()" />
				</div>
		        
		        <div class="loc-row">
			        <h5 class="bluef">Tags</h5>
			        <input id="tags" type="text" name="locationTags" class="tags" value="" />
		        </div>
		        <div class="loc-row">
		        	<input type="submit" value="Save Location" id="save-location" class="bttn small-bttn blueb" /> 
		        </div>
        	</div>
        </div>
        
        <div id="saved-locations" class="box50">
        	<label class="purpleb">Saved Locations</label>
        	<div class="padder">
        		
        	</div>

        </div>
              
    </div>

    
    
    




