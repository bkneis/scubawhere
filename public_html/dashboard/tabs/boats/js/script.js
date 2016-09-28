
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

var boatsList,
    boatsForm,
    boatroomsList,
    boatroomsForm,
    roomTypes,
    addRoom;

Handlebars.registerHelper('firstID', function(obj) {
    return obj[0].id;
});

Handlebars.registerPartial('boatroom_show', $('#show-room-template').html());

$(function() {

    // Handlebars Prep
    boatsList = Handlebars.compile($("#boat-list-template").html());
    boatroomsList = Handlebars.compile($("#boatroom-list-template").html());
    renderBoatList();

    boatsForm = Handlebars.compile($("#boat-form-template").html());
    renderEditForm();
    TourManager.getBoatsTour();
    boatroomsForm = Handlebars.compile($("#boatroom-form-template").html());
    addRoom = Handlebars.compile($('#add-room-template').html());

    $('#boat-list-container').on('click', 'li', function(event) {
        if ($(event.target).is('strong')) event.target = event.target.parentNode;
        renderEditForm(event.target.getAttribute('data-id'));
    });

    $('#boatroom-list-container').on('click', 'li', function(event) {
        if ($(event.target).is('strong')) event.target = event.target.parentNode;
        renderRoomEditForm(event.target.getAttribute('data-id'));
    });

    $("#boat-list-container").on('click', '#change-to-add-boat', function(event) {
        event.preventDefault();
        renderEditForm();
    });

    $("#wrapper").on('click', '.change-to-add-boatroom', function(event) {
        event.preventDefault();
        renderRoomEditForm();
    });

    $("#boat-form-container").on('click', '#add-room', function(event) {
        event.preventDefault();
        if (!$.isEmptyObject(roomTypes)) {
            $('form').data('hasChanged', true);
            $('#room-types').append(addRoom({
                boatrooms: roomTypes
            }));
        }
		if(roomTypes.length === 0)
		{
        	$(this).before('<div class="alert alert-danger clearfix"><i class="fa fa-exclamation-triangle fa-3x fa-fw pull-left"></i><p class="pull-left"><strong>No cabins available!</strong><br>You can create <a href="#" class="change-to-add-boatroom">Cabins</a> to add.</p></div>');
		}
    });

    $("#boat-form-container").on('click', '.remove-room', function(event) {
        event.preventDefault();
        $('form').data('hasChanged', true);
        $(event.target).parent().remove();
    });

    $("#boat-form-container").on('click', '.remove-boat', function(event) {
        event.preventDefault();
        //var check = confirm('Do you really want to remove this boat?');
        var check = confirm('WARNING! Deleting this boat will mean it is removed from all associated sessions and/or quotes. Are you sure you wish to continue?');
		
        if (check) {
            // Show loading indicator
            $(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

            Boat.delete({
                    'id': $('#update-boat-form input[name=id]').val(),
                    '_token': $('[name=_token]').val()
                },
                function success(data) {
                    pageMssg(data.status, true);
                    renderBoatList();
                    renderEditForm();
                },
                function error(xhr) {
                    var data = JSON.parse(xhr.responseText);
                    //console.log(data);

                    var errorsHTML = Handlebars.compile($("#errors-template").html());
                    errorsHTML = errorsHTML(data);

                    // Render error messages
                    $('.errors').remove();
                    $('#update-boat-form').prepend(errorsHTML);
                    $('.remove-boat').prop('disabled', false);
                    $('.loader').remove();
                });
        }
    });

    $("#boat-form-container").on('click', '.remove-boatroom', function(event) {
        event.preventDefault();
        var check = confirm('Do you really want to remove this room type?');
        if (check) {
            // Show loading indicator
            $(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

            Boatroom.delete({
                    'id': $('#update-boatroom-form input[name=id]').val(),
                    '_token': $('[name=_token]').val()
                },
                function success(data) {
                    pageMssg(data.status, true);
                    renderBoatList();
                    renderRoomEditForm();
                },
                function error(xhr) {
                    var data = JSON.parse(xhr.responseText);
                    //console.log(data);

                    var errorsHTML = Handlebars.compile($("#errors-template").html());
                    errorsHTML = errorsHTML(data);

                    // Render error messages
                    $('.errors').remove();
                    $('#update-boatroom-form').prepend(errorsHTML);
                    $('.remove-boatroom').prop('disabled', false);
                    $('.loader').remove();
                });
        }
    });

    $("#boat-form-container").on('submit', '#add-boat-form', function(event) {
        event.preventDefault();
        $('#add-boat').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
        Boat.create($('#add-boat-form').serialize(), function success(data) {
                pageMssg(data.status, true);
                //console.log(data);

                $('form').data('hasChanged', false);

                renderBoatList(function() {
                    renderEditForm(data.id);
                });

            },
            function error(xhr) {
                var data = JSON.parse(xhr.responseText);
                //console.log(data);

                var errorsHTML = Handlebars.compile($("#errors-template").html());
                errorsHTML = errorsHTML(data);

                // Render error messages
                $('.errors').remove();
                $('#add-boat-form').prepend(errorsHTML);
                $('#add-boat').prop('disabled', false);
                $('.loader').remove();
            });
    });

    $("#boat-form-container").on('submit', '#add-boatroom-form', function(event) {
        event.preventDefault();
        $('#add-boatroom').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
        Boatroom.create($('#add-boatroom-form').serialize(), function success(data) {
                pageMssg(data.status, true);
                //console.log(data);

                $('form').data('hasChanged', false);

                renderBoatList(function() {
                    renderRoomEditForm(data.id);
                });
            },
            function error(xhr) {
                var data = JSON.parse(xhr.responseText);
                //console.log(data);

                var errorsHTML = Handlebars.compile($("#errors-template").html());
                errorsHTML = errorsHTML(data);

                // Render error messages
                $('.errors').remove();
                $('#add-boatroom-form').prepend(errorsHTML);
                $('#add-boatroom').prop('disabled', false);
                $('.loader').remove();
            });
    });

    $("#boat-form-container").on('submit', '#update-boat-form', function(event) {
        event.preventDefault();
        $('#update-boat').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
        Boat.update($('#update-boat-form').serialize(), function success(data) {
                pageMssg(data.status, true);
                //console.log(data);

                $('form').data('hasChanged', false);

                /*renderBoatList(function() {
                renderEditForm($('#update-boat-form input[name=id]').val())
            });*/
                renderBoatList();
                $('#update-boat').prop('disabled', false);
                $('.loader').remove();
            },
            function error(xhr) {
                var data = JSON.parse(xhr.responseText);
                //console.log(data);

                var errorsHTML = Handlebars.compile($("#errors-template").html());
                errorsHTML = errorsHTML(data);

                // Render error messages
                $('.errors').remove();
                $('#update-boat-form').prepend(errorsHTML);
                $('#update-boat').prop('disabled', false);
                $('.loader').remove();
            });
    });

    $("#boat-form-container").on('submit', '#update-boatroom-form', function(event) {
        event.preventDefault();
        $('#update-boatroom').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
        Boatroom.update($('#update-boatroom-form').serialize(), function success(data) {
			pageMssg(data.status, true);
			//console.log(data);

			$('form').data('hasChanged', false);

			/*renderBoatList(function() {
			renderRoomEditForm($('#update-boatroom-form input[name=id]').val());
		});*/
			renderBoatList();
			$('.loader').remove();
			$('#update-boatroom').prop('disabled', false);
		},
		function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			//console.log(data);

			var errorsHTML = Handlebars.compile($("#errors-template").html());
			errorsHTML = errorsHTML(data);

			// Render error messages
			$('.errors').remove();
			$('#update-boatroom-form').prepend(errorsHTML);
			$('#update-boatroom').prop('disabled', false);
			$('.loader').remove();
		});
    });

});

function renderBoatList(callback) {

    $('#boat-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');
    $('#boatroom-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	var gotBoats = $.Deferred();
	var gotBoatRooms = $.Deferred();

    Boat.getAll(function success(data) {
        window.boats = _.indexBy(data, 'id');
        console.log(data);
        $('#boat-list').remove();
        $('#boat-list-container .loader').remove();
        $("#boat-list-container").append(boatsList({
            boats: data
        }));
		gotBoats.resolve();
        //if (typeof callback === 'function') callback();
    });

    Boatroom.getAll(function success(data) {
        window.boatrooms = _.indexBy(data, 'id');
        roomTypes = data;
        $('#boatroom-list').remove();
        $('#boatroom-list-container .loader').remove();
        $("#boatroom-list-container").append(boatroomsList({
            boatrooms: data
        }));
		gotBoatRooms.resolve();
    });

	if(typeof callback === 'function')
	{
		$.when(gotBoats, gotBoatRooms).done(function() {
			callback();
		});
	}
}

function renderEditForm(id) {

    if (unsavedChanges()) {
        var question = confirm("ATTENTION: All unsaved changes are lost!");
        if (!question) return false;
    }

    var boat;

    if (id) {
        boat = window.boats[id];
        boat.task = 'update';
        boat.update = true;
    } else {
        boat = {
            task: 'add',
            update: false
        };
    }

    $('#boat-form-container').empty().append(boatsForm(boat));

    if (!id) $('input[name=name]').focus();

    CKEDITOR.replace('description');

    setToken('[name=_token]');

    // Set up change monitoring
    $('form').on('change', 'input, select, textarea', function() {
        $('form').data('hasChanged', true);
    });
}

function renderRoomEditForm(id) {

    if (unsavedChanges()) {
        var question = confirm("ATTENTION: All unsaved changes are lost!");
        if (!question) return false;
    }

    var boatRoom;

    if (id) {
        boatRoom = window.boatrooms[id];
        boatRoom.task = 'update';
        boatRoom.update = true;
    } else {
        boatRoom = {
            task: 'add',
            update: false
        };
    }

    $('#boat-form-container').empty().append(boatroomsForm(boatRoom));

    if (!id) $('input[name=name]').focus();

    CKEDITOR.replace('description');

    setToken('[name=_token]');

    // Set up change monitoring
    $('form').on('change', 'input, select, textarea', function() {
        $('form').data('hasChanged', true);
    });
}

function unsavedChanges() {
    return $('form').data('hasChanged');
}

function clearForm() {

    var boatRoom;

    boatRoom = {
        task: 'add',
        update: false
    };

    $('#boat-form-container').empty().append(boatroomsForm(boatRoom));

    $('input[name=name]').focus();

    CKEDITOR.replace('description');

    setToken('[name=_token]');

    // Set up change monitoring
    $('form').on('change', 'input, select, textarea', function() {
        $('form').data('hasChanged', true);
    });
}
