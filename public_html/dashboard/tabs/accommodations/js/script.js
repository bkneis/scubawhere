var accommodationForm,
    accommodationList,
    priceInputTemplate;

// Needs to be declared before the $(function) call
Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
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

priceInputTemplate = Handlebars.templates['accommodationPriceInput'];
Handlebars.registerPartial('price_input', priceInputTemplate);

window.sw.default_first_base_price = {
	id: randomString(),
	from: '0000-00-00',
	isBase: true,
	isAlways: true
};
window.sw.default_base_price = {
	isBase: true,
	from: moment().format('YYYY-MM-DD')
};
window.sw.default_price = {
	id: randomString(),
	from: moment().format('YYYY-MM-DD'),
	until: moment().add(3, 'months').format('YYYY-MM-DD')
};

$(function(){

	// Render initial accommodation list
	accommodationList = Handlebars.compile( $("#accommodation-list-template").html() );
	accommodationForm = Handlebars.compile( $("#accommodation-form-template").html() );
	console.log(Handlebars.templates);
	//accommodationList = Handlebars.templates['accommodationList'];
	//accommodationForm = Handlebars.templates['accommodationForm'];
	loadAccommodations(); // Automatically renders the views when data is loaded

	Tour.getAcommodationsTour();

	var selAccommodationFortContainer = $("#accommodation-form-container");

	selAccommodationFortContainer.on('submit', '#add-accommodation-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#add-accommodation').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Accommodation.create( $('#add-accommodation-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			window.accommodations[data.model.id] = data.model;

			$('form').data('hasChanged', false);

			renderViews(data.model.id);
		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#add-accommodation-form').prepend(errorsHTML);
				$('#add-accommodation').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-accommodation').prop('disabled', false);
			$('#add-accommodation-form').find('#save-loader').remove();
		});
	});

	selAccommodationFortContainer.on('submit', '#update-accommodation-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#update-accommodation').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Accommodation.update( $('#update-accommodation-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			window.accommodations[data.model.id] = data.model;

			renderViews(data.model.id);
		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-accommodation-form').prepend(errorsHTML);
				$('#update-accommodation').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#update-accommodation').prop('disabled', false);
			$('.loader').remove();
		});
	});

	selAccommodationFortContainer.on('click', '.remove-accommodation', function(event){
		event.preventDefault();
	    var deletable = $('#update-accommodation-form input[name=force]').val();
       
        var check;
        if(deletable === "false")
            check = confirm('If you delete this accommodation then it will be removed from all packages associated with it, are you sure you wish to contiue?');
        else
            check = confirm('Do you really want to remove this accommodation?');

		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			var id = $('#update-accommodation-form input[name=id]').val();

			Accommodation.delete({
				'id'    : id,
				'_token': $('[name=_token]').val()
			}, function success(data) {

				pageMssg(data.status, true);

				delete window.accommodations[id];

				renderViews();
			}, function error(xhr) {

				var data = JSON.parse(xhr.responseText);
				console.log(data);

				if(data.errors.length > 0) {

					var errorsHTML = Handlebars.compile( $("#errors-template").html() );
					errorsHTML = errorsHTML(data);

					// Render error messages
					$('.errors').remove();
					$('#update-accommodation-form').prepend(errorsHTML);
					$('#update-accommodation').before(errorsHTML);
				}
				else {
					alert(xhr.responseText);
				}

				$('.remove-accommodation').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$("#accommodation-list-container").on('click', '#change-to-add-accommodation', function(event){

		event.preventDefault();

		renderEditForm();
	});

	selAccommodationFortContainer.on('click', '.add-base-price', function(event) {
		event.preventDefault();

		window.sw.default_base_price.id = randomString();

		$(event.target).before( priceInputTemplate(window.sw.default_base_price) );

		initPriceDatepickers();
	});

	selAccommodationFortContainer.on('click', '.add-price', function(event) {
		event.preventDefault();

		window.sw.default_price.id = randomString();

		$(event.target).before( priceInputTemplate(window.sw.default_price) );

		initPriceDatepickers();
	});

	selAccommodationFortContainer.on('click', '.remove-price', function(event) {
		event.preventDefault();

		$(event.target).parent().remove();
	});

});

function loadAccommodations() {
	$('#accommodation-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Accommodation.getAll(function success(data) {
		window.accommodations = _.indexBy(data, 'id');
		renderViews();
	});
}

function renderViews(id) {
	$('#accommodation-list').remove();
	$('#accommodation-list-container .loader').remove();

	$("#accommodation-list-container").append( accommodationList({accommodations : window.accommodations}) );

	// (Re)Assign eventListener for accommodation clicks
	$('#accommodation-list').on('click', 'li, strong', function(event) {
		if( $(event.target).is('strong') )
			event.target = event.target.parentNode;

		renderEditForm( event.target.getAttribute('data-id') );
	});

	renderEditForm(id);
}

function renderEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes will be lost!");
		if( !question) {
			return false;
		}
	}

	var accommodation;

	if(id) {
		accommodation = window.accommodations[id];

		accommodation.task   = 'update';
		accommodation.update = true;

		_.each(accommodation.base_prices, function(value) {
			value.isBase   = true;

			if(value.from == '0000-00-00')
				value.isAlways = true;
		});
	}
	else {
		// Set defaults for a new accommodation form
		accommodation = {
			task: 'add',
			update: false,
			base_prices: [ window.sw.default_first_base_price ],
			capacity: 0
		};
	}

	accommodation.default_price = window.sw.default_price;

	$('#accommodation-form-container').empty().append( accommodationForm(accommodation) );

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

function clearForm() {

	var accommodation = {
		task: 'add',
		update: false,
		base_prices: [ window.sw.default_first_base_price ]
	};


	accommodation.default_price = window.sw.default_price;

	$('#accommodation-form-container').empty().append( accommodationForm(accommodation) );

	$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	initPriceDatepickers();

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}
