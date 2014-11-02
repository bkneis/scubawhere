var packageForm,
    packageList,
    priceInput,
    ticketSelect;

// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(ticketID) {
	if(this.id == ticketID)
		return ' selected';
	else
		return '';
});
Handlebars.registerHelper('multiply', function(a, b) {
	return (a * b).toFixed(2);
});
Handlebars.registerHelper('count', function(array) {
	var sum = 0;
	_.each(array, function(value) {
		sum += value.pivot.quantity * 1;
	});
	return sum;
});
Handlebars.registerPartial('ticket_select', $('#ticket-select-template').html());

priceInput = Handlebars.compile( $('#price-input-template').html() )
Handlebars.registerPartial('price_input', priceInput);

window.sw.available_months=[{id:1,name:'January'},{id:2,name:'February'},{id:3,name:'March'},{id:4,name:'April'},{id:5,name:'May'},{id:6,name:'June'},{id:7,name:'July'},{id:8,name:'August'},{id:9,name:'September'},{id:10,name:'October'},{id:11,name:'November'},{id:12,name:'December'}];
window.sw.default_price = {
	id: randomString(),
	fromDay   : 1,
	fromMonth : 1,
	untilDay  : 31,
	untilMonth: 12,
	available_months: window.sw.available_months,
};

$(function(){

	// Render initial package list
	packageList = Handlebars.compile( $("#package-list-template").html() );
	renderPackageList();

	// Default view: show create package form
	Ticket.getAllTickets(function success(data) {
		window.tickets = _.indexBy(data, 'id');

		packageForm = Handlebars.compile( $("#package-form-template").html() );
		renderEditForm();
	});

	ticketSelect = Handlebars.compile( $("#ticket-select-template").html() );

	$("#package-form-container").on('click', '#add-package', function(event) {

		event.preventDefault();

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Package.createPackage( $('#add-package-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderPackageList(function() {
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
				$('#add-package-form').prepend(errorsHTML)
				$('#add-package').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-package').prop('disabled', false);
			$('#add-package-form').find('#save-loader').remove();
		});
	});

	$("#package-form-container").on('click', '#update-package', function(event) {

		event.preventDefault();

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Package.updatePackage( $('#update-package-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			if(data.id) {
				renderPackageList(function() {
					renderEditForm(data.id);
				});
			}
			else {
				var editedID = $('[name=id]').val();
				window.packages[editedID].prices = data.prices;
				// Reload edit form
				renderEditForm(editedID);

				renderPackageList();
			}
		}, function error(xhr) {

			data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-package-form').prepend(errorsHTML)
				$('#update-package').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#update-package').prop('disabled', false);
			$('.loader').remove();
		});
	});

	$('#package-form-container').on('change', '.ticket-select', function(event) {
		$self     = $(event.target);
		$quantity = $self.siblings('.quantity-input').first();
		$prices   = $self.siblings('.ticket-prices').first();

		var id = $self.val();

		if(id == "0") {
			// Reset
			$quantity.prop('disabled', true);
			$quantity.attr('name', '');
			$quantity.val('');

			$prices.html( $prices.attr('data-default') );
		}
		else {
			$quantity.prop('disabled', false);
			$quantity.attr('name', 'tickets[' + id + '][quantity]');
			$quantity.val(1);

			$quantity.trigger('change');

			// Check if empty ticket-select exists and if not, create and append one
			if( $('.ticket-list').find('.quantity-input[disabled]').length == 0) {
				$('.ticket-list').append( ticketSelect({available_tickets: window.tickets}) );
			}
		}
	});

	$('#package-form-container').on('change', '.quantity-input', function(event) {
		$quantity = $(event.target);
		$prices   = $quantity.siblings('.ticket-prices').first();
		$ticket   = $quantity.siblings('.ticket-select').first();
		id = $ticket.val();

		var html = '';
		_.each(window.tickets[id].prices, function(p, index, list) {
			html += '<span style="border: 1px solid lightgray; padding: 0.25em 0.5em;">' + p.fromDay + '/' + p.fromMonth + ' - ' + p.untilDay + '/' + p.untilMonth + ': ' + p.currency + ' ' + ($quantity.val() * p.decimal_price).toFixed(2) + '</span> ';
		});

		$prices.html(html);
	});

	$('#package-form-container').on('click', '.remove-package', function(event){
		var check = confirm('Do you really want to remove this package?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Package.deletePackage({
				'id'    : $('#update-package-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderPackageList();

				renderEditForm();
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-package').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$('#package-form-container').on('click', '.deactivate-package', function(event){
		var check = confirm('Do you really want to deactivate this package?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Package.deactivatePackage({
				'id'    : $('#update-package-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderPackageList();

				window.packages[ $('#update-package-form input[name=id]').val() ].trashed = true;

				renderEditForm( $('#update-package-form input[name=id]').val() );
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-package').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$('#package-form-container').on('click', '.restore-package', function(event){

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Package.restorePackage({
			'id'    : $('#update-package-form input[name=id]').val(),
			'_token': $('[name=_token]').val()
		}, function success(data){

			pageMssg(data.status, true);

			renderPackageList();

			window.packages[ $('#update-package-form input[name=id]').val() ].trashed = false;

			renderEditForm( $('#update-package-form input[name=id]').val() );
		}, function error(xhr){

			pageMssg('Oops, something wasn\'t quite right');

			$('.remove-package').prop('disabled', false);
			$('#save-loader').remove();
		});
	});

	$("#package-list-container").on('click', '#change-to-add-package', function(event){

		event.preventDefault();

		renderEditForm();
	});

	$('#package-form-container').on('click', '.add-price', function(event) {
		event.preventDefault();

		window.sw.default_price.id = randomString();

		$(event.target).before( priceInput(window.sw.default_price) );
	});

	$('#package-form-container').on('click', '.remove-price', function(event) {
		event.preventDefault();

		$(event.target).parent().remove();
	});
});

function renderPackageList(callback) {

	$('#package-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Package.getAllWithTrashed(function success(data) {

		window.packages = _.indexBy(data, 'id');
		$('#package-list').remove();
		$('#package-list-container .loader').remove();

		$("#package-list-container").append( packageList({packages : data}) );

		// (Re)Assign eventListener for package clicks
		$('#package-list').on('click', 'li, strong', function(event) {

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

	var package;

	if(id) {
		package = window.packages[id];

		package.task   = 'update';
		package.update = true;

		_.each(package.tickets, function(value, key, list) {
			value.existing = true;
			value.available_tickets = window.tickets;
		});

		_.each(package.prices, function(value, key, list) {
			value.available_months = window.sw.available_months;
		});
	}
	else {
		// Set defaults for a new package form
		package = {
			task: 'add',
			update: false,
		};
	}

	package.available_tickets = window.tickets;

	package.default_price = window.sw.default_price;

	$('#package-form-container').empty().append( packageForm(package) );

	if(!id)
		$('input[name=name]').focus();

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function(event) {
		$('form').data('hasChanged', true);
	});
}

function unsavedChanges() {
	return $('form').data('hasChanged');
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
