var Company = {
	getCompany : function(handleData) {
		$.get("/company?" + Math.random()).done(function(data){
			handeData(data);
		});
	}
};
