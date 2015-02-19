$.ajaxSetup({
	beforeSend: function(xhr, options) {

		// Only continue if we have to remap a API request
		if(options.url.substr(0, 4) !== '/api') return true;

		// Figure out correct url prefix
		var prefix = window.location.hostname === 'rms.scubawhere.com' ? 'api' : 'api-test';

		// Start new AJAX request with changed url
		$.ajax(
			$.extend(this, {
				url: '//' + prefix + '.scubawhere.com' + options.url
			})
		);

		// Cancel original request
		return false;
	}
});

$(function(){

	$("#loginDC").click(function(e) {

		e.preventDefault();

		var error = false;

		var username = $('[name="username"]').val();
		var password = $('[name="password"]').val();

		if((!username)||(!password)) {
			error = true;
			$(".form-error").html("Please complete the form.");

		}
		else {
			if(username.length <= 2) {
				error = true;
				$(".form-error").html("The username isn't long enough. Must have at least 3 characters.");
			}

			// Check that password is over 6 chars
			if(password.length < 6) {
				error = true;
				$(".form-error").html("The password isn't long enough. Must have at least 6 characters.");
			}
		}

		if(error === true) {
			$( "form" ).effect( "shake" );
			console.log(error);
		}
		else {
			// Set loading indicator
			$('#loginDC').addClass('loading');

			// Send login request
			$.ajax({
				url: '/api/login',
				type: "POST",
				dataType: "json",
				data: $("#loginForm").serialize(),
				success: function(){
					window.location.href = "/dashboard/";
				},
				error: function(xhr){
					var data = JSON.parse(xhr.responseText);

					$('#loginDC').removeClass('loading');

					$(".form-error").html(data.errors[0]);

					$( "form" ).effect( "shake" );
				}
			});
		}
	});
});
