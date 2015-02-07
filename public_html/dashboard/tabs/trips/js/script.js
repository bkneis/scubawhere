// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(selectObject) {
	if(this.terms == selectObject)
		return ' selected';
	else
		return '';
});
Handlebars.registerHelper('readable', function(duration) {
	return readableDuration(duration);
});
Handlebars.registerHelper('inArray', function(needle, haystack) {
	if(haystack === undefined) return '';

	return _.has(haystack, needle) ? ' checked' : '';
});

var tripForm,
	tripList;

$(function(){

	// Render initial trip list
	tripList = Handlebars.compile( $("#trip-list-template").html() );
	renderTripList();

	// Load locations & tags, then show default create trip form
	// Promises are not supported in IE.. :( (http://caniuse.com/#feat=promises)
	Place.attached(function(data) {
		// window.places = _.indexBy(data, 'id');
		window.places = data;

		Trip.tags(function(data) {
			window.tags = _.indexBy(data, 'id');

			tripForm = Handlebars.compile( $("#trip-form-template").html() );
			renderEditForm();
		});
	});

	$("#start-tour").on('click', function(event) {
		introJs().setOption('doneLabel', 'Visit Tickets').start().oncomplete(function() {
        	window.location.href = '#tickets?multipage=true';
        });
	});

	var $tripFormContainer = $('#trip-form-container');
	// Assign eventListener for trip clicks
	$('#trip-list-container').on('click', 'li', function(event) {

		if( $(event.target).is('strong') )
			event.target = event.target.parentNode;

		renderEditForm( event.target.getAttribute('data-id') );
	});

	$tripFormContainer.on('submit', '#add-trip-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#add-trip').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Trip.create( $('#add-trip-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderTripList(function() {
				renderEditForm(data.id);
			});

		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#add-trip-form').prepend(errorsHTML);
				$('#add-trip').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-trip').prop('disabled', false);
			$('#add-trip-form').find('#save-loader').remove();
		});
	});

	$tripFormContainer.on('submit', '#update-trip-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#update-trip').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Trip.update( $('#update-trip-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			renderTripList();

			$('form').data('hasChanged', false);

			// Because the page is not re-rendered like with add-trip, we need to manually remove the error messages
			$('.errors').remove();

			$('#update-trip').prop('disabled', false);
			$('#update-trip-form').find('#save-loader').remove();

		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-trip-form').prepend(errorsHTML);
				$('#update-trip').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#update-trip').prop('disabled', false);
			$('#save-loader').remove();
		});
	});

	$("#trip-list-container").on('click', '#change-to-add-trip', function(event) {
		event.preventDefault();
		renderEditForm();
	});

	$tripFormContainer.on('click', '.remove-trip', function(event) {
    event.preventDefault();
		var check = confirm('Do you really want to remove this trip?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Trip.delete({
				'id'    : $('#update-trip-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderTripList();

				renderEditForm();
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-trip').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$tripFormContainer.on('click', '.add1d', function(event) {
		event.preventDefault();

		var $field = $('#tripDuration');
		var duration = $field.val();
		$field.val( parseInt(duration) + 24 );

		$field.trigger('change');
	});

	$tripFormContainer.on('click', '.sub1d', function(event) {
		event.preventDefault();

		var $field = $('#tripDuration');
		var duration = $field.val();
		$field.val( Math.max( 1, parseInt(duration) - 24 ) );

		$field.trigger('change');
	});

	$tripFormContainer.on('change', "#tripDuration", function() {
		$('#readableDuration').text(
			readableDuration(
				$('#tripDuration').val()
			)
		);
	});
});

function renderTripList(callback) {

	$('#trip-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Trip.getAllTrips(function success(data) {

		window.trips = _.indexBy(data, 'id');
		$('#trip-list').remove();
		$('#trip-list-container .loader').remove();

		$("#trip-list-container").append( tripList({trips : data}) );

		if(typeof callback === 'function')
			callback();
	});
}

function renderEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) {
			return false;
		}
	}

	var trip;

	if( id ) {
		trip = window.trips[id];
		trip.task = 'update';
		trip.update = true;
	}
	else {
		trip = {
			task: 'add',
			duration: 6,
		};
		trip.update = false;
	}

	trip.locations = _.indexBy(trip.locations, 'id');
	trip.tags      = _.indexBy(trip.tags, 'id');

	trip.available_locations = window.places;
	trip.available_tags      = window.tags;

	$('#trip-form-container').empty().append( tripForm(trip) );

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}

function readableDuration(hours) {
	var duration = moment.duration(parseInt(hours), 'hours');
	return /*(duration.days() > 0 ?*/ duration.days() + ' d ' /*: '')*/ + duration.hours() + ' h';
}

function changeParent(self) {
	$(self).parent().toggleClass('checked');
}

function showMe(box, self) {

	var div = $(box);

	if( $(self).is(':checked') ) {
		div.show(0);
		div.find('input, textarea').prop('disabled', false);
	}
	else {
		div.hide(0);
		div.find('input, textarea').prop('disabled', true);
	}
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}

function setToken(element) {
	if( window.token ) {
		$(element).val( window.token );
	}
	else {
		$.get('/token', function success(data) {
			window.token = data;
			setToken(element);
		});
	}
}
