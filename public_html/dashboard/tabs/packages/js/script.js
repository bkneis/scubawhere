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
Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});
Handlebars.registerPartial('ticket_select', $('#ticket-select-template').html());

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

$(function(){

	// Render initial package list
	packageList = Handlebars.compile( $("#package-list-template").html() );
	renderPackageList();

	// Default view: show create package form
	Ticket.getAllTickets(function success(data) {
		window.tickets = _.indexBy(data, 'id');

		packageForm = Handlebars.compile( $("#package-form-template").html() );
		renderEditForm();
		startTour();
	});

	ticketSelect = Handlebars.compile( $("#ticket-select-template").html() );

	$("#package-form-container").on('submit', '#add-package-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#add-package').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Package.createPackage( $('#add-package-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderPackageList(function() {
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
				$('#add-package-form').prepend(errorsHTML);
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

	$("#package-form-container").on('submit', '#update-package-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#update-package').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Package.updatePackage( $('#update-package-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			if(data.id || data.base_prices || data.prices) {
				if(!data.id)
					data.id = $('#update-package-form input[name=id]').val();

				renderPackageList(function() {
					renderEditForm(data.id);
				});
			}
			else {
				renderPackageList();
				// Remove the loader
				$('#update-package').prop('disabled', false);
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
				$('#update-package-form').prepend(errorsHTML);
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
		var $self     = $(event.target);
		var $quantity = $self.siblings('.quantity-input').first();
		var $prices   = $self.siblings('.ticket-prices').first();

		var id = $self.val(), disabledInputs, numberOfDisabledInputs;

		if(id == "0") {
			// Reset
			$quantity.prop('disabled', true);
			$quantity.attr('name', '');
			$quantity.val('');

			$prices.html( $prices.attr('data-default') );

			// Check if more than one empty ticket-selects exist and if so, remove the extra one
			disabledInputs         = $('.ticket-list').find('.quantity-input[disabled]');
			numberOfDisabledInputs = disabledInputs.length;
			if( numberOfDisabledInputs > 1) {
				disabledInputs.last().parent().remove();
			}
		}
		else {
			$quantity.prop('disabled', false);
			$quantity.attr('name', 'tickets[' + id + '][quantity]');
			$quantity.val(1);

			$quantity.trigger('change');

			// Check if empty ticket-select exists and if not, create and append one
			disabledInputs         = $('.ticket-list').find('.quantity-input[disabled]');
			numberOfDisabledInputs = disabledInputs.length;
			if( numberOfDisabledInputs === 0) {
				$('.ticket-list').append( ticketSelect({available_tickets: window.tickets}) );
			}
		}
	});

	$('#package-form-container').on('change', '.quantity-input', function(event) {
		var $quantity = $(event.target);
		var $prices   = $quantity.siblings('.ticket-prices').first();
		var $ticket   = $quantity.siblings('.ticket-select').first();
		var id = $ticket.val();

		/*
		var html = '';
		_.each(window.tickets[id].prices, function(p, index, list) {
			html += '<span style="border: 1px solid lightgray; padding: 0.25em 0.5em;">' + p.fromDay + '/' + p.fromMonth + ' - ' + p.untilDay + '/' + p.untilMonth + ': ' + window.company.currency.symbol + ' ' + ($quantity.val() * p.decimal_price).toFixed(2) + '</span> ';
		});

		$prices.html(html);
		*/
	});

	$('#package-form-container').on('click', '.remove-package', function(event) {
    event.preventDefault();
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

	$('#package-form-container').on('click', '.deactivate-package', function(event) {
    event.preventDefault();
		var check = confirm('Do you really want to remove this package?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Package.deactivatePackage({
				'id'    : $('#update-package-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderPackageList();

				renderEditForm();
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.deactivate-package').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	/*
	$('#package-form-container').on('click', '.restore-package', function(event){

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Package.restorePackage({
			'id'    : $('#update-package-form input[name=id]').val(),
			'_token': $('[name=_token]').val()
		}, function success(data){

			pageMssg(data.status, true);

			renderPackageList();

			window.packages[ $('#update-package-form input[name=id]').val() ].deleted_at = false;

			renderEditForm( $('#update-package-form input[name=id]').val() );
		}, function error(xhr){

			pageMssg('Oops, something wasn\'t quite right');

			$('.restore-package').prop('disabled', false);
			$('#save-loader').remove();
		});
	});
	*/

	$("#package-list-container").on('click', '#change-to-add-package', function(event){

		event.preventDefault();

		renderEditForm();
	});

	$('#package-form-container').on('click', '.add-base-price', function(event) {
		event.preventDefault();

		window.sw.default_base_price.id = randomString();

		$(event.target).before( priceInput(window.sw.default_base_price) );

		initPriceDatepickers();
	});

	$('#package-form-container').on('click', '.add-price', function(event) {
		event.preventDefault();

		window.sw.default_price.id = randomString();

		$(event.target).before( priceInput(window.sw.default_price) );

		initPriceDatepickers();
	});

	$('#package-form-container').on('click', '.remove-price', function(event) {
		event.preventDefault();

		$(event.target).parent().remove();
	});

	$("#tour-next-step").on("click", function() {
		window.location.href = "#add-ons";
		window.currentStep = "#add-ons";
	});
	
});

function startTour() {

	if(window.tourStart) {

		introJs().setOptions( {
			showStepNumbers : false,
			exitOnOverlayClick : false,
            exitOnEsc : false
			}).start().onchange(function(targetElement) {
				switch (targetElement.id) {  
					case "package-form-container":
			        	$("#package-name").val("Family dive day");
			        	break;
		        	case "package-tickets":
			        	$("#package-tickets").find(".ticket-select").filter(":first").val(1);
			        	$("#package-tickets").find(".quantity-input").filter(":first").val(4);
			        	break;
			        case "package-base":
			        	$("#package-base").find(".base-price").filter(":first").val(150);
			        	break;
			        case "package-seasonal":
			        	$("#package-seasonal").find('input[type=checkbox]').filter(':first').click();
			        	break;
			        case "package-size":
			        	$("#package-capacity").val(4);
			        	break;
			        case "packages-list-div":
			        	$("#package-list").append('<li id="dummy-package"><strong>Family dive day</strong> | £150.00 </li>');
			        	break;
		        }
			}).oncomplete(function() {
				$("#dummy-package").remove();
				clearForm();
			});
	}

}

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

		_.each(package.tickets, function(value) {
			value.existing = true;
			value.available_tickets = window.tickets;
		});

		_.each(package.base_prices, function(value) {
			value.isBase   = true;

			if(value.from == '0000-00-00')
				value.isAlways = true;
		});
	}
	else {
		// Set defaults for a new package form
		package = {
			task: 'add',
			update: false,
			base_prices: [ window.sw.default_first_base_price ],
		};
	}

	package.available_tickets = window.tickets;
	package.default_price     = window.sw.default_price;

	$('#package-form-container').empty().append( packageForm(package) );

	if(!id)
		$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	initPriceDatepickers();

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

function clearForm() {

	var package;

	// Set defaults for a new package form
	package = {
		task: 'add',
		update: false,
		base_prices: [ window.sw.default_first_base_price ],
	};

	package.available_tickets = window.tickets;
	package.default_price     = window.sw.default_price;

	$('#package-form-container').empty().append( packageForm(package) );

	$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	initPriceDatepickers();

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}
