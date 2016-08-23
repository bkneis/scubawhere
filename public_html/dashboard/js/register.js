
$(function() {

	var company_repo = new CompanyRepo();
	var register_service = new RegisterService(company_repo);

	$('#registerForm').on('submit', function(event) {
		event.preventDefault();
		$('#registerForm input').removeClass('input-error');
		register_service.register($('#registerForm'));
	});

});
