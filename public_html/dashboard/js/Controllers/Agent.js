
var Agent = {

	getAgent : function(params, handleData) {
		$.get("/api/agent", function(data) {
			handleData(data);
		});
	},

	getAllAgents : function(handleData) {
		$.get("/api/agent/all", function(data){
			handleData(data);
		});
	},

	createAgent : function(params, handleData) {
		$.post("/api/agent/add", params, function(data) {
			handleData(data);
		});
	},

	updateAgent : function(params, handleData) {
		$.post("/api/agent/edit", params, function(data) {
			handleData(data);
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
