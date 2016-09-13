if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href= '#dashboard';
}

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

	window.promises.agencies_loaded = $.Deferred();
	window.promises.countries_loaded = $.Deferred();
	window.promises.currencies_loaded = $.Deferred();

	$.get("/api/agency/all", function(data) {
		window.company.agencies       = _.indexBy(window.company.agencies, 'id');
		window.company.other_agencies = _.omit( _.indexBy(data, 'id'), _.keys(window.company.agencies) );
		window.promises.agencies_loaded.resolve();	
	});

	$.get('/api/country/all', function(data) {
		window.countries = _.indexBy(data, 'id');
		window.promises.countries_loaded.resolve();	
	});

	$.get('/api/currency/all', function(data) {
		window.currencies = _.indexBy(data, 'id');
		window.promises.currencies_loaded.resolve();	
	});

	$.when(
		window.promises.agencies_loaded,
		window.promises.countries_loaded,
		window.promises.currencies_loaded
	).then(function() {
		companyForm = Handlebars.compile( $("#company-form-template").html());
		creditInfoTemplate = Handlebars.compile( $('#credit-info-template').html());

		
		renderEditForm();
	});

	/*$('#company-form-container').on('click', '#start-wizard', function(event) {
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
	});*/

	/*$('#company-form-container').on('click', '#start-wizard', function(event) {
		$("#guts").prepend($("#tour-nav-wizard").html());
		window.tourStart = true;
		window.currentStep = {
			tab : "#settings",
			position : 1
		};
		$(".tour-progress").on("click", function(event) {
			if(window.currentStep.position >= $(this).attr('data-position')) {
				window.location.href = $(this).attr('data-target');
			} else {
				pageMssg("Please complete the unfinished steps");
			}
		});
		window.location.href = '#settings';
	});*/


	// @todo potentially change this to trigge coniditonally on update of form thoruhg save button
	$('#company-form-container').on('click', '#upload-terms', function(event) {
		event.preventDefault();

		var terms_file = $('#terms-file').prop('files')[0];

		// Use FormData Object instead of JSON to handle the file types such as pdf
		var formData = new FormData();
		formData.append('terms_file', terms_file);
		//formData.append('company_name', window.company.name);

		// @todo change uplaod terms to copany controllr and add it to js repo
		$.ajax({
	        url: "/api/register/upload-terms",
	        type: "POST",
	        data: formData,
	        success: function(data) {
				pageMssg(data.status, 'success');
	        },
	        error: function(xhr) {
	        	console.log(xhr);
				var data = JSON.parse(xhr.responseText);
				pageMssg(data.errors[0]);
	        },
	        contentType: false,
	        processData: false
	    });
	});

	$('#company-form-container').on('change', '#country_id', function(event) {
		var currency_id = $(event.target).find('option:selected').attr('data-currency-id');
		$('#currency_id option').filter('[value=' + currency_id + ']').prop('selected', true);
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

function renderCurrencyList() {
	var currency_select_options = '';
	var selected = '';
	for(var key in window.currencies) {
		if(window.currencies[key].id === window.company.currency_id) selected = 'selected ';
		else selected = '';
		currency_select_options += '<option value="' + window.currencies[key].id + '"' + selected + '>' + window.currencies[key].name + '</option>';
	}
	$('#currency_id').append( currency_select_options );
}

function renderCountryList() {
	var country_select_options = '';
	var selected;
	for(var key in window.countries) {
		if(window.countries[key].id === window.company.country_id) selected = 'selected ';
		else selected = '';
		country_select_options += '<option data-currency-id="' + window.countries[key].currency_id + '"' + selected + ' value="' + window.countries[key].id + '">' + window.countries[key].name + '</option>';
	}
	$('#country_id').append( country_select_options );
}

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
			TourManager.getSettingsTour();
		},
		error: function(xhr) {
			console.log(xhr);
		}
	});

	renderCountryList();
	renderCurrencyList();

	setToken('[name=_token]');

	/*$('#terms-file').bind('fileuploadsubmit', function (e, data) {
		data.formData = {
			_token: window.token
		};
	});

    $('#terms-file').fileupload({
        url: '/api/register/upload-terms',
        maxFileSize: 50000000,
        done: function (e, data) {
			pageMssg(data.result.status, 'success');
        },
		fail: function(e, data) {
			var res = JSON.parse(data.jqXHR.responseText);
			pageMssg(res.errors[0]);
		}
    });*/

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}
