var companyForm;

$(function() {

	companyForm = Handlebars.compile( $("#company-form-template").html());
	renderEditForm();

	$.get("/api/agency/all", function(data) {
		var agency_options = '';
		for(var key in data) {
			agency_options += '<label class="certify"><input id="agencies[]" name="agencies[]" type="checkbox" value="'+data[key].id+'"><strong>'+data[key].abbreviation+'</strong><br></label>';
		}
		$('#agencies').html( agency_options );
	});

	$.get("/api/country/all", function(data) {
		var country_select_options = '';
		for(var key in data) {
			country_select_options += '<option data-currency-id="' + data[key].currency_id + '" value="' + data[key].id + '">' + data[key].name + '</option>';
		}
		$('#country_id').append( country_select_options );
	});

	$.get("/api/currency/all", function(data) {
		var currency_select_options = '';
		for(var key in data) {
			currency_select_options += '<option value="' + data[key].id + '">' + data[key].name + '</option>';
		}
		$('#currency_id').append( currency_select_options );
	});

	CKEDITOR.replace('description');
});

function renderEditForm() {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) return false;
	}

	$('#company-form-container').empty().append( companyForm(window.company) );

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