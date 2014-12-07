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
				url: "/login",
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
