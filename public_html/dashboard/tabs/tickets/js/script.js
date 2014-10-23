// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(selectObject) {
	if(this.terms == selectObject)
		return ' selected';
	else
		return '';
});

var tripList;

$(function () {

	// Render initial agent list
	ticketList = Handlebars.compile( $("#ticket-list-template").html() );
	renderTicketList();

	// Default view: show create ticket form
	ticketForm = Handlebars.compile( $("#ticket-form-template").html() );
	renderEditForm();

	tripTemplate = Handlebars.compile( $("#trip-list-template").html() );
	
	Trip.getAllTrips(function success(data){
		indexedTrips = _.indexBy(data, 'id');
		$("#trip-select").empty().append(tripTemplate({trips : data}));

		// --------------------------------- //
		// 2. Compile for saved tickets data //
		// --------------------------------- //

		Ticket.getAllTickets(function success(data){
			// Sort the ticket array by trip_id
			data = _.sortBy(data, 'trip_id');
			// -------------------------------- //
			// 3. Compile the boat list temlate //
			// -------------------------------- //
			var boatTemplate = Handlebars.compile($("#boat-template").html());

			Boat.getAllBoats(function success(data){
				$("#boat-select").empty().append( boatTemplate({boats : data.boats}) );
			});
		});
	});

	$("#ticket-form-container").on('click', '#add-ticket', function(event) {

		event.preventDefault();

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Ticket.createTicket( $('#add-ticket-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderTicketList(function() {
				renderEditForm(data.id);
			});

		}, function error(xhr) {

			data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#add-ticket-form').prepend(errorsHTML)
				$('#add-ticket').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-ticket').prop('disabled', false);
			$('#add-ticket-form').find('#save-loader').remove();
		});
	});

	// Click event for saving a new ticket
	$("#update-ticket").click(function(e){
		e.preventDefault();
		$('#update-ticket').prop('disabled', true).after('<div id="update-loader" class="loader"></div>');

		Ticket.updateTicket($("#update-ticket-form").serialize(), function success(data) {
			$('#update-ticket').attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#update-loader').remove();

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
				$('#update-ticket-form').prepend(errorsHTML)
				$('#update-ticket').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#save-ticket').prop('disabled', false);
			$('#save-loader').remove();
		});
	});

	$("#ticket-list-container").on('click', '#change-to-add-ticket', function(event){

		event.preventDefault();

		renderEditForm();
	});
});

function renderTicketList(callback) {

	$('#ticket-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Ticket.getAllTickets(function success(data) {

		window.tickets = _.indexBy(data, 'id');
		$('#ticket-list').remove();
		$('#ticket-list-container .loader').remove();

		$("#ticket-list-container").append( ticketList({tickets : data}) );

		// (Re)Assign eventListener for ticket clicks
		$('#ticket-list').on('click', 'li, strong', function(event) {

			if( $(event.target).is('strong') )
				event.target = event.target.parentNode;

			renderEditForm( event.target.getAttribute('data-id') );
		});

		if( typeof callback === 'function')
			callback();
	});
}

function renderEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) {
			return false;
		}
	}

	var ticket;

	if(id) {
		ticket = window.tickets[id];
		ticket.task = 'update';
		ticket.update = true;
	}
	else {
		ticket = {
			task: 'add',
		};
		ticket.update = false;
	}

	$('#ticket-form-container').empty().append( ticketForm(ticket) );

	tripList = Handlebars.compile( $("#trip-list-template").html() );
	
	Trip.getAllTrips(function success(data){
		trips = _.sortBy(data, 'id');
		$("#trip-select").append(tripList({trips : data}));
	});

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function(event) {
		$('form').data('hasChanged', true);
	});
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}

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

function setToken(element) {
	if( window.token ) {
		$(element).val( window.token );
	}
	else {
		$.get('/token', function success(data) {
			window.token = data;
			setToken(element);
		});
	}
}