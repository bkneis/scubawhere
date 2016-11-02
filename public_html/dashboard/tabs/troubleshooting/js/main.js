
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1)
{
	window.location.href = '#dashboard';
}


$(function() {

    let log_repo     = new LogRepo();
    let log_service  = new LogService(log_repo);
    let log_observer = new LogObserver(log_service);

	let listRendered = $.Deferred();

    log_observer.attach_handlers();

    log_service.renderList(function() { listRendered.resolve(); });

	$.when(listRendered).then(function() {
		log_service.renderEditForm();
		log_service.renderLog();
	});

	/**
	 * @note For some reason, String.replace did not work when using square brackets, so I have
	 * 		 had to use indexOf and combine the string myself. This should be examined later but I don't
	 * 		 think there is much performance overhead anyways, but if there is a cleander way to do it that would be better
	 */
	Handlebars.registerHelper('link', function(description) {
		let html = log_service.renderLinks(description);
		return new Handlebars.SafeString(html);
	});

});

