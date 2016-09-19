function RegisterService(company_repo) {

	var company_repo = company_repo;

	var validate_data = function(data)
	{
		// Validate the format of the email
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		 // @todo implment our own serializeObject method
		 // if we were to add an element to the form we might need to change this index of 1
		var error = !(filter.test(data[1].value));
		if(error)
		{
			$('#registerForm [name="email"]').addClass('input-error');
			//$('#registerForm [name="email"]').css('border', '2px solid red');
			$('.form-errors').html('<p style="color: red; width:280px;">Please input a valid email address</p>');
			return true;
		}

		// Check if password and re entered passwords match
		if(data[5].value === data[6].value) 
		{
			return false;
		}
		else 
		{
			$('#registerForm [type="password"]').addClass('input-error');
			$('.form-errors').html('<p style="color:red; width:280px">The passwords you entered did not match</p>');
			return true;
		}
	}

	this.register = function(params, successFn, errorFn)
	{
		var err = validate_data(params.serializeArray());
		if(err) return;

		company_repo.create(params.serialize(), function success(data) {
			var success_html;
			success_html  = '<img src="/common/img/scubawhere_logo.svg">';
			success_html += '<h1>RMS Operator Sign Up</h1>';
			success_html += '<p style="width:280px" class="text-success"> Successful. Thank you for registering with ';
			success_html += 'scubawhereRMS. You can now login into your new account.</p>';
			$('#registerForm').html(success_html);
			setTimeout(function() {
				window.location.href = '/login';
			}, 2000);
		},
		function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			for(var i in data.errors)
			{
				$('.form-errors').append('<p style="color:red;">' + data.errors[i] + '</p>');
			}
			console.log(xhr);
		});
	}

}
