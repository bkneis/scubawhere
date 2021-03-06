
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

var pickupList;

Handlebars.registerHelper('hasPickups', function(pickups){
	if(pickups.length > 0) return false;
	else return true;
});

Handlebars.registerHelper('trimSeconds', function(date){
	return date.substring(0, date.length - 3);
});

$(function() {

	$('input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	pickupList = Handlebars.compile($("#pick-up-schedule-template").html());

	$("#date-select").val(getToday());

	loadPickups();

	$('#date-select').on('change', function() {
		loadPickups();
	});

	$('#wrapper').on('click', '.view-booking', function(event) {
		// Load booking data and redirect to add-booking tab
		Booking.getByRef($(this).html(), function success(object) {
			window.booking      = object;
			// window.booking.mode = 'view'; // Should be default behavior
			window.clickedEdit  = true;

			window.location.hash = 'add-booking';
		});
	});

});

function getToday() {
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();

	if(dd<10) {
		dd='0'+dd
	}

	if(mm<10) {
		mm='0'+mm
	}

	var date = yyyy+'-'+mm+'-'+dd;

	return date;
}

function loadPickups() {
	var params = { date : $("#date-select").val() };
	Report.getPickupSchedule(params, function success(data) {
		$("#pickup-table").empty().append( pickupList({ pickups : data.pick_ups }) );
		if(data.pick_ups.length > 0) createDataTable();
	});
}

function createDataTable() {
	var date = $('#date-select').val();
	var pickupTable = $('.reports-table').DataTable({
        "pageLength": 10,
		"dom": '<"col-md-6 dt-buttons"B><"col-md-6"f>rt<"col-md-6"l><"col-md-6"p>',
		"buttons": [
			{
				extend : 'excel',
	   			title  : 'Pickup Schedule for ' + date	
			},
			{
				extend : 'pdf',
				title  : 'Pickup Schedule for ' + date,
				orientation: 'landscape'
			},
			{
				extend : 'print',
				title  : 'Pickup Schedule for ' + date
			}
		]
	});
}

function customerData(booking) {
	this._ref          = booking.reference;
	this._name         = booking.lead_customer.firstname + booking.lead_customer.lastname;
	this._phone        = booking.lead_customer.phone;
	this._numCustomers = booking.number_of_customers;
	this._location     = booking.pick_up_location;
	this._time         = booking.pick_up_time;

	this.ref          = function() { return this._ref; }
	this.name         = function() { return this._name; }
	this.phone        = function() { return this._phone; }
	this.numCustomers = function() { return this._numCustomers; }
	this.location     = function() { return this._location; }
	this.time         = function() { return this._time; }
}
