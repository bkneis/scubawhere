var boatsList;
var boatsForm;
var boatroomsList;
var boatroomsForm;
var roomTypes;
var addRoom;

Handlebars.registerHelper('firstID', function(obj){
	return obj[0].id;
});

Handlebars.registerPartial('boatroom_show', $('#show-room-template').html());

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
		if( $(event.target).is('strong') ) event.target = event.target.parentNode;
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

	$("#boats-form-container").on('click', '#add-room', function(event){
		event.preventDefault();
		$('form').data('hasChanged', true);
		$('#room-types').append( addRoom({boatrooms : roomTypes}) );
	});

	$("#boats-form-container").on('click', '.remove-room', function(event){
		event.preventDefault();
		$('form').data('hasChanged', true);
		$(event.target).parent().remove();
	});

	$("#boats-form-container").on('click', '.remove-boat', function(event){
		event.preventDefault();
		var check = confirm('Do you really want to remove this boat?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Boat.delete({
				'id'    : $('#update-boats-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			},
			function success(data){
				pageMssg(data.status, true);
				renderBoatList();
				renderEditForm();
			},
			function error(xhr){
				data = JSON.parse(xhr.responseText);
				//console.log(data);

				errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-boats-form').prepend(errorsHTML);
				$('.remove-boat').prop('disabled', false);
				$('.loader').remove();
			});
		}
	});

$("#boats-form-container").on('click', '.remove-boatroom', function(event){
		event.preventDefault();
		var check = confirm('Do you really want to remove this room type?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Boatroom.delete({
				'id'    : $('#update-boatrooms-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			},
			function success(data){
				pageMssg(data.status, true);
				renderBoatList();
				renderRoomEditForm();
			},
			function error(xhr){
				data = JSON.parse(xhr.responseText);
				//console.log(data);

				errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-boatrooms-form').prepend(errorsHTML)
				$('.remove-boatroom').prop('disabled', false);
				$('.loader').remove();
			});
		}
	});

	$("#boats-form-container").on('submit', '#add-boats-form', function(event){
		event.preventDefault();
		$('#add-boat').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
		Boat.create($('#add-boats-form').serialize(), function success(data){
			pageMssg(data.status, true);
			//console.log(data);

			$('form').data('hasChanged', false);

			renderBoatList(function() {renderEditForm(data.id);	})

		},
		function error(xhr){
			data = JSON.parse(xhr.responseText);
			//console.log(data);

			errorsHTML = Handlebars.compile( $("#errors-template").html() );
			errorsHTML = errorsHTML(data);

			// Render error messages
			$('.errors').remove();
			$('#add-boats-form').prepend(errorsHTML)
			$('#add-boat').prop('disabled', false);
			$('.loader').remove();
		});
	});

	$("#boats-form-container").on('submit', '#add-boatrooms-form', function(event){
		event.preventDefault();
		$('#add-boatroom').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
		Boatroom.create($('#add-boatrooms-form').serialize(), function success(data){
			pageMssg(data.status, true);
			//console.log(data);

			$('form').data('hasChanged', false);

			renderBoatList(function() {
				renderRoomEditForm(data.id);
			});
		},
		function error(xhr){
			data = JSON.parse(xhr.responseText);
			//console.log(data);

			errorsHTML = Handlebars.compile( $("#errors-template").html() );
			errorsHTML = errorsHTML(data);

			// Render error messages
			$('.errors').remove();
			$('#add-boatrooms-form').prepend(errorsHTML)
			$('#add-boatroom').prop('disabled', false);
			$('.loader').remove();
		});
	});

	$("#boats-form-container").on('submit', '#update-boats-form', function(event){
		event.preventDefault();
		$('#update-boat').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
		Boat.update($('#update-boats-form').serialize(), function success(data){
			pageMssg(data.status, true);
			//console.log(data);

			$('form').data('hasChanged', false);

			/*renderBoatList(function() {
				renderEditForm($('#update-boats-form input[name=id]').val())
			});*/
			renderBoatList();
			$('#update-boat').prop('disabled', false);
			$('.loader').remove();
		},
		function error(xhr){
			data = JSON.parse(xhr.responseText);
			//console.log(data);

			errorsHTML = Handlebars.compile( $("#errors-template").html() );
			errorsHTML = errorsHTML(data);

			// Render error messages
			$('.errors').remove();
			$('#update-boats-form').prepend(errorsHTML)
			$('#update-boat').prop('disabled', false);
			$('.loader').remove();
		});
	});

	$("#boats-form-container").on('submit', '#update-boatrooms-form', function(event){
		event.preventDefault();
		$('#update-boatroom').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
		Boatroom.update($('#update-boatrooms-form').serialize(), function success(data){
			pageMssg(data.status, true);
			//console.log(data);

			$('form').data('hasChanged', false);

			/*renderBoatList(function() {
				renderRoomEditForm($('#update-boatrooms-form input[name=id]').val());
			});*/
			renderBoatList();
			$('.loader').remove();
			$('#update-boatroom').prop('disabled', false);
		},
		function error(xhr){
			data = JSON.parse(xhr.responseText);
			//console.log(data);

			errorsHTML = Handlebars.compile( $("#errors-template").html() );
			errorsHTML = errorsHTML(data);

			// Render error messages
			$('.errors').remove();
			$('#update-boatrooms-form').prepend(errorsHTML)
			$('#update-boatroom').prop('disabled', false);
			$('.loader').remove();
		});
	});

});

function renderBoatList(callback) {

	$('#boats-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');
	$('#boatrooms-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Boat.getAll(function success(data) {
		window.boats = _.indexBy(data, 'id');
		//console.log(data);
		$('#boats-list').remove();
		$('#boats-list-container .loader').remove();
		$("#boats-list-container").append( boatsList({boats : data}) );
		if(typeof callback === 'function') callback();
	});

	Boatroom.getAll(function success(data) {
		window.boatrooms = _.indexBy(data, 'id');
		roomTypes = data;
		$('#boatrooms-list').remove();
		$('#boatrooms-list-container .loader').remove();
		$("#boatrooms-list-container").append( boatroomsList({boatrooms : data}));
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
