var Company = {
	getCompany : function(handleData) {
		$.get("/company").done(function(data){
			handeData(data);
		});
	}
};
