var ticketForm,
    ticketList,
    priceInput;

window.promises = {};

// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(id) {
	if(this.id == id)
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
Handlebars.registerHelper('pricerange', function(base_prices, prices) {
	var min = 9007199254740992, // http://stackoverflow.com/questions/307179/what-is-javascripts-highest-integer-value-that-a-number-can-go-to-without-losin
	    max = 0;

	if( base_prices.length === 1 && prices.length === 0) {
		return window.company.currency.symbol + ' ' + base_prices[0].decimal_price;
	}

	_.each(base_prices, function(value) {
		min = Math.min(value.decimal_price, min).toFixed(2);
		max = Math.max(value.decimal_price, max).toFixed(2);
	});

	_.each(prices, function(value) {
		min = Math.min(value.decimal_price, min).toFixed(2);
		max = Math.max(value.decimal_price, max).toFixed(2);
	});

	return window.company.currency.symbol + ' ' + min + ' - ' + max;
});
Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});
priceInput = Handlebars.compile( $('#price-input-template').html() );
Handlebars.registerPartial('price_input', priceInput);

window.sw.default_first_base_price = {
	id: randomString(),
	from: '0000-00-00',
	isBase: true,
	isAlways: true,
};
window.sw.default_base_price = {
	isBase: true,
	from: moment().format('YYYY-MM-DD'),
};
window.sw.default_price = {
	id: randomString(),
	from: moment().format('YYYY-MM-DD'),
	until: moment().add(3, 'months').format('YYYY-MM-DD'),
};

$(function () {

	// Handlebars Prep
	ticketList = Handlebars.compile( $("#ticket-list-template").html() );
	ticketForm = Handlebars.compile( $("#ticket-form-template").html() );

	// Render initial form and ticket list
	renderTicketList();
	window.promises.loadedTrips     = $.Deferred();
	window.promises.loadedBoats     = $.Deferred();
	window.promises.loadedBoatrooms = $.Deferred();

	window.promises.loadedTrips.done(function() {
		window.promises.loadedBoats.done(function() {
			window.promises.loadedBoatrooms.done(function() {
					renderEditForm();
			});
		});
	});

	Trip.getAllTrips(function success(data){
		window.trips = _.indexBy(data, 'id');
		window.promises.loadedTrips.resolve();
	});

	Boat.getAll(function success(data){
		window.boats = _.indexBy(data, 'id');
		window.promises.loadedBoats.resolve();
	});

	Boatroom.getAll(function success(data){
		window.boatrooms = _.indexBy(data, 'id');
		window.promises.loadedBoatrooms.resolve();
	});

	$('#ticket-list-container').on('click', 'li', function(event) {

		if( $(event.target).is('strong') )
			event.target = event.target.parentNode;

		renderEditForm( event.target.getAttribute('data-id') );
	});

	$("#ticket-form-container").on('submit', '#add-ticket-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#add-ticket').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Ticket.createTicket( $('#add-ticket-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderTicketList(function() {
				renderEditForm(data.id);
			});

		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#add-ticket-form').prepend(errorsHTML);
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
	$("#ticket-form-container").on('submit', '#update-ticket-form', function(event) {

		event.preventDefault();

		$('#update-ticket').prop('disabled', true).after('<div id="update-loader" class="loader"></div>');

		Ticket.updateTicket($("#update-ticket-form").serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			if(data.id || data.base_prices || data.prices) {
				if(!data.id)
					data.id = $('#update-ticket-form input[name=id]').val();

				renderTicketList(function() {
					renderEditForm(data.id);
				});
			}
			else {
				renderTicketList();
				// Remove the loader
				$('#update-ticket').prop('disabled', false);
				$('.loader').remove();
			}
		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-ticket-form').prepend(errorsHTML);
				$('#update-ticket').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#update-ticket').prop('disabled', false);
			$('.loader').remove();
		});
	});

	$("#ticket-list-container").on('click', '#change-to-add-ticket', function(event){

		event.preventDefault();

		renderEditForm();
	});

	$('#ticket-form-container').on('click', '.add-base-price', function(event) {
		event.preventDefault();

		window.sw.default_base_price.id = randomString();

		$(event.target).before( priceInput(window.sw.default_base_price) );
	});

	$('#ticket-form-container').on('click', '.add-price', function(event) {
		event.preventDefault();

		window.sw.default_price.id = randomString();

		$(event.target).before( priceInput(window.sw.default_price) );
	});

	$('#ticket-form-container').on('click', '.remove-price', function(event) {
		event.preventDefault();

		$(event.target).parent().remove();
	});
});

function renderTicketList(callback) {

	$('#ticket-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Ticket.getAllTickets(function success(data) {

		window.tickets = _.indexBy(data, 'id');
		$('#ticket-list').remove();
		$('#ticket-list-container .loader').remove();

		$("#ticket-list-container").append( ticketList({tickets : data}) );

		if(typeof callback === 'function')
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

		ticket.task         = 'update';
		ticket.update       = true;
		ticket.trips        = _.indexBy(ticket.trips, 'id');
		ticket.boats        = _.indexBy(ticket.boats, 'id');
		ticket.boatrooms    = _.indexBy(ticket.boatrooms, 'id');
		ticket.hasBoats     = Object.keys(ticket.boats).length > 0;
		ticket.hasBoatrooms = Object.keys(ticket.boatrooms).length > 0;

		_.each(ticket.base_prices, function(value) {
			value.isBase = true;
			if(value.from == '0000-00-00')
				value.isAlways = true;
		});
	}
	else {
		ticket = {
			task: 'add',
			update: false,
			hasBoats: false,
			hasBoatrooms: false,
			base_prices: [ window.sw.default_first_base_price ],
		};
	}

	ticket.available_trips     = window.trips;
	ticket.available_boats     = window.boats;
	ticket.available_boatrooms = window.boatrooms;
	ticket.default_price       = window.sw.default_price;

	$('#ticket-form-container').empty().append( ticketForm(ticket) );

	if(!id)
		$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}

function showMe(box, self) {

	var div = $(box);

	if( $(self).is(':checked') ) {
		div.show(0);
		div.find('input, select').prop('disabled', false);
	}
	else {
		div.hide(0);
		div.find('input, select').prop('disabled', true);
	}
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
