<?php 
	$root = $_SERVER['DOCUMENT_ROOT']; 
	require_once($root."/engine/core/db/interface/places.php");
?>

<script>
	$(function(){
		//click function for any element that links away from this form
		$('.click-away').click(function(){
			//for each form element in this form
			$('#trip-add-location').find(':input').each(function(){
				var inName = $(this).attr('name');
				var inVal = $(this).val();
				
				$('#hidden-form').find("[name='" + inName + "']").val(inVal);
				
			});
			
			var loadForm = $(this).attr('data-load-form');
			$("#add-trip").load(loadForm);
		});
	});
</script>

<script>
$(function(){
	if(!$('#hidden-submit').hasClass('submit-hidden')){
	$('#hidden-submit').addClass('submit-hidden');
	}
});
</script>


<script>
//generates random string
	var rand = function() {
    return Math.random().toString(36).substr(2); // remove `0.`
	};
	
	var token = function() {
	    return rand() + rand(); // to make it longer
	};
</script>
<script>
	$(function(){
		var locationCount = 1;
		
		$('#save-location').click(function(e){
			var randomToken = token();
			var locationName = $("[name='locationName']").val();
			var lat = $("[name='lat']").val();
			var longi = $("[name='long']").val();
			var locationTags = $("[name='locationTags']").val();
			
			$("[name='locationName']").removeClass("red-border");
			$("[name='lat']").removeClass("red-border");
			$("[name='long']").removeClass("red-border");
			
			//validate the form first
			if(!$("[name='locationName']").val() || !$("[name='lat']").val() || !$("[name='long']").val()){
			
				if(!$("[name='locationName']").val()){
					$("[name='locationName']").addClass("red-border");
				}
				
				if(!$("[name='lat']").val()){
					$("[name='lat']").addClass("red-border");
				}
				
				if(!$("[name='long']").val()){
					$("[name='long']").addClass("red-border");
				}
				
			}else{
			
				$('#dive-locations').append(
				"<div class='single-location' id='" + randomToken + "'>" +
					"Name: " + locationName + "</br>" +
					"Latitude: " + lat + "</br>" +
					"Longitude: " + longi + "</br>" +
					"Tags: " + locationTags + "</br>" +
					"<a class='remove-location'><div class='form-button remove-button'>Remove Location</div></a>" +
				"</div>"
				);
				
				//set total amount of locations
				$("[name='locationCount']").val(locationCount);
				
				//also add to hidden fields
				$('#append-locations').append(
				"<div class='hidden-location' id='" + randomToken + "hidden'>" +
					"<input type='hidden' id='locationName' name='locationName[]' value='"+locationName+"'>" + 
					"<input type='hidden' id='locationLat' name='locationLat[]' value='"+lat+"'>" + 
					"<input type='hidden' id='locationLong' name='locationLong[]' value='"+longi+"'>" + 
					"<input type='hidden' id='locationTags' name='locationTags[]' value='"+locationTags+"'>" + 
				"</div>"
				);
				
				//reset fields
				$("[name='locationName']").val("");
				$("[name='lat']").val("");
				$("[name='long']").val("");
				$("[name='locationTags']").val("");
				
				locationCount++;
				
				
			}//else all inouts hold val
			e.preventDefault();
		});
		
		$( "#dive-locations" ).delegate( ".remove-location", "click", function() {
			//remove hidden fields
			$("#" + $(this).closest(".single-location").attr('id')+"hidden").remove();
			//remove visible location
			$(this).closest(".single-location").remove();
			//decrement the total locations
			$("[name='locationCount']").val($("[name='locationCount']").val() - 1);
			
			e.preventDefault();
		});
		
		$("[name='locationCount']").val($('.hidden-location').length);
		
		var i = 1;
		//append if there are already locations set
		$('.hidden-location').each(function(){
			
				locationName = $(this).children('#locationName').val();
				lat = $(this).children('#locationLat').val();
				longi = $(this).children('#locationLong').val();
				locationTags = $(this).children('#locationTags').val();
				
				$('#dive-locations').append(
				"<div class='single-location'>" +
					"Location: " + i + "</br>" +
					"Name: " + locationName + "</br>" +
					"Latitude: " + lat + "</br>" +
					"Longitude: " + longi + "</br>" +
					"Tags: " + locationTags +
					"<a class='remove-location'>Remove Location</a>" +
				"</div>"
				);
			
			i++;
		});
		
	});
</script>
<!--

<input class="map-text" name="locationName" id="locationName" type="textbox" value="" placeholder="Name">
        
        <h5 class="map-heading">Search</h5>
        
        
        <input class="map-text" name="search" id="search" type="textbox" value="" placeholder="Search">
        <input class="map-button" type="button" value="Find" onclick="codeAddress()">
        
        <label class="map-label">Latitude</label>
        <input class="map-text" name="lat" id="lat" type="textbox" value="" placeholder="Latitude">
        <label class="map-label">Longitude</label>
        <input class="map-text" name="long" id="long" type="textbox" value="" placeholder="Longitude">        
        <input class="map-button" type="button" value="Find" onclick="jumpTo()">
        
        
        
        <h5 class="map-heading">Tags</h5>
        <input id="tags" type="text" name="location-tags" class="tags" value="" /></p>
-->


<script>
$(function(){
	$('.trip-progress-stage').removeClass('stage-active');
    $('#second-stage').addClass('stage-active');
});
</script>

<form action="" method="post"  id="trip-add-location">
	<span class="form-item">
		<label class="add-trip-label item-head">Locations</label>
		<?php include($root."/engine/core/maps/dive_locator.php"); ?>
		
		
		<div id="dive-locations">
		</div>
		
		<div class="form-foot">
			<a data-load-form='tabs/add-trip-forms/details.php' class="click-away form-button trip-bttn-left">Add Details</a>
			<a data-load-form='tabs/add-trip-forms/tickets.php' class="click-away form-button trip-bttn-right">Add Tickets</a>
		</div>
	</span>
	
</form>