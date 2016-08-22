var companyForm;
var creditInfoTemplate;

Handlebars.registerHelper('trimDate', function(date) {
	return date.slice(0, -9);
});

Handlebars.registerHelper('getUtil', function(capacity, assigned){
	if(capacity === assigned) return 0;
	return Math.round((assigned/capacity) * 100);
});

$(function() {
	$.get("/api/agency/all", function(data) {
		window.company.agencies       = _.indexBy(window.company.agencies, 'id');
		window.company.other_agencies = _.omit( _.indexBy(data, 'id'), _.keys(window.company.agencies) );

		companyForm = Handlebars.compile( $("#company-form-template").html());
		creditInfoTemplate = Handlebars.compile( $('#credit-info-template').html());
		
		renderEditForm();
	});

	$('#company-form-container').on('click', '#start-wizard', function(event) {
		if(window.tourStart) {
			window.location.href = window.currentStep.tab;
		}
		else {
			window.currentStep = "#dashboard";
			window.location.href = '#accommodations';
				$("#guts").prepend($("#tour-nav-wizard").html());
				window.tourStart = true;
				window.currentStep = {
					tab : "#accommodations",
					position : 1
				};
				$(".tour-progress").on("click", function(event) {
					if(window.currentStep.position >= $(this).attr('data-position')) {
						window.location.href = $(this).attr('data-target');
					} else {
						pageMssg("Please complete the unfinished steps");
					}
				});
		}
	});

	$('#company-form-container').on('submit', '#update-company-form', function(event) {

		event.preventDefault();

		var form = $(this);

		$('.update-settings').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		var params = form.serialize();
		Company.update(params, function success(data) {
			// Assign updated company data to window.company object
			window.company = data.company;

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			$('.update-settings').prop('disabled', false);
			$('.loader').remove();

			//Clear error messages
			$('.errors').remove();
		},
		function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg('Oops, something wasn\'t quite right.');

			var errorsHTML = Handlebars.compile( $("#errors-template").html() );
			errorsHTML = errorsHTML(data);

			// Render error messages
			$('.errors').remove();
			$('#company-form-container').prepend(errorsHTML);
			$('.update-settings').prop('disabled', false);
			$('.loader').remove();
		});
	});

	$('#company-form-container').on('click', '#send-password', function(event) {
		event.preventDefault();

		var btn = $(event.target);
		btn.data('text', btn.text());
		btn.prop('disabled', true).html(btn.text() + ' <i class="fa fa-cog fa-spin fa-fw"></i>');

		$.ajax({
			url: "/api/password/remind",
			type: "POST",
			data: {'email': window.company.email},
			success: function(data) {
				pageMssg(data.status, true);

				btn.prop('disabled', false).html(btn.data('text'));
			},
			error: function(xhr) {
				var data = JSON.parse(xhr.responseText);
				if(xhr.status == 500)
					pageMssg('Server error: ' + data.errors[0]);
				else
					pageMssg(data.errors[0]);

				btn.prop('disabled', false).html(btn.data('text'));
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

	$.ajax({
		type: 'GET',
		url: '/api/company/credits',
		success: function(data) {
			$('#credit-info').empty().append(creditInfoTemplate(data));	
		},
		error: function(xhr) {
			console.log(xhr);
		}
	});

	//CKEDITOR.replace('description');
	//CKEDITOR.replace('terms');

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}
