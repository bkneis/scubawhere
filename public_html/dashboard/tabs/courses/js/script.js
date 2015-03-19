var courseForm;
var courseList;
window.classes;

Handlebars.registerHelper('readable', function(duration) {
	return readableDuration(duration);
});

Handlebars.registerPartial('classes_show', $('#class-select-template').html());

$(function () {

	// Handlebars Prep
	courseList = Handlebars.compile( $("#course-list-template").html() );
	courseForm = Handlebars.compile( $("#course-form-template").html() );

	renderCourseList();
	renderEditForm();

	Class.getAll(function success(data) {
		window.classes = data;
		console.log(data);
	});

	$('#course-list-container').on('click', 'li', function(event) {

		if( $(event.target).is('strong') ) event.target = event.target.parentNode;
		renderEditForm( event.target.getAttribute('data-id') );
	});

	$('#course-form-container').on('click', '#add-class', function(event) {
		event.preventDefault();
		var courseSelect = Handlebars.compile( $("#class-select-template").html() );
		$("#class-types").append(courseSelect({classes : window.classes}));
	});

	$("#course-form-container").on('click', '.remove-class', function(event){
		event.preventDefault();
		$('form').data('hasChanged', true);
		$(event.target).parent().remove();
	});

	$("#course-form-container").on('submit', '#add-course-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		//$('#add-ticket').prop('disabled', true).after('<div id="save-loader" course="loader"></div>');

		Course.create( $('#add-course-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			rendercourseList(function() {
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
				$('#add-course-form').prepend(errorsHTML);
				$('#add-course').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-course').prop('disabled', false);
			//$('#add-course-form').find('#save-loader').remove();
		});
	});

	$("#course-form-container").on('submit', '#update-course-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		//$('#add-ticket').prop('disabled', true).after('<div id="save-loader" course="loader"></div>');

		Course.update( $('#update-course-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			console.log(data);

			$('form').data('hasChanged', false);

			rendercourseList(function() {
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
				$('#add-course-form').prepend(errorsHTML);
				$('#add-course').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#add-course').prop('disabled', false);
			//$('#add-course-form').find('#save-loader').remove();
		});
	});

});

function renderCourseList(callback) {

	//$('#course-list-container').append('<div id="save-loader" course="loader" style="margin: auto; display: block;"></div>');

	Course.getAll(function success(data) {

		window.courses = _.indexBy(data, 'id');
		$('#course-list').remove();
		//$('#course-list-container .loader').remove();

		$("#course-list-container").append( courseList({courses : data}) );

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
		training = window.courses[id];
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

	$('#course-form-container').empty().append( courseForm(training) );

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

	$('#course-form-container').empty().append( courseForm(training) );

	$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}
