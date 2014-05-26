<script>
	$(".dash-button").click(function(){
		  		
	  		var loadContent = $( this ).attr( 'data-load-content' );
	  		var contentTitle = $( this ).attr( 'data-content-title' );
	  		
	  
			$("#content").html('');
			$("#content").html('<div id="loading"><img src="img/loading.gif"></div>').load( loadContent );
			$("#content-title").html( contentTitle );
			
	  	});
</script>
<a class="dash-button" data-content-title="Manually Add Bookings" data-load-content="tabs/enter-bookings.php">
	<div class="dash-button-red">Add Bookings</div>
</a>
<a class="dash-button" data-content-title="Find User Booking" data-load-content="tabs/find-booking.php">
	<div class="dash-button-blue">Find Booking</div>
</a>