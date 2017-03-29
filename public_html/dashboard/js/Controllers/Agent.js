var Agent = {

	getAgent : function(params, handleData) {
		$.get("/api/agent", params, function(res) {
			handleData(res.data);
		});
	},

	getAllAgents : function(handleData) {
		$.get("/api/agent/all", function(res){
			handleData(res.data);
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

    delete: function(params, handledata, erroFn) {
        $.ajax({
            type: "POST",
            url: "/api/agent/delete",
            data: params,
            success: handledata,
            error: erroFn
        });
    }

	/*
	deleteAgent : function(params, handleData){
		$.post("/api/agent/delete", params, function(data){
			handleData(data);
		});
	}
	*/
};
