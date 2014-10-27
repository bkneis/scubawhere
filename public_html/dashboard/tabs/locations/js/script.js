var gmap = promises = {};

$(function() {
	console.log('Setting container height');
	// Set up height of map container to fill whole screen whithout showing scrollbars
	$('#map-container').css('height', window.innerHeight - 232);

	loadGoogleMaps();

});

function initialise() {
	console.log('Initialising started');
	var mapOptions = {
		zoom:   5,
		center: new google.maps.LatLng(company.latitude, company.longitude)
		// center: new google.maps.LatLng(50.582847, 5.96848) // Somewhere around Aachen in Germany
	};

	window.gmap = new google.maps.Map(document.getElementById('map'), mapOptions);

	// Load locations

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

			// Render all locations
			renderHomeLocation();
			renderLocations();
			renderAttachedLocations();
		});
	});
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
		window.locations = _.indexBy(data, 'id');

		promises.loadedLocations.resolve();
	});
}
function loadAttachedLocations(promise) {

	Place.attached(function success(data) {
		window.attachedLocations = _.indexBy(data, 'id');

		promises.loadedAttachedLocations.resolve();
	});
}

function removeAttachedFromLocations() {
	// Remove all attached locations from the general locations collection
	window.locations = _.omit(window.locations, _.keys(window.attachedLocations));
}

function renderHomeLocation() {
	var markerOptions = {
		position:  new google.maps.LatLng( company.latitude, company.longitude ),
		// map:       gmap,
		title:     'Home',
		icon:      'http://mt.googleapis.com/vt/icon/name=icons/spotlight/home_L_8x.png&scale=1'
	};
	new google.maps.Marker(markerOptions);
}

function renderLocations() {
	var markerArray = [];
	_.each(window.locations, function(location) {
		var markerOptions = {
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
}

function renderAttachedLocations() {
	console.log(window.attachedLocations);
	var markerArray = [];
	_.each(window.attachedLocations, function(location) {
		var markerOptions = {
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
}
