
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1)
{
	window.location.href = '#dashboard';
}

var classForm;
var classList;

Handlebars.registerHelper('readable', function(duration) {
	return readableDuration(duration);
});

$(function () {

	// Handlebars Prep
	classList = Handlebars.compile( $("#class-list-template").html() );
	classForm = Handlebars.compile( $("#class-form-template").html() );

	renderClassList();
	renderEditForm();

	TourManager.getClassesTour();

	$('#class-list-container').on('click', 'li', function(event) {

		if( $(event.target).is('strong') ) event.target = event.target.parentNode;
		renderEditForm( event.target.getAttribute('data-id') );
	});

	$("#class-list-container").on('click', '#change-to-add-class', function(event){

		event.preventDefault();
		renderEditForm();
	});

	$("#class-form-container").on('submit', '#add-class-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		//$('#add-ticket').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Class.create( $('#add-class-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderClassList(function() {
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
				$('#add-class-form').prepend(errorsHTML);
				$('#add-class').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-class').prop('disabled', false);
			//$('#add-class-form').find('#save-loader').remove();
		});
	});

	$("#class-form-container").on('submit', '#update-class-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		//$('#add-ticket').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		Class.update( $('#update-class-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			console.log(data);

			$('form').data('hasChanged', false);

			renderClassList(function() {
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
				$('#add-class-form').prepend(errorsHTML);
				$('#add-class').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-class').prop('disabled', false);
			//$('#add-class-form').find('#save-loader').remove();
		});
	});

	$('#class-form-container').on('click', '.remove-class', function(event) {
    	event.preventDefault();
        var deleteable = $('#update-class-form input[name=deleteable]').val();
		var check;
        if(deleteable === "true")
            check = confirm('Do you really want to remove this class?');
        else
            check = confirm('If you delete this class then it will be removed from all courses associated with it, are you sure you wish to contiue?');

		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Class.delete({
				'id'    : $('#update-class-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderClassList();

				renderEditForm();
			}, function error(xhr){

				var data = JSON.parse(xhr.responseText);
				
                pageMssg(data.errors[0], 'danger', true);
                //pageMssg('Oops, something wasn\'t quite right');

				$('.remove-class').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

});

function renderClassList(callback) {

	//$('#class-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Class.getAll(function success(data) {

		window.classes = _.indexBy(data, 'id');
		$('#class-list').remove();
		//$('#class-list-container .loader').remove();

		$("#class-list-container").append( classList({classes : data}) );

		if(typeof callback === 'function') callback();
	});
}

function renderEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question) {
			return false;
		}
	}

	var training;

	if(id) {
		training = window.classes[id];
		training.task         = 'update';
		training.update       = true;
	}
	else {
		training = {
			task: 'add',
			update: false,
			hasBoats: false,
			hasBoatrooms: false,
			base_prices: [ window.sw.default_first_base_price ],
		};
	}

	$('#class-form-container').empty().append( classForm(training) );

	if(!id)
		$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}

function readableDuration(hours) {
	var duration = moment.duration(parseInt(hours), 'hours');
	return /*(duration.days() > 0 ?*/ duration.days() + ' d ' /*: '')*/ + duration.hours() + ' h';
}

function clearForm() {

	var training;

	training = {
		task: 'add',
		update: false
	};

	$('#class-form-container').empty().append( classForm(training) );

	$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}
