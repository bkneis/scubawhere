var Ticket = {

	get : function(id, handleData){
		$.get("/api/ticket/" + id, function(data){
			handleData(data);
		});
	},

	getAllTickets : function(handleData){
		$.get("/api/ticket", function(data) {
			handleData(data);
		});
	},

	getAllWithTrashed : function(handleData){
		$.get("/api/ticket", { with_deleted : true }, function(data){
			handleData(data);
		});
	},

	getOnlyAvailable : function(handleData){
		$.get("/api/ticket", { only_available : true }, function(data){
			handleData(data);
		});
	},

	create : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/ticket",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	update : function(id, params, handleData, errorFn){
		$.ajax({
			type: "PUT",
			url: "/api/ticket/" + id,
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	delete : function(id, params, handleData, errorFn){
		$.ajax({
			type: "DELETE",
			url: "/api/ticket/" + id,
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};
