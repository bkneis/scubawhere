var LogObserver = function(log_service) {
    
    let $list_container = $("#log-list-container");
    let $details_container = $("#log-details-container");

    this.attach_handlers = function() {

		// bind this to parent in observer
	    $list_container.on('click', '#log-list li', function( event ) {

			if( $(event.target).is( 'strong' ) )
				event.target = event.target.parentNode;

			log_service.renderEditForm( event.target.getAttribute( 'data-id' ) );
		});

        $details_container.on('click', '.remove-log', function(event) {
            let check = confirm('Do you really want to remove this log?');
            if(check)
                log_service.remove($(this).attr('data-id'));
        });

		$details_container.on('click', '.view-booking', function(event) {
			event.preventDefault();
			log_service.viewBooking($(this).html());
		});
        
    };

};
