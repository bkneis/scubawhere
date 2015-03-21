var Course = {

	get : function(params, handleData) {
		$.get("/api/course", params, function(data) {
			handleData(data);
		});
	},

	getAll : function(handleData) {
		$.get("/api/course/all", function(data){
			handleData(data);
		});
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/course/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/course/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/course/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};