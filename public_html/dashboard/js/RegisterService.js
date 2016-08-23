function RegisterService(company_repo) {

	var company_repo = company_repo;

	var validate_data = function(data)
	{
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		 // @todo implment our own serializeObject method
		 // if we were to add an element to the form we might need to change this index of 1
		var error = !(filter.test(data[1].value));
		return error;
	}

	this.register = function(params, successFn, errorFn)
	{
		var err = validate_data(params.serializeArray());
		if(err)
		{
			$('#registerForm [name="email"]').css('border', '2px solid red');
			$('.form-errors').html('<p>Please fill in the highlighted fields</p>');
			return;
		}	
		company_repo.create(params.serialize(), function success(data) {
			var success_html;
			success_html  = '<img src="/common/img/scubawhere_logo.svg">';
			success_html += '<h1>RMS Operator Sign Up</h1>';
			success_html += '<p class="success-text"> Successful. Thank you for registering with';
			success_html += 'scubawhereRMS. You will recieve an email within 48 hours with confirmation';
			success_html += 'of your account';
			$('#registerForm').html(success_html);
		},
		function error(xhr) {
			console.log(xhr);
		});
	}

}
