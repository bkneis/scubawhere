var boatsList;
var boatsForm;
var boatroomsList;
var boatroomsForm;
var roomTypes;
var addRoom;

$(function (){

	// Handlebars Prep
	boatsList = Handlebars.compile($("#boats-list-template").html());
	boatroomsList = Handlebars.compile($("#boatrooms-list-template").html());
	renderBoatList();

	boatsForm = Handlebars.compile( $("#boats-form-template").html() );
	renderEditForm();

	boatroomsForm = Handlebars.compile( $("#boatrooms-form-template").html() );
	addRoom = Handlebars.compile( $('#add-room-template').html() );

	$('#boats-list-container').on('click', 'li', function(event) {
		if( $(event.target).is('strong') ) event.target = event.target.parentNode;
		renderEditForm( event.target.getAttribute('data-id') );
	});

	$('#boatrooms-list-container').on('click', 'li', function(event) {
		renderRoomEditForm(event.target.getAttribute('data-id'));
	});

	$("#boats-list-container").on('click', '#change-to-add-boat', function(event){
		event.preventDefault();
		renderEditForm();
	});

	$("#boatrooms-list-container").on('click', '#change-to-add-boatroom', function(event){
		event.preventDefault();
		renderRoomEditForm();
	});

	$("#rooms").on('click', '#add-room', function(event){
		event.preventDefault();
		$('form').data('hasChanged', true);
		$('#room-types').append( addRoom({boatrooms : roomTypes}) );
	});

	$("#rooms").on('click', '#remove-room', function(event){
		event.preventDefault();
		$(event.target).parent().remove();
	});

	$("boats-form-container").on('submit', '#add-boats-form', function(event){
		event.preventDefault();
	});

});

function renderBoatList(callback) {

	$('#boats-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');
	$('#boatrooms-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Boat.getAllBoats(function success(data) {
		window.boats = _.indexBy(data.boats, 'id');
		window.boatrooms = _.indexBy(data.accommodations, 'id');
		roomTypes = data.accommodations;
		console.log(data);
		$('#boats-list').remove();
		$('#boatrooms-list').remove();
		$('#boats-list-container .loader').remove();
		$('#boatrooms-list-container .loader').remove();
		$("#boats-list-container").append( boatsList({boats : data.boats}) );
		$("#boatrooms-list-container").append( boatroomsList({boatrooms : data.accommodations}));
		if(typeof callback === 'function') callback();
	});
}

function renderEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) return false;
	}

	var boat;

	if(id) {
		boat = window.boats[id];
		boat.task     = 'update';
		boat.update   = true;
	}
	else {
		boat = {
			task: 'add',
			update: false
		};
	}

	$('#boats-form-container').empty().append( boatsForm(boat) );

	if(!id) $('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function(event) {
		$('form').data('hasChanged', true);
	});
}

function renderRoomEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) return false;
	}

	var boatRoom;

	if(id) {
		boatRoom = window.boatrooms[id];
		boatRoom.task     = 'update';
		boatRoom.update   = true;
	}
	else {
		boatRoom = {
			task: 'add',
			update: false
		};
	}

	$('#boats-form-container').empty().append( boatroomsForm(boatRoom) );

	if(!id) $('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function(event) {
		$('form').data('hasChanged', true);
	});
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