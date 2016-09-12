//all ticket interactions with the api

var Ticket = {

	// Param - id of ticket wanted
	getTicket : function(params, handleData){
		$.get("/api/ticket", params, function(data){
			handleData(data);
		});
	},

	// No params needed
	getAllTickets : function(handleData){
		$.get("/api/ticket/all", function(data){
			/*if(window.location.hostname === 'rms-test.scubawhere.com')
				_.each(data, function(object) {
					object.only_packaged = parseInt(object.only_packaged);
				});*/

			handleData(data);
		});
	},

	getAllWithTrashed : function(handleData){
		$.get("/api/ticket/all-with-trashed", function(data){
			if(window.location.hostname === 'rms-test.scubawhere.com')
				_.each(data, function(object) {
					object.only_packaged = parseInt(object.only_packaged);
				});

			handleData(data);
		});
	},

	getOnlyAvailable : function(handleData){
		$.get("/api/ticket/only-available", function(data){
			if(window.location.hostname === 'rms-test.scubawhere.com')
				_.each(data, function(object) {
					object.only_packaged = parseInt(object.only_packaged);
				});

			handleData(data);
		});
	},

	//Params
	// trip_id
	// name
	// description
	// price
	// currency (if not set then it will default to centres currency)
	// boats (optional - and array of boat_id => accomodation_id)
	createTicket : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/ticket/add",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	//Params
	// trip_id
	// name
	// description
	// price
	// currency (if not set then it will default to centres currency)
	// boats (optional - and array of boat_id => accomodation_id)

	// !!!!
	// The response can contain an id field. If it does it means
	// that the ticket could not simply be updated because it has
	// already been booked. Instead the old ticket has now been
	// replaced with an updated ticket in the system. The returned
	// id is the new ID of the ticket and must be used from now on!
	updateTicket : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/ticket/edit",
			data: params,
			success: handleData,
			error: errorFn
		});
	},

	//only param is ID - the id of the ticket needed to be deleted
	deleteTicket : function(params, handleData, errorFn){
		$.ajax({
			type: "POST",
			url: "/api/ticket/delete",
			data: params,
			success: handleData,
			error: errorFn
		});
		/*$.post("/api/ticket/delete", params, function(data){
			handleData(data);
		});*/
	}

};
