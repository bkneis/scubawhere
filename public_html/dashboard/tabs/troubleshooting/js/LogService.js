var LogService = function(log_repo) {

    this.log_repo = log_repo;

    let log_list = Handlebars.compile( $("#log-list-template").html() );
    let log_details = Handlebars.compile ( $("#log-details-template").html() );

    this.renderList = function(callback) {
	    $('#log-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');
        this.log_repo.getAll(function(data) {
			renderListSuccess(data, callback);
		}, renderListError);
    };

    let renderListSuccess = function( data, callback ) {
        $("#log-list").remove();
		$("#log-list-container .loader").remove();
		$("#log-list-container").append( log_list( {logs : data} ) );
		if(typeof callback === 'function') callback();	
    };

    let renderListError = function( xhr ) {
        console.log( xhr );
    };

	this.viewBooking = function(booking_ref) {
		// Load booking data and redirect to add-booking tab
		Booking.getByRef(booking_ref, function success(object) {
			window.booking      = object;
			// window.booking.mode = 'view'; // Should be default behavior
			window.clickedEdit  = true;

			window.location.hash = 'add-booking';
		},
		function(xhr) {
			pageMssg('The booking cannot be viewed as it is already deleted');	
		});
	};

	this.renderLinks = function(description)
	{
		let refs = description.match(/[^[\]]+(?=])/g);
		console.log(refs);
		let html = '<li>' + description + '</li>';
		_.each(refs, function(obj) {
			let str = '[' + obj + ']';
			let start = html.indexOf(str);
			let end = start + str.length;
			let link = '<a class="view-booking">' + obj + '</a>';
			html = html.substring(0, start) + link + html.substring(start + link.length, html.length);
		});
		return html;
	}

    this.renderEditForm = function(id) {
    	$("#log-details-container").empty().append(log_details(window.logs[id]));
    }

    this.remove = function(id) {
        let that = this;
        log_repo.delete(id, function success(data) {
            that.renderList();
            that.renderEditForm();
        },
        function error(xhr) {
            console.log(xhr);
            alert(xhr.responseText);
        });
    };

};
