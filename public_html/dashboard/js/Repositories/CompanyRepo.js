function CompanyRepo() {
	
	this.create = function(params, successFn, errorFn)
	{
		var errorCallback;
		if(typeof errorFn === 'undefined')
			errorCallback = defaultErrorFn;
		else
			errorCallback = errorFn;

		$.ajax({
			type 	: 'POST',
			url  	: '/api/register/company',
			data 	: params,
			success : successFn,
		   error 	: errorCallback	
		});
	}

	var errorCallback = function(xhr)
	{
		console.log(xhr.responseText);
	}

}
