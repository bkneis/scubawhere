var Agency = {

	getAll : function(handleData) {
		$.get("/api/agency/all", function(data){
			handleData(data);
		});
	}
};
