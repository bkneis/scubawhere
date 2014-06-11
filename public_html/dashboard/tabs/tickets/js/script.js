$(function () {

	// -------------------------------- //
	// 1. Compile the trip list temlate //
	// -------------------------------- //
	var tripSource = $("#trips-template").html();
	var triptemplate = Handlebars.compile(tripSource);

	var indexedTrips;

	Trip.getAllTrips(function success(data){
		indexedTrips = _.indexBy(data, 'id');
		$("#trip-select").empty().append(triptemplate({trips : data}));

		// --------------------------------- //
		// 2. Compile for saved tickets data //
		// --------------------------------- //
		var sTicketsSource = $("#saved-tickets-template").html();
		var sTtemplate = Handlebars.compile(sTicketsSource);

		Ticket.getAllTickets(function success(data){
			// Sort the ticket array by trip_id
			data = _.sortBy(data, 'trip_id');

			$("#saved-tickets").empty().append(sTtemplate({tickets : data}));

			// -------------------------------- //
			// 3. Compile the boat list temlate //
			// -------------------------------- //
			var boatSource = $("#boat-template").html();
			var boatTemplate = Handlebars.compile(boatSource);

			Boat.getAllBoats(function success(data){
				$("#boat-select").empty().append( boatTemplate({boats : data.boats}) );
			});
		});
	});

	// Click event for saving a new ticket
	$("#save-ticket").click(function(e){
		e.preventDefault();
		$('#save-ticket').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Ticket.createTicket($("#new-ticket-form").serialize(), function success(data) {

			$('#save-ticket').attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#save-loader').remove();

			pageMssg(data.status, true);

			// Trigger tab reload
			window.location.hash = "";
			$('#wrapper').html(LOADER);
			window.location.hash = "tickets";
		}, function error(xhr) {

			data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#new-ticket-form').prepend(errorsHTML)
				$('#save-ticket').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#save-ticket').prop('disabled', false);
			$('#save-loader').remove();
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

Handlebars.registerHelper('count', function(array) {
	return array.length;
});





