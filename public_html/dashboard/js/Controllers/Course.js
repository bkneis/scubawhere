var Course = {

	get : function(id, handleData) {
		$.get("/api/course/" + id, function(data) {
			handleData(data);
		});
	},

	getAll : function(handleData) {
		$.get("/api/course", function(data){
			handleData(data);
		});
	},
	
	getAllWithTrashed : function(handleData) {
		$.get("/api/course", { with_deleted : true }, function(data){
			handleData(data);
		});
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/course",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(id, params, handleData, errorFn) {
		$.ajax({
			type: "PUT",
			url: "/api/course/" + id,
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(id, params, handleData, errorFn) {
		$.ajax({
			type: "DELETE",
			url: "/api/course/" + id,
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};
