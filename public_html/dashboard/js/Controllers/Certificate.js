var Certificate = {

	getAll : function(handleData) {
		$.get("/api/certificate/all", function(data){
			handleData(data);
		});
	}

};