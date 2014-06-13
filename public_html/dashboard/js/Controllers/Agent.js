
var Agent = {

	getAgent : function(params, handleData) {
		$.get("/api/agent?" + Math.random(), function(data) {
			handleData(data);
		});
	},

	getAllAgents : function(handleData) {
		$.get("/api/agent/all?" + Math.random(), function(data){
			handleData(data);
		});
	},

	createAgent : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/agent/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateAgent : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/agent/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	/*
	deleteAgent : function(params, handleData){
		$.post("/api/agent/delete", params, function(data){
			handleData(data);
		});
	}
	*/
};
