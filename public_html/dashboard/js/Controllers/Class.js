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

	getSessions : function(params, handleData) {
		$.get("/api/class-session/filter", params, function(data){
			handleData(data);
		});
	},

	getAllSessions : function(params, handleData) {
		$.get("/api/class-session/all", params, function(data){
			handleData(data);
		});
	},

	createSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class-session/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	updateSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class-session/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deleteSession: function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class-session/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	deactivateSession: function(params, handleData) {
		$.post("/api/class-session/deactivate", params, function(data){
			handleData(data);
		});
	},

	restoreSession: function(params, handleData) {
		$.post("/api/class-session/restore", params, function(data){
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
	},

	delete : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/class/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
	}

};