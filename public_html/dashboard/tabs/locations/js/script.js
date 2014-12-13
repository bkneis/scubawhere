var gmap = {},
	promises = {};

window.sw.latitudeInput  = $('#newMarkerLatitude');
window.sw.longitudeInput = $('#newMarkerLongitude');

getToken();

$(function() {
	console.log('Setting container height');
	// Set up height of map container to fill whole screen whithout showing scrollbars
	$('#map-container').css('height', window.innerHeight - 200);

	loadGoogleMaps();

});

function initialise() {
	console.log('Initialising started');
	var mapOptions = {
		zoom:   8,
		center: new google.maps.LatLng(window.company.latitude, window.company.longitude)
		// center: new google.maps.LatLng(50.582847, 5.96848) // Somewhere around Aachen in Germany
	};

	window.gmap = new google.maps.Map(document.getElementById('map'), mapOptions);

	// Load locations
	console.log('Loading locations');
	window.promises.loadedLocations         = $.Deferred();
	window.promises.loadedAttachedLocations = $.Deferred();

	google.maps.event.addListenerOnce(gmap, 'bounds_changed', function() {
		loadLocationsInView();
		loadAttachedLocations();
	});

	promises.loadedLocations.done(function() {
		promises.loadedAttachedLocations.done(function() {
			// Both calls have finished and we can get to work
			console.log('All locations loaded');

			removeAttachedFromLocations();

			window.sw.markers = [];

			// Render all locations
			renderHomeLocation();
			renderLocations();
			renderAttachedLocations();

			// Register marker onclick behaviour
			_.each(window.sw.markers, function(marker) {
				google.maps.event.addListener(marker, 'click', existingMarkerClick);
			});
		});
	});

	Place.tags(function(data) {
		window.tags = _.indexBy(data, 'id');
	});


	// Declare map onclick behaviour
	google.maps.event.addListener(gmap, 'click', function(event) {
		placeNewMarker(event.latLng);
	});

	// Define inputs behaviour
	$('#showLocation').on('click', function() {
		if( window.sw.latitudeInput.val() === '' || window.sw.longitudeInput.val() === '')
			return false;

		var location = new google.maps.LatLng( window.sw.latitudeInput.val(), window.sw.longitudeInput.val() );

		var startLat = gmap.getCenter().lat(),
			startLng = gmap.getCenter().lng(),
			endLat   = location.lat(),
			endLng   = location.lng();
		// var distance = Math.sqrt( (startLat - endLat)^2 + (startLng - endLng)^2 ); // degrees
		var distance = ( Math.acos( Math.sin( startLat * Math.PI / 180 ) * Math.sin( endLat * Math.PI / 180 ) + Math.cos( startLat * Math.PI / 180 ) * Math.cos( endLat * Math.PI / 180 ) * Math.cos( (startLng - endLng) * Math.PI / 180) ) * 180 / Math.PI ) * 60 * 1.1515;
		var animTime = Math.min(1 * distance * (gmap.getZoom() + 1)^1.5, 1500); // ms (max 1.5s)

		placeNewMarker(location);

		var startTime = Date.now(); // in ms
		// TODO animate panning
		(function animloop(){

			// Calculate
			var elapsed = (Date.now() - startTime) / animTime; // percent

			if(elapsed < 1)
				requestAnimationFrame(animloop);

			var animation = easeOutAnimation;
			var lat = animation(startLat, endLat, elapsed),
				lng = animation(startLng, endLng, elapsed);

			gmap.setCenter( new google.maps.LatLng(lat, lng) );
		})();

		// Render the marker again (sometimes it didn't show after scrolling outside the viewwindow)
		// placeNewMarker(location);
	});

	$('#createLocation').on('click', function() {
		if( window.sw.latitudeInput.val() === '' || window.sw.longitudeInput.val() === '')
			return false;

		var markerObject = {
			latitude:  window.sw.latitudeInput.val(),
			longitude: window.sw.longitudeInput.val()
		};
		showModalWindow(markerObject);
	});

	// Define modal window button clicks
	$('#modalWindows').on('click', '.attach-location', function(event) {

		event.preventDefault();

		// Disable button and display loader
		$(event.target).prop('disabled', true).after('<div id="attach-location-loader" class="loader"></div>');

		var modal = $(event.target).closest('.reveal-modal');
		var markerObject = modal.data('markerObject');

		var params = {
			location_id: markerObject.id,
			_token:      window.token
		};

		Place.attach(params, function success(data) {

			// Communitcate success to user
			$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#attach-location-loader').remove();

			// Update markerObject
			markerObject.attached = true;
			markerObject.setIcon('http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1');

			// Shift location to new collection
			window.sw.attachedLocations[markerObject.id] = window.sw.locations[markerObject.id];
			// Delete from old collection
			window.sw.locations = _.omit(window.sw.locations, markerObject.id);

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		});
	});

	$('#modalWindows').on('click', '.detach-location', function(event) {

		event.preventDefault();

		// Disable button and display loader
		$(event.target).prop('disabled', true).after('<div id="detach-location-loader" class="loader"></div>');

		var modal = $(event.target).closest('.reveal-modal');
		var markerObject = modal.data('markerObject');

		var params = {
			location_id: markerObject.id,
			_token:      window.token
		};

		Place.detach(params, function success(data) {

			// Communitcate success to user
			$(event.target).attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#detach-location-loader').remove();

			// Update markerObject
			markerObject.attached = false;
			markerObject.setIcon('http://mt.googleapis.com/vt/icon?color=ff004C13&name=icons/spotlight/spotlight-waypoint-blue.png&scale=1');

			// Shift location to new collection
			window.sw.locations[markerObject.id] = window.sw.attachedLocations[markerObject.id];
			// Delete from old collection
			window.sw.attachedLocations = _.omit(window.sw.attachedLocations, markerObject.id);

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.errors[0]);
		});
	});

	$('#modalWindows').on('submit', '#create-location-form', function(event) {

		event.preventDefault();

		// Disable button and display loader
		$(event.target).find('[type=submit]').prop('disabled', true).after('<div id="add-location-loader" class="loader"></div>');

		var modal = $(event.target).closest('.reveal-modal');

		var params = modal.find('form').serializeObject();
		params._token = window.token;

		Place.create(params, function success(data) {

			// Communitcate success to user
			$(event.target).find('[type=submit]').attr('value', 'Success!').css('background-color', '#2ECC40');
			$('#add-location-loader').remove();

			var location = $.extend(true, {}, params);
			location.id = data.id;
			delete location._token;

			location.tags = [];
			_.each(params.tags, function(tag_id) {
				location.tags.push( window.tags[tag_id] );
			});

			// Create marker
			var markerOptions = {
				id:        location.id,
				attached:  true,
				position:  new google.maps.LatLng( location.latitude, location.longitude ),
				map:       gmap,
				title:     location.name,
				icon:      'http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1'
			};
			var marker = new google.maps.Marker(markerOptions);
			google.maps.event.addListener(marker, 'click', existingMarkerClick);
			window.sw.markers.push(marker);
			window.sw.attachedLocations[location.id] = location;

			// Remove and delete newMarker
			google.maps.event.clearInstanceListeners(window.sw.newMarker);
			window.sw.newMarker.setMap(null);
			delete window.sw.newMarker;

			// Reset coordinate inputs
			window.sw.latitudeInput.val('');
			window.sw.longitudeInput.val('');

			// Close modal window
			$('#modalWindows .close-reveal-modal').click();

			pageMssg(data.status, true);
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			var html = '';

			_.each(data.errors, function(error) {
				html += '<div class="alert alert-danger">' + error + '</div>';
			});

			$('#create-location-form').find('.alert').remove();
			$('#create-location-form').prepend(html);

			$(event.target).find('[type=submit]').prop('disabled', false);
			$(event.target).find('.loader').remove();
		});
	});
}

