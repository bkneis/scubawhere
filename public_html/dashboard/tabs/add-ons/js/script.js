// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(selectObject) {
	if(this.terms == selectObject)
		return ' selected';
	else
		return '';
});

var addonForm,
	addonList;

$(function(){

	// Render initial addon list
	addonList = Handlebars.compile( $("#addon-list-template").html() );
	renderAddonList();

	// Default view: show create addon form
	addonForm = Handlebars.compile( $("#addon-form-template").html() );
	renderEditForm();

	$("#addon-form-container").on('submit', '#add-addon-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Addon.createAddon( $('#add-addon-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderAddonList(function() {
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
				$('#add-addon-form').prepend(errorsHTML)
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
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Addon.updateAddon( $('#update-addon-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			renderAddonList();

			$('form').data('hasChanged', false);

			// Because the page is not re-rendered like with add-addon, we need to manually remove the error messages
			$('.errors').remove();

			$('#update-addon').prop('disabled', false);
			$('#update-addon-form').find('#save-loader').remove();

		}, function error(xhr) {

			data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-addon-form').prepend(errorsHTML)
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

	$("#addon-list-container").on('click', '#change-to-add-addon', function(event){

		event.preventDefault();

		renderEditForm();
	});

	$('#addon-form-container').on('click', '.remove-addon', function(event){
		var check = confirm('Do you really want to remove this addon?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Addon.deleteAddon({
				'id'    : $('#update-addon-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderAddonList();

				renderEditForm();
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-addon').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$('#addon-form-container').on('click', '.deactivate-addon', function(event){
		var check = confirm('Do you really want to deactivate this addon?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Addon.deactiveAddon({
				'id'    : $('#update-addon-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderAddonList();

				window.addons[ $('#update-addon-form input[name=id]').val() ].trashed = true;

				renderEditForm( $('#update-addon-form input[name=id]').val() );
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-addon').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$('#addon-form-container').on('click', '.restore-addon', function(event){

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Addon.restoreAddon({
			'id'    : $('#update-addon-form input[name=id]').val(),
			'_token': $('[name=_token]').val()
		}, function success(data){

			pageMssg(data.status, true);

			renderAddonList();

			window.addons[ $('#update-addon-form input[name=id]').val() ].trashed = false;

			renderEditForm( $('#update-addon-form input[name=id]').val() );
		}, function error(xhr){

			pageMssg('Oops, something wasn\'t quite right');

			$('.remove-addon').prop('disabled', false);
			$('#save-loader').remove();
		});
	});
});

function renderAddonList(callback) {

	$('#addon-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Addon.getAllAddons(function success(data) {

		window.addons = _.indexBy(data, 'id');
		$('#addon-list').remove();
		$('#addon-list-container .loader').remove();

		$("#addon-list-container").append( addonList({addons : data}) );

		// (Re)Assign eventListener for addon clicks
		$('#addon-list').on('click', 'li, strong', function(event) {

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

	var addon;

	if( id ) {
		addon = window.addons[id];
		addon.task = 'update';
		addon.update = true;
		addon.compulsory = parseInt( addon.compulsory );
	}
	else {
		addon = {
			task: 'add',
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
		addon.update = false;
	}

	$('#addon-form-container').empty().append( addonForm(addon) );

	if(!id)
		$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function(event) {
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
