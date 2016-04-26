var addonForm,
	addonList,
	priceInputTemplate;

// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(selectObject) {
	if(this.terms == selectObject)
		return ' selected';
	else
		return '';
});

Handlebars.registerHelper('pricerange', function(base_prices) {

	var min = 9007199254740992, // http://stackoverflow.com/questions/307179/what-is-javascripts-highest-integer-value-that-a-number-can-go-to-without-losin
	    max = 0;

	if( base_prices.length === 1 ) {
		return window.company.currency.symbol + ' ' + base_prices[0].decimal_price;
	}

	_.each(base_prices, function(value) {
		min = Math.min(value.decimal_price, min).toFixed(2);
		max = Math.max(value.decimal_price, max).toFixed(2);
	});

	return window.company.currency.symbol + ' ' + min + ' - ' + max;
});

Handlebars.registerHelper('currency', function() {
	if(typeof window.company !== 'undefined')
		return window.company.currency.symbol;
	else
		return '???'; // TODO Set placeholder and try again in a second (similar to the 'countryName' helper)
});

priceInputTemplate = Handlebars.templates.priceInput();
Handlebars.registerPartial('price_input', priceInputTemplate);

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

$(function(){

	// Render initial addon list
	addonList = Handlebars.templates.addonList();
	//addonList = Handlebars.compile( $("#addon-list-template").html() );
	addonForm = Handlebars.templates.addonForm();
	loadAddons(); // Automatically renders the views when data is loaded

	Tour.getAddonsTour();
	$("#addon-form-container").on('submit', '#add-addon-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#add-addon').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Addon.createAddon( $('#add-addon-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			window.addons[data.model.id] = data.model;

			$('form').data('hasChanged', false);

			renderViews(data.model.id);
		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.templates.errors();
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#add-addon-form').prepend(errorsHTML);
				$('#add-addon').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-addon').prop('disabled', false);
			$('#add-addon-form').find('#save-loader').remove();
		});
	});

	$("#addon-form-container").on('submit', '#update-addon-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#update-addon').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Addon.updateAddon( $('#update-addon-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			window.addons[data.model.id] = data.model;

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
				$('#update-addon-form').prepend(errorsHTML);
				$('#update-addon').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#update-addon').prop('disabled', false);
			$('#save-loader').remove();
		});
	});

	$("#addon-list-container").on('click', '#change-to-add-addon', function(event) {

		event.preventDefault();

		renderEditForm();
	});

	$('#addon-form-container').on('click', '.remove-addon', function(event) {
    event.preventDefault();
		var check = confirm('Do you really want to remove this addon?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			var id = $('#update-addon-form input[name=id]').val();

			Addon.deleteAddon({
				'id'    : id,
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				delete window.addons[id];

				renderViews()
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-addon').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$('#addon-form-container').on('click', '.add-base-price', function(event) {
		event.preventDefault();

		window.sw.default_base_price.id = randomString();

		$(event.target).before( priceInputTemplate(window.sw.default_base_price) );

		initPriceDatepickers();
	});

	$('#addon-form-container').on('click', '.remove-price', function(event) {
		event.preventDefault();

		$(event.target).parent().remove();
	});

});

function loadAddons() {

	$('#addon-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Addon.getAllAddons(function success(data) {
		window.addons = _.indexBy(data, 'id');
		renderViews();
	});
}

function renderViews(id) {
	$('#addon-list').remove();
	$('#addon-list-container .loader').remove();

	$("#addon-list-container").append( addonList({addons : window.addons}) );

	// (Re)Assign eventListener for addon clicks
	$('#addon-list').on('click', 'li, strong', function(event) {
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

	var addon;

	if( id ) {
		addon = window.addons[id];
		addon.task = 'update';
		addon.update = true;
		addon.compulsory = parseInt( addon.compulsory );

		_.each(addon.base_prices, function(value) {
			value.isBase = true;

			if(value.from == '0000-00-00')
				value.isAlways = true;
		});

		if(_.size(addon.base_prices) === 0)
			addon.base_prices = [window.sw.default_first_base_price];
	}
	else {
		addon = {
			task: 'add',
			update: false,
			base_prices: [ window.sw.default_first_base_price ],
		};
	}

	$('#addon-form-container').empty().append( addonForm(addon) );

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

function showMe(box, self) {

	var div = $(box);

	if( $(self).is(':checked') ) {
		div.show(0);
		div.find('input, textarea').prop('disabled', false);
	}
	else {
		div.hide(0);
		div.find('input, textarea').prop('disabled', true);
	}
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}

function clearForm() {

	var addon;

	addon = {
		task: 'add',
		update: false,
		base_prices: [ window.sw.default_first_base_price ],
		/*name: 'TUI',
		website: 'http://tui.com',
		branch_name: 'Fishponds',
		branch_address: 'R8t down the road\nBristol, BS16 2HG',
		branch_phone: '+44791234567',
		branch_email: 'fishponds@uk.tui.com',
		commission: 12.8,
		terms: 'deposit',
		has_billing_details: function() {
			return this.billing_address || this.billing_email || this.billing_phone;
		},*/
	};

	$('#addon-form-container').empty().append( addonForm(addon) );

	$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	initPriceDatepickers();

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}
