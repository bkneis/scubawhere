var Campaign = {

	get : function(params, handleData) {
		$.get("/api/campaign", params, handleData);
	},

	getAll : function(handleData) {
		$.get("/api/campaign/all", handleData);
	},

	create : function(params, handleData, errorFn) {
		$.ajax({
			type: "POST",
			url: "/api/campaign/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},
    
    createTemplate : function(params, handleData, errorFn) {
        $.ajax({
			type: "POST",
			url: "/api/campaign_template/add",
			data: params,
			success: handleData,
			error: errorFn
		});
    },
    
    getAllTemplates : function(handleData) {
        $.get("/api/campaign_template/all", handleData);
    },
    
    getAnalytics : function(id, handleData) {
        $.get("/api/campaign/analytics", id, handleData);
    },
    
    getAutomationRules : function(handleData) {
        $.get("/api/campaign/automationRules", handleData);
    }

};