function linearAnimation(start, end, percentage) {
	return start + (end - start) * percentage;
}

function easeInOutAnimation(start, end, percentage) {
	var c = end - start;
	var t = percentage * 2;
	var b = start;

	// From http://www.gizma.com/easing/#quad3
	// t /= d/2;
	if (t < 1) return c/2*t*t + b;
	t--;
	return -c/2 * (t*(t-2) - 1) + b;
}

function easeOutAnimation(start, end, percentage) {
	var c = end - start;
	var t = percentage;
	var b = start;

	// From http://www.gizma.com/easing/#quad2
	// t /= d;
	return -c * t*(t-2) + b;
}

function loadLocationsInView() {
	var bounds = window.gmap.getBounds();
	var north  = bounds.getNorthEast().lat(),
	    west   = bounds.getSouthWest().lng(),
	    south  = bounds.getSouthWest().lat(),
	    east   = bounds.getNorthEast().lng();

	// var area   = [north, west, south, east];
	// TODO make the location creating relative, meaning render only locations in view and which are not on the map yet
	var area = [-90, -180, 90, 180];
	var params = {
		'area': area
	};
	Place.inside(params, function success(data) {
		window.sw.locations = _.indexBy(data, 'id');

		// Show feedback to user
		$('#legend-available-locations-loader').hide();
		$('#legend-available-locations-icon').show();

		promises.loadedLocations.resolve();
	});
}
function loadAttachedLocations() {

	Place.attached(function success(data) {
		window.sw.attachedLocations = _.indexBy(data, 'id');

		// Show feedback to user
		$('#legend-your-locations-loader').hide();
		$('#legend-your-locations-icon').show();

		promises.loadedAttachedLocations.resolve();
	});
}

