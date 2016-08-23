$(function(){

	$('[name=username]').focus();

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
				$(".form-error").html("The username isn't long enough.<br>It must have at least 3 characters.");
			}

			// Check that password is over 6 chars
			if(password.length < 6) {
				error = true;
				$(".form-error").html("The password isn't long enough.<br>It must have at least 6 characters.");
			}
		}

		if(error === true) {
			$( "#login-form" ).effect( "shake" );
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
				statusCode: {
					401: function(xhr) {
						var data = JSON.parse(xhr.responseText);

						$('#loginDC').removeClass('loading');

						$(".form-error").html(data.errors[0]);

						$( "#login-form" ).effect( "shake" );
					},
					406: function(xhr) {
						var data = JSON.parse(xhr.responseText);

						$('#loginDC').removeClass('loading');

						$(".form-error").html(data.errors[0]);

						$( "#login-form" ).effect( "shake" );
					},
					202: function() {
						window.location.href = "/";
					},
					200: function() {
						window.location.href = "/";
					}
				}/*
				success: function(){
					window.location.href = "/";
				},
				error: function(xhr){
					var data = JSON.parse(xhr.responseText);

					$('#loginDC').removeClass('loading');

					$(".form-error").html(data.errors[0]);

					$( "#login-form" ).effect( "shake" );
				}*/
			});
		}
	});
});
