<script>
	function updateClock ( )
	    {
	    
	    var currentDateString = moment().format("dddd, MMMM Do YYYY");
	    var currentTimeString = moment().format("h:mm:ss a");
	    
	    $("#dash-clock-time").html(currentTimeString);
	    $("#dash-clock-date").html(currentDateString);
	        
	 }
	
	
	   setInterval('updateClock()', 1000);
	  
</script>

<div id="dash-clock">
	<div id="dash-clock-time">
	</div>
	<div id="dash-clock-date">
	</div>
</div>