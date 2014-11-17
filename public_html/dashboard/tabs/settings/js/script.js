var companyForm;
var errorsHTML;

$(function() {
	$.get("/api/agency/all", function(data) {
		window.company.agencies       = _.indexBy(window.company.agencies, 'id');
		window.company.other_agencies = _.omit ( _.indexBy(data, 'id'), _.keys(window.company.agencies) );

		companyForm = Handlebars.compile( $("#company-form-template").html());
		renderEditForm();
	});

	$('#company-form-container').on('submit', '#update-company-form', function(event) {

		event.preventDefault();

		var form = $(this);

		$('.submit').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		var params = form.serialize();
		$.ajax({
			url: "/company/update",
			type: "POST",
			data: params,
			success: function(data){
				// Assign updated company data to window.company object
				window.company = data.company;

				pageMssg(data.status, true);

				$('form').data('hasChanged', false);

				$('.submit').prop('disabled', false);
				$('.loader').remove();
			},
			error: function(xhr) {
				data = JSON.parse(xhr.responseText);
				pageMssg('Oops, something wasn\'t quite right');

				errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#company-form-container').prepend(errorsHTML);
				$('.submit').prop('disabled', false);
				$('.loader').remove();
			}
		});
	});

	$('#company-form-container').on('click', '#send-password', function(event) {
		event.preventDefault();

		$.ajax({
			url: "/password/remind",
			type: "POST",
			data: {'email': window.company.email},
			success: function(data) {
				pageMssg(data.status, true);
			},
			error: function(xhr) {
				data = JSON.parse(xhr.responseText);
				if(xhr.status == 500)
					pageMssg('Server error: ' + data.errors[0]);
				else
					pageMssg(data.errors[0]);
			}
		});
	});
});

function renderEditForm() {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) return false;
	}

	//console.log(window.company);
	$('#company-form-container').empty().append( companyForm(window.company) );

	CKEDITOR.replace('description');

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
