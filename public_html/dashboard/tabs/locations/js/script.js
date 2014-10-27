var gmap = {},
	promises = {};

window.sw.latitudeInput  = $('#newMarkerLatitude');
window.sw.longitudeInput = $('#newMarkerLongitude');

Handlebars.registerHelper('renderTags', function(tags) {
	// Do something
	return '';
});

getToken();

$(function() {
	console.log('Setting container height');
	// Set up height of map container to fill whole screen whithout showing scrollbars
	$('#map-container').css('height', window.innerHeight - 232);

	loadGoogleMaps();

});

function initialise() {
	console.log('Initialising started');
	var mapOptions = {
		zoom:   8,
		center: new google.maps.LatLng(company.latitude, company.longitude)
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
				google.maps.event.addListener(marker, 'click', function(event) {
					var markerObject = this;

					// Show modal window
					showModalWindow(markerObject);
				});
			});
		});
	});


	// Declare map onclick behaviour
	google.maps.event.addListener(gmap, 'click', function(event) {
		placeNewMarker(event.latLng);
	});

	// Define inputs behaviour
	$('#showLocation').on('click', function(event) {
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

	// Define modal window button clicks
	$('#modalWindows').on('click', '.attach-location', function(event) {

		event.preventDefault();

		// Disable button and display loader
		$(event.target).prop('disabled', true).after('<div id="attach-location-loader" class="loader"></div>');

		var modal = $(event.target).closest('.reveal-modal');
		var markerObject = modal.data('markerObject');

		var params = {
			location_id: markerObject.id,
			_token:      window._token || window.token
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
			_token:      window._token || window.token
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

function loadLocationsInView(promise) {
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

		promises.loadedLocations.resolve();
	});
}
function loadAttachedLocations(promise) {

	Place.attached(function success(data) {
		window.sw.attachedLocations = _.indexBy(data, 'id');

		promises.loadedAttachedLocations.resolve();
	});
}

function removeAttachedFromLocations() {
	// Remove all attached locations from the general locations collection
	window.sw.locations = _.omit(window.sw.locations, _.keys(window.sw.attachedLocations));
}

function renderHomeLocation() {
	var markerOptions = {
		position:  new google.maps.LatLng( company.latitude, company.longitude ),
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
		}, i * 400);
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
		}, i * 400);
		i++;
	});

	window.sw.markers = window.sw.markers.concat(markerArray);
}

function placeNewMarker(location) {

	if(window.sw.newMarker)
		window.sw.newMarker.setPosition(location);
	else {
		// Create new marker
		var markerOptions = {
			position:  location,
			draggable: true,
			map:       gmap,
			icon:      'http://mt.googleapis.com/vt/icon?psize=30&font=fonts/arialuni_t.ttf&color=ff304C13&name=icons/spotlight/spotlight-waypoint-a.png&ax=43&ay=48&text=%E2%80%A2&scale=1'
		}
		window.sw.newMarker = new google.maps.Marker(markerOptions);

		google.maps.event.addListener(window.sw.newMarker, 'drag', function(event) {
			updateLatLngInputs(event.latLng);
		});

		google.maps.event.addListener(window.sw.newMarker, 'click', function(event) {
			//
		});
	}

	// Update inputs with new coordinates
	updateLatLngInputs(location);
}

function updateLatLngInputs(location) {
	window.sw.latitudeInput.val( location.lat() );
	window.sw.longitudeInput.val( location.lng() );
}

function showModalWindow(markerObject) {

	// Create the modal window from location-template
	if(!window.sw.locationTemplate) window.sw.locationTemplate = Handlebars.compile( $("#location-template").html() );

	if(markerObject.id) {
		if(markerObject.attached)
			var location = window.sw.attachedLocations[markerObject.id];
		else
			var location = window.sw.locations[markerObject.id];

		location.attached = markerObject.attached;

		$('#modalWindows')
		.append( window.sw.locationTemplate(location) )        // Create the modal
		.children('#modal-' + markerObject.id)        // Directly find it and use it
		.data('markerObject', markerObject)           // Assign the eventObject to the modal DOM element
		.reveal({                                     // Open modal window | Options:
			animation: 'fadeAndPop',                  // fade, fadeAndPop, none
			animationSpeed: 300,                      // how fast animtions are
			closeOnBackgroundClick: true,             // if you click background will modal close?
			dismissModalClass: 'close-modal',         // the class of a button or element that will close an open modal
			'markerObject': markerObject,             // Submit by reference to later get it as this.eventObject for removal
			onFinishModal: function() {
				$('#modal-' + this.markerObject.id).remove();
			}
		});
	}
	else
		var location = {};
}
