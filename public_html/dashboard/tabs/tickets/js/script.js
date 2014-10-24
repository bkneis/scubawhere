// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(selectObject) {
	if(this.terms == selectObject)
		return ' selected';
	else
		return '';
});
Handlebars.registerHelper('inArray', function(needle, haystack, string, elseString) {
	if(elseString === undefined) elseString = '';
	if(haystack === undefined) return elseString;

	return _.has(haystack, needle) ? string : elseString;
});
Handlebars.registerHelper('isEqualDeepPivot', function(compare, array, key, attribute, string) {
	if(!array) return '';
	if( !_.has(array, key) ) return '';

	return array[key].pivot[attribute] === compare ? string : '';
});

$(function () {

	// Handlebars Prep
	ticketList = Handlebars.compile( $("#ticket-list-template").html() );
	renderTicketList();

	//Render initial form and ticket list

	Trip.getAllTrips(function success(data){
		window.trips = _.indexBy(data, 'id');

			Boat.getAllBoats(function success(data){
				window.boats = _.indexBy(data.boats, 'id');

				ticketForm = Handlebars.compile( $("#ticket-form-template").html() );
				renderEditForm();
			});
	});

	$('#ticket-list-container').on('click', 'li', function(event) {

		if( $(event.target).is('strong') )
			event.target = event.target.parentNode;

		renderEditForm( event.target.getAttribute('data-id') );
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
	$("#ticket-form-container").on('click', '#update-ticket', function(event) {

		event.preventDefault();

		$('#update-ticket').prop('disabled', true).after('<div id="update-loader" class="loader"></div>');

		Ticket.updateTicket($("#update-ticket-form").serialize(), function success(data) {

			pageMssg(data.status, true);

			renderTicketList();

			$('form').data('hasChanged', false);

			// Because the page is not re-rendered like with add-agent, we need to manually remove the error messages
			$('.errors').remove();

			$('#update-ticket').prop('disabled', false);
			$('#update-ticket-form').find('#update-loader').remove();
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

		ticket.task     = 'update';
		ticket.update   = true;
		ticket.trips    = _.indexBy(ticket.trips, 'id');
		ticket.boats    = _.indexBy(ticket.boats, 'id');
		ticket.hasBoats = Object.keys(ticket.boats).length > 0;
	}
	else {
		ticket = {
			task: 'add',
			update: false,
			hasBoats: false,
		};
	}

	ticket.available_trips = window.trips;
	ticket.available_boats = window.boats;

	$('#ticket-form-container').empty().append( ticketForm(ticket) );

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

	// Set all child inputs to not-checked and trigger disabling of select fields
	$('#boat-select').find('[type=checkbox]').attr('checked', false).trigger('change');
}

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
