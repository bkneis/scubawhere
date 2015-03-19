var Class = {

	get : function(params, handleData) {
		$.get("/api/class", params, function(data) {
			handleData(data);
		});
	},

	getAll : function(handleData) {
		$.get("/api/class/all", function(data){
			handleData(data);
		});
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};