function removeAttachedFromLocations() {
	// Remove all attached locations from the general locations collection
	window.sw.locations = _.omit(window.sw.locations, _.keys(window.sw.attachedLocations));
}

function renderHomeLocation() {
	var markerOptions = {
		position:  new google.maps.LatLng(window.company.latitude, window.company.longitude),
		map:       gmap,
		title:     'Home',
		icon:      'http://mt.googleapis.com/vt/icon/name=icons/spotlight/home_L_8x.png&scale=1'
	};
	new google.maps.Marker(markerOptions);
}

function renderLocations() {
	var markerArray = [];
	_.each(window.sw.locations, function(location) {
		var markerOptions = {
			id:        location.id,
			attached:  false,
			position:  new google.maps.LatLng( location.latitude, location.longitude ),
			// map:       gmap,
			title:     location.name,
			animation: google.maps.Animation.DROP,
			icon:      'http://mt.googleapis.com/vt/icon?color=ff004C13&name=icons/spotlight/spotlight-waypoint-blue.png&scale=1'
		};
		markerArray.push( new google.maps.Marker(markerOptions) );
	});

	var i = 0;
	_.each(markerArray, function(marker) {
		setTimeout(function() {
			marker.setMap(gmap);
		}, i * 150);
		i++;
	});

	window.sw.markers = window.sw.markers.concat(markerArray);
}

function renderAttachedLocations() {
	var markerArray = [];
	_.each(window.sw.attachedLocations, function(location) {
		var markerOptions = {
			id:        location.id,
			attached:  true,
			position:  new google.maps.LatLng( location.latitude, location.longitude ),
			// map:       gmap,
			title:     location.name,
			animation: google.maps.Animation.DROP,
			icon:      'http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1'
		};
		markerArray.push( new google.maps.Marker(markerOptions) );
	});

	var i = 0;
	_.each(markerArray, function(marker) {
		setTimeout(function() {
			marker.setMap(gmap);
		}, i * 150); // Must possibly be reduced to 100, depending on how many locations an operator actually uses
		i++;
	});

	window.sw.markers = window.sw.markers.concat(markerArray);
}

function placeNewMarker(location) {

	if(window.sw.newMarker) {
		// Remove old marker
		google.maps.event.clearInstanceListeners(window.sw.newMarker);
		window.sw.newMarker.setMap(null);
		delete window.sw.newMarker;
	}

	// Create new marker
	var markerOptions = {
		position:  location,
		draggable: true,
		map:       gmap,
		icon:      'http://mt.googleapis.com/vt/icon?psize=30&font=fonts/arialuni_t.ttf&color=ff304C13&name=icons/spotlight/spotlight-waypoint-a.png&ax=43&ay=48&text=%E2%80%A2&scale=1'
	};
	window.sw.newMarker = new google.maps.Marker(markerOptions);

	google.maps.event.addListener(window.sw.newMarker, 'drag', function(event) {
		updateLatLngInputs(event.latLng);
	});

	google.maps.event.addListener(window.sw.newMarker, 'click', function(event) {
		showModalWindow({
			latitude: event.latLng.lat(),
			longitude: event.latLng.lng()
		});
	});

	// Update inputs with new coordinates
	updateLatLngInputs(location);
}

function updateLatLngInputs(location) { // Sync changes of the marker's location to the input fields

	var latitude = Math.round(location.lat() * 1000000) / 1000000;
	var longitude = Math.round(location.lng() * 1000000) / 1000000;

	window.sw.latitudeInput.val( latitude );
	window.sw.longitudeInput.val( longitude );
}

function existingMarkerClick() {
	var markerObject = this;

	// Show modal window
	showModalWindow(markerObject);
}

function showModalWindow(markerObject) {

	if(markerObject.id) {
		// Create the modal window from location-template
		if(!window.sw.locationTemplate) window.sw.locationTemplate = Handlebars.compile( $("#location-template").html() );
		var location;
		if(markerObject.attached)
			location = window.sw.attachedLocations[markerObject.id];
		else
			location = window.sw.locations[markerObject.id];

		location.attached = markerObject.attached;

		$('#modalWindows')
		.append( window.sw.locationTemplate(location) ) // Create the modal
		.children('#modal-' + markerObject.id)          // Directly find it and use it
		.data('markerObject', markerObject)             // Assign the eventObject to the modal DOM element
		.reveal({                                       // Open modal window | Options:
			animation: 'fadeAndPop',                    // fade, fadeAndPop, none
			animationSpeed: 300,                        // how fast animtions are
			closeOnBackgroundClick: true,               // if you click background will modal close?
			dismissModalClass: 'close-modal',           // the class of a button or element that will close an open modal
			'markerObject': markerObject,               // Submit by reference to later get it as this.eventObject for removal
			onFinishModal: function() {
				$('#modal-' + this.markerObject.id).remove();
			}
		});
	}
	else {
		// Create the modal window from create-location-template
		if(!window.sw.newLocationTemplate) window.sw.newLocationTemplate = Handlebars.compile( $("#new-location-template").html() );

		markerObject.latitude       = Math.round(markerObject.latitude * 1000000) / 1000000;
		markerObject.longitude      = Math.round(markerObject.longitude * 1000000) / 1000000;
		markerObject.available_tags = window.tags;

		$('#modalWindows')
		.append( window.sw.newLocationTemplate(markerObject) ) // Create the modal
		.children('#modal-new')                                // Directly find it and use it
		.reveal({                                              // Open modal window | Options:
			animation: 'fadeAndPop',                           // fade, fadeAndPop, none
			animationSpeed: 300,                               // how fast animtions are
			closeOnBackgroundClick: true,                      // if you click background will modal close?
			dismissModalClass: 'close-modal',                  // the class of a button or element that will close an open modal
			onFinishModal: function() {
				$('#modal-new').remove();
			},
			onOpenedModal: function() {
				$('#new-location-name').focus();
			}
		});

		CKEDITOR.replace( 'description' );
	}
}

function changeParent(self) {
	$(self).parent().toggleClass('checked');
}
