$(function(){

	// Fill country select
	var country_select_options = '';
	$.get("/api/country/all", function(data) {
		for(var key in data) {
			country_select_options += '<option value="' + data[key].id + '">' + data[key].name + '</option>';
		}
		$('#country-select').append( country_select_options );
	});

	$( "#regSubmit" ).click(function(e) {

		e.preventDefault();

		var error = false;

		//reset the form error
		$(".form-error").html("");

		$(".required").each(function() {
			$(this).css("border-color", "#c8c8c8");
		});

		//get form input data to validate
		var username  = $('[name="username"]').val();
		var name      = $('[name="name"]').val();
		var email     = $('[name="email"]').val();
		var address_1 = $('[name="address_1"]').val();
		var address_2 = $('[name="address_2"]').val();
		var city      = $('[name="city"]').val();
		var county    = $('[name="county"]').val();
		var postcode  = $('[name="postcode"]').val();
		var phone     = $('[name="phone"]').val();
		var website   = $('[name="website"]').val();


		// If the form isn't complete
		if((!username) || (!email) || (!name) || (!address_1) || (!city) || (!postcode) || (!phone)) {
			//form isnt complete
			error = true
			$(".form-error").html("Please complete the marked fields.");
			$(".required").each(function() {
				if($(this).val().length < 1) {
					$(this).css("border-color", "#FF7163");
				}
			});
		}
		else {

			// Check username is not already used and min 4 chars
			if(username.length >= 4) {
				// OK
			}
			else {
				error = true;
				$('[name="username"]').errorMssg("Please enter at least 4 characters.");
			}

			// Check email is an email and isnt already used
			if( !isEmail(email) ) {
				// It's not an email address
				error = true;
				$('[name="email"]').errorMssg("Invalid email.");
			}
		}

		if(error == true) {
			$( "form" ).effect( "shake" );
		}
		else {
			// Set loading indicator
			$('#regSubmit').prop('disabled', true).addClass('loading');

			//submit the form
			$.ajax({
				url: "/register/company",
				type: "POST",
				dataType: "json",
				data: $("#regForm").serialize(),
				success: function(data){
					console.log(data.status);

					$('#regForm').empty().append('<img src="/dashboard/common/img/ScubaWhere_logo.svg"><h1>Thank you!</h1><p>Your account has been created.</p><p style="color: #FF7163;"><strong>Please note:</strong><br>A manual activation by ScubaWhere is required for you to be able to access the system. Please contact <a href="mailto:hello@scubawhere.com">hello@scubawhere.com</a> if you haven\'t already done so.</p><p>We sent you a temporary password to your email address. Please check your email and then <a href="../login/">log in</a>!');
					$('')
				},
				error: function(xhr){
					data = JSON.parse(xhr.responseText);

					$('#regSubmit').prop('disabled', false).removeClass('loading');

					errors = '';
					for(var i = 0; i < data.errors.length; i++) {
						errors += data.errors[i] + '<br>';
					}

					$(".form-error").html(errors);
					$( "form" ).effect( "shake" );
				}
			});
		}
	});
});

function isEmail(email) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}

$.fn.errorMssg = function(mssg){
    $(this).after("<div class='errorMssg'>" + mssg + "</div>");
}
