var accommodationForm,
    accommodationList,
    priceInput;

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

	// Render initial accommodation list
	accommodationList = Handlebars.compile( $("#accommodation-list-template").html() );
	renderAccommodationList();

	// Default view: show create accommodation form
	accommodationForm = Handlebars.compile( $("#accommodation-form-template").html() );
	renderEditForm();

	$("#start-tour").on('click', function(event) {
		var introd = introJs();
          introd.setOptions({
            steps: [
              { 
                intro: "This is the accommodations tab, if you business includes managing accommodation, you can control you resources here."
              },
              {
                element: '#accommodations-list',
                intro: 'Here you can view all of your accommodations. Click on an accommodation to edit it.',
                position : 'right'
              },
              {
                element: '#acom-name',
                intro: 'Enter the name of the room here.',
                position : 'left'
              },
              {
                element: '#cke_acom-description',
                intro: 'Enter a description of the room and accommodation here.',
                position : 'left'
              },
              {
                element: '#acom-base',
                intro: 'Base prices allow for annual price changes to be updated at the specified date',
                position : 'left'
              },
              {
                element: '#acom-season',
                intro: 'Additonally a seasonal price could be specified by clicking the checkbox. This will change the price of the room depending on the date.',
                position : 'left'
              },
              {
                element: '#acom-rooms',
                intro: 'Lastly, if your accommodation is a dorm room. Enter the number of beds available, otherwise, enter the number of rooms available.',
                position : 'left'
              }
            ]
          });
		introd.start();/*.oncomplete(function() {
        	window.location.href = '#accommodations?multipage=true';
        });*/
	});

	$("#accommodation-form-container").on('submit', '#add-accommodation-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#add-accommodation').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Accommodation.create( $('#add-accommodation-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderAccommodationList(function() {
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

	$("#accommodation-form-container").on('submit', '#update-accommodation-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#update-accommodation').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Accommodation.update( $('#update-accommodation-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			if(data.id || data.base_prices || data.prices) {
				if(!data.id)
					data.id = $('#update-accommodation-form input[name=id]').val();

				renderAccommodationList(function() {
					renderEditForm(data.id);
				});
			}
			else {
				renderAccommodationList();
				// Remove the loader
				$('#update-accommodation').prop('disabled', false);
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

	$('#accommodation-form-container').on('click', '.remove-accommodation', function(event){
		event.preventDefault();
    var check = confirm('Do you really want to remove this accommodation?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Accommodation.delete({
				'id'    : $('#update-accommodation-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderAccommodationList();

				renderEditForm();
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-accommodation').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$('#accommodation-form-container').on('click', '.deactivate-accommodation', function(event){
		event.preventDefault();
    var check = confirm('Do you really want to remove this accommodation?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Accommodation.deactivate({
				'id'    : $('#update-accommodation-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderAccommodationList();

				renderEditForm();
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.deactivate-accommodation').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	/*
	$('#accommodation-form-container').on('click', '.restore-accommodation', function(event){

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Accommodation.restore({
			'id'    : $('#update-accommodation-form input[name=id]').val(),
			'_token': $('[name=_token]').val()
		}, function success(data){

			pageMssg(data.status, true);

			renderAccommodationList();

			window.accommodations[ $('#update-accommodation-form input[name=id]').val() ].deleted_at = null;

			renderEditForm( $('#update-accommodation-form input[name=id]').val() );
		}, function error(xhr){

			pageMssg('Oops, something wasn\'t quite right');

			$('.restore-accommodation').prop('disabled', false);
			$('#save-loader').remove();
		});
	});
	*/

	$("#accommodation-list-container").on('click', '#change-to-add-accommodation', function(event){

		event.preventDefault();

		renderEditForm();
	});

	$('#accommodation-form-container').on('click', '.add-base-price', function(event) {
		event.preventDefault();

		window.sw.default_base_price.id = randomString();

		$(event.target).before( priceInput(window.sw.default_base_price) );

		initPriceDatepickers();
	});

	$('#accommodation-form-container').on('click', '.add-price', function(event) {
		event.preventDefault();

		window.sw.default_price.id = randomString();

		$(event.target).before( priceInput(window.sw.default_price) );

		initPriceDatepickers();
	});

	$('#accommodation-form-container').on('click', '.remove-price', function(event) {
		event.preventDefault();

		$(event.target).parent().remove();
	});
});

function renderAccommodationList(callback) {

	$('#accommodation-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Accommodation.getAll(function success(data) {

		window.accommodations = _.indexBy(data, 'id');
		$('#accommodation-list').remove();
		$('#accommodation-list-container .loader').remove();

		$("#accommodation-list-container").append( accommodationList({accommodations : data}) );

		// (Re)Assign eventListener for accommodation clicks
		$('#accommodation-list').on('click', 'li, strong', function(event) {

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
