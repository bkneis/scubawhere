var companyForm;
var errorsHTML;

$(function() {

	companyForm = Handlebars.compile( $("#company-form-template").html());
	renderEditForm();

	CKEDITOR.replace('description');

	$.get("/api/agency/all", function(data) {
		var agency_options = '';
		for(var key in data) {
			agency_options += '<label class="certify"><input id="agencies[]" name="agencies[]" type="checkbox" value="'+data[key].id+'"><strong>'+data[key].abbreviation+'</strong><br></label>';
		}
		$('#agencies').html( agency_options );
	});

	$('#update-company-form').submit(function(event){

		event.preventDefault();

		var form = $(this);

		$('.submit').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		var params = form.serialize();
		$.ajax({
			url: "/company/update",
			type: "POST",
			// dataType: "json",
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
});

function renderEditForm() {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) return false;
	}

	//console.log(window.company);
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
