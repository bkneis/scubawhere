

<div id="outer">
  <div class="block floating w2" id="dash-clock-block"></div>
  <div class="block floating w2" id="dash-act-pie-block"></div>
  <div class="block floating" id="dash-staff-block"></div>
  <div class="block floating" id="dash-trans-block"></div>
  <div class="block floating w2" id="dash-bookings-block"></div>
  <div class="block floating w2" id="dash-checkin-block"></div>
  <div class="block floating w2" id="dash-checkout-block"></div>
  <div class="block floating w2" id="dash-booking-chart-block"></div>
  <div class="block floating" id="dash-booking-buttons-block"></div>
  <div class="block floating"></div>
  <div class="block floating"></div>
  <div class="block floating w2"></div>
  <div class="block floating"></div>
  <div class="block floating"></div>
  <div class="block floating w2"></div>
  <div class="block floating"></div>
</div>

<script>
$(function(){
	

	var container = document.querySelector('#outer');
	var msnry = new Masonry( container, {
	  // options
	  columnWidth: 200,
	  itemSelector: '.block',
	  "gutter": 10,
	  "isFitWidth": true
	
	});				
	//load dashboard date
	$("#dash-clock-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/date.php" );
	
	//load number of inactive trips
	$("#dash-act-pie-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/act-pie.php" );
	
	//loads staff
	$("#dash-staff-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/staff.php" );
	
	//loads transaction cart number
	$("#dash-trans-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/trans-cart.php" );
	
	//loads most recent bookings
	$("#dash-bookings-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/bookings.php" );
	
	//loads most bookings chart
	$("#dash-booking-chart-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/bookings-chart.php" );
	
	//loads todays check ins - or trips starting today
	$("#dash-checkin-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/trips-starting.php" );
	
	//loads most bookings chart
	$("#dash-checkout-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/trips-ending.php" );
	
	//loads most bookings chart
	$("#dash-checkout-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/trips-ending.php" );
	
	//load booking buttons
	$("#dash-booking-buttons-block").html('<div id="loading"><img src="img/loading.gif"></div>').load( "tabs/dash-widgets/booking-buttons.php" );
  
});		
</script>