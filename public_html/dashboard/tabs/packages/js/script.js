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

var packageForm,
	packageList,
	ticketSelect;

$(function(){

	// Render initial package list
	packageList = Handlebars.compile( $("#package-list-template").html() );
	renderPackageList();

	// Default view: show create package form
	packageForm = Handlebars.compile( $("#package-form-template").html() );
	prepareEditForm();

	ticketSelect = Handlebars.compile( $("#ticket-select-template").html() );

	$("#package-form-container").on('click', '#add-package', function(event) {

		event.preventDefault();

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Package.createPackage( $('#add-package-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderPackageList(function() {
				prepareEditForm(data.id);
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

			renderPackageList();

			$('form').data('hasChanged', false);

			// Because the page is not re-rendered like with add-package, we need to manually remove the error messages
			$('.errors').remove();

			$('#update-package').prop('disabled', false);
			$('#save-loader').remove();

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
			$('#save-loader').remove();
		});
	});

	$('#package-form-container').on('change', '.ticket-select', function(event) {
		$self     = $(event.target);
		$quantity = $self.siblings('.quantity-input').first();
		$price    = $self.siblings('.ticket-price').first();

		var id = $self.val();

		if(id == "0") {
			// Reset
			$quantity.prop('disabled', true);
			$quantity.attr('name', '');
			$quantity.val('');

			$price.text( $price.attr('data-default') );
		}
		else {
			$quantity.prop('disabled', false);
			$quantity.attr('name', 'tickets[' + id + '][quantity]');
			$quantity.val(1);

			$price.text(window.tickets[id].currency + ' ' + window.tickets[id].decimal_price);

			// Check if empty ticket-select exists and if not, create and append one
			if( $('.ticket-list').find('.quantity-input[disabled]').length == 0) {
				$('.ticket-list').append( ticketSelect({available_tickets: window.tickets}) );
			}
		}
	});

	$('#package-form-container').on('change', '.quantity-input', function(event) {
		$quantity = $(event.target);
		$price    = $quantity.siblings('.ticket-price').first();
		$ticket   = $quantity.siblings('.ticket-select').first();
		id = $ticket.val();

		var priceSum = ($quantity.val() * window.tickets[id].decimal_price).toFixed(2);

		$price.text( window.tickets[id].currency + ' ' + priceSum );
	});

	$("#package-list-container").on('click', '#change-to-add-package', function(event){

		event.preventDefault();

		prepareEditForm();
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

			Package.deactivePackage({
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
});

function renderPackageList(callback) {

	$('#package-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Package.getAllPackages(function success(data) {

		window.packages = _.indexBy(data, 'id');
		$('#package-list').remove();
		$('#package-list-container .loader').remove();

		$("#package-list-container").append( packageList({packages : data}) );

		// (Re)Assign eventListener for package clicks
		$('#package-list').on('click', 'li, strong', function(event) {

			if( $(event.target).is('strong') )
				event.target = event.target.parentNode;

			prepareEditForm( event.target.getAttribute('data-id') );
		});

		if( typeof callback === 'function')
			callback();
	});
}

function prepareEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) {
			return false;
		}
	}

	// Load all tickets if not already available
	if( typeof window.tickets !== 'object') {
		Ticket.getAllTickets(function success(data) {
			window.tickets = _.indexBy(data, 'id');

			renderEditForm(id);
		});
	}
	else {
		renderEditForm(id);
	}
}

function renderEditForm(id) {

	var package;

	if( id ) {
		package = window.packages[id];
		package.task = 'update';
		package.update = true;
		_.each(package.tickets, function(value, key, list) {
			value.existing = true;
			value.available_tickets = window.tickets;
		});
	}
	else {
		// Set defaults for a new package form
		package = {
			task: 'add',
			update: false,
			decimal_price: '-',
		};
	}
	package.available_tickets = window.tickets;

	package.has_billing_details = package.billing_address || package.billing_email || package.billing_phone;

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
