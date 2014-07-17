$(function() {

	$('#datepicker').datepicker({
		dateFormat: "yy-mm-dd",
	});

	var bookingSource = $("#booking-list-template").html();
	var bookingTemplate = Handlebars.compile(bookingSource);

	/*Booking.getAllBookings(function(data){
					$("#bookings").append(bookingTemplate({booking:data}));
				});*/

	$.get("/api/booking/all").done(function(data){
			$("#bookings").append(bookingTemplate({booking:data}));
			console.log(data);
		});

	var test = document.getElementsByClassName('confirm');
	console.log(test);

});

