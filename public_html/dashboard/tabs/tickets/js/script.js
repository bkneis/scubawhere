$(function () {

	//compile the trip list temlate
	var tripSource = $("#trip-template").html();
	var triptemplate = Handlebars.compile(tripSource);

	var indexedTrips;

	Trip.getAllTrips(function(data){
		indexedTrips = _.indexBy(data, 'id');
		$("#trip-select").append(triptemplate({trips : data}));

	});

	//compile for saved tickets data
	var sTicketsSource = $("#saved-tickets-template").html();
	var sTtemplate = Handlebars.compile(sTicketsSource);

	Ticket.getAllTickets(function(data){
		// 1. Sort the ticket array by trip_id
		data = _.sortBy(data, 'trip_id');
		// 2. Add certain corresponding trip details
		data = _.each(data, function(element) {
			element.trip_name = indexedTrips[ element.trip_id ].name;
		});
		$("#saved-tickets").append(sTtemplate({tickets : data}));
	});

	//compile the boat list temlate
	var boatSource = $("#boat-template").html();
	var boatTemplate = Handlebars.compile(boatSource);

	Boat.getAllBoats(function(data){
		$("#boat-select").append( boatTemplate({boats : data.boats}) );
		console.log(data);
	});


	//click event for saving a new ticket
	$("#save-ticket").click(function(e){
		e.preventDefault();
		$('#save-ticket').prop('disabled', true).after('<div id="save-ticket-loader" class="loader"></div>');

		Ticket.createTicket($("#new-ticket-form").serialize(), function(data, textStatus, xhr){
			console.log(data);
			console.log(textStatus);
			console.log(xhr);
			// window.location.reload();
			$('#save-ticket').attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#save-ticket-loader').remove();

		});
	});
});

function toggleBoatSelect(self) {
	self = $(self);
	select = self.parent().children('select');

	if( self.is(':checked') )
	{
		select.removeAttr('disabled');
	}
	else
	{
		select.prop('disabled', true);
	}
}

function toggleShowBoats() {
	$('#boat-select').toggle();
	$('#boat-select').find('[type=checkbox]').attr('checked', false).trigger('change');
}






