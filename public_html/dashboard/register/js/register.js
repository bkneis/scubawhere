var completed = false;
var errorChecking = true;
var editor;

function validateEmail(type, email){
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var error = !(filter.test(email));
	if(error){
		if(type == 'email') $('#email').css( "border", "2px solid red" );
		else $('#business_email').css( "border", "2px solid red" );
	}
	return error;
}

function validate(sectionNum){

	var error = false;
	var section = "#section" + sectionNum;

	$( ".required", section ).css( "border", "2px #c8c8c8" );

	if(sectionNum === 1)      error = validateEmail('email', $('[name="email"]').val());
	else if(sectionNum === 2) error = validateEmail('business', $('[name="business_email"]').val());

	$( ".required", section ).each(function( index ) {
		if(!($( this ).val())) {
			$(this).css( "border", "2px solid red" );
			error = true;
		}
	});

	if(error){
		return false;
	}
	else {
		$( ".required", section ).css( "border", "2px #c8c8c8" );
		return true;
	}
}

function validateAccount(){
	var password = $('[name="passwd"]').val();
	var repassword = $('[name="repasswd"]').val();
	if(password === repassword) {
		return true;
	} return false;
}

$(function(){

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

	$.get("/api/agency/all", function(data) {
		var agency_options = '';
		for(var key in data) {
			agency_options += '<label class="certify"><input id="agencies[]" name="agencies[]" type="checkbox" value="'+data[key].id+'"><strong>'+data[key].abbreviation+'</strong><br></label>';
		}
		$('#agencies').html( agency_options );
	});

	$('#register-form').submit(function(event){

		event.preventDefault();

		$('.errors').remove();

		if(!$('#our-terms').is(':checked')) {
			alert('Please confim you have read and agreed to our terms and conditions');
			return false;
		}

		if(completed) {
			alert('The registration has already been sent!');
			return false;
		}

		$('.yellow-helper').remove();

		var form = $(this);

		$('.submit').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		var params = form.serializeObject();
		//params.terms = $('#terms').val();
		params.terms = CKEDITOR.instances.terms.getData();

		params.phone = params.phone_ext + ' ' + params.phone;
		params.business_phone = params.business_phone_ext + ' ' + params.business_phone;
		console.log(params);

		$.ajax({
			url: '/api/register/company',
			type: "POST",
			// dataType: "json",
			data: params,
			success: function(data){
				console.log(data.status);
				completed = true;

				$('#section4').html('<center><h3 class="text-success">Registration complete!</h3><p>Thank your for registering for scubawhereRMS!</p><p><strong>Please check your email inbox for a confirmation email and click the link provided.</p></center>');
				$('#section4').append('<a class="btn btn-success btn-lg" href="http://www.scubawhere.com/blog">Go back to the scubawhere homepage</a>');
				form.find('#save-loader').remove();
			},
			error: function(xhr) {

				var data = JSON.parse(xhr.responseText);
				console.log(data);

				var html = '';
				html += '<div class="alert alert-warning errors" style="color: #E82C0C;">';
				html += '	<h4>There are a few problems with the form:</h4>';
				for(i = 0; i < data.errors.length; i++)
					html += '	<h4 style="font-weight: normal;">' + data.errors[i] + '</h4>';
				html += '</div>';

				$('.errors').remove();
				$('#page-title').after(html);

				$('.submit').prop('disabled', false);
				form.find('#save-loader').remove();
			}
		});
	});

	$("#steps").steps({
		headerTag: "h3",
		bodyTag: "section",
		transitionEffect: "slideLeft",
		enableFinishButton: false,
		onStepChanging : function (event, currentIndex, newIndex) {

			// Always allow going backward even if the current step contains invalid fields!
			if (newIndex < currentIndex) {
				return true;
			}

			if(errorChecking) {

				if(completed) {
					return true;
				}

				if(currentIndex === 0) { // This is the criteria for the first step
					if(validate(1)) {
						return true;
					}
					else return false;
					//return true;
				}

				if(currentIndex === 1) {
					if(validate(2)) {
						return true;
					}
					else return false;
				}

				if(currentIndex === 2) {
					return true;
				}
			}
			else return true;
		},
		onStepChanged: function(event, currentIndex, priorIndex) {
			$('#steps-p-' + currentIndex).find('input').first().focus();
		}
	});

	$('#country_id').change(function(event) {
		var currency_id = $(event.target).find('option:selected').attr('data-currency-id');
		$('#currency_id option').filter('[value=' + currency_id + ']').prop('selected', true);
	});

	CKEDITOR.config.height = 490;

	editor = CKEDITOR.replace('terms');

});
