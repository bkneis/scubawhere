var courseForm,
    courseList,
    priceInputTemplate,
    ticketSelectTemplate;

// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(ticketID) {
	if(this.id == ticketID)
		return ' selected';
	else
		return '';
});
Handlebars.registerHelper('multiply', function(a, b) {
	return (a * b).toFixed(2);
});
Handlebars.registerHelper('count', function(array) {
	var sum = 0;
	_.each(array, function(value) {
		sum += value.pivot.quantity * 1;
	});
	return sum;
});
Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});
Handlebars.registerPartial('tickets_template', $('#tickets-template').html());
Handlebars.registerPartial('ticket_select', $('#ticket-select-template').html());

Handlebars.registerPartial('class_template', $('#class-template').html());
Handlebars.registerPartial('class_select', $('#class-select-template').html());

priceInputTemplate = Handlebars.compile( $('#price-input-template').html() );
Handlebars.registerPartial('price_input', priceInputTemplate);

window.sw.default_first_base_price = {
	id: randomString(),
	from: '0000-00-00',
	isBase: true,
	isAlways: true,
};
window.sw.default_base_price = {
	isBase: true,
	from: moment().format('YYYY-MM-DD'),
};
window.sw.default_price = {
	id: randomString(),
	from: moment().format('YYYY-MM-DD'),
	until: moment().add(3, 'months').format('YYYY-MM-DD'),
};

$(function(){

	// Render initial package list
	courseList = Handlebars.compile( $("#course-list-template").html() );
	renderCourseList();

	Class.getAll(function success(data) {
		window.training = _.indexBy(data, 'id');
		console.log(window.training);
	});

	// Default view: show create package form
	Ticket.getAllTickets(function success(data) {
		window.tickets = _.indexBy(data, 'id');

		courseForm = Handlebars.compile( $("#course-form-template").html() );
		renderEditForm();
		//Tour.getCoursesTour();
	});

	ticketSelectTemplate = Handlebars.compile( $("#ticket-select-template").html() );

	$("#course-form-container").on('submit', '#add-course-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#add-course').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		$('#training_id').val(parseInt($("#class-select").val()));

		Course.create( $('#add-course-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderCourseList(function() {
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
			$('#add-course-form').find('#save-loader').remove();
		});
	});

	$("#course-form-container").on('submit', '#update-course-form', function(event) {

		event.preventDefault();

		// Show loading indicator
		$('#update-course').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

		$('#training_id').val(parseInt($("#class-select").val()));

		Course.update( $('#update-course-form').serialize(), function success(data) {

			pageMssg(data.status, true);

			$('form').data('hasChanged', false);

			renderCourseList();

			$('.new_price').remove();

			if(data.base_prices) {
				_.each(data.base_prices, function(price) {
					price.isBase = true;
					$('.add-base-price').before( priceInputTemplate(price) );
				});
			}

			if(data.prices) {
				_.each(data.prices, function(price) {
					$('.add-price').before( priceInputTemplate(price) );
				});
			}

			// Remove the loader
			$('#update-course').prop('disabled', false);
			$('.loader').remove();
		}, function error(xhr) {

			var data = JSON.parse(xhr.responseText);
			console.log(data);

			if(data.errors.length > 0) {

				var errorsHTML = Handlebars.compile( $("#errors-template").html() );
				errorsHTML = errorsHTML(data);

				// Render error messages
				$('.errors').remove();
				$('#update-course-form').prepend(errorsHTML);
				$('#update-course').before(errorsHTML);
			}
			else {
				alert(xhr.responseText);
			}

			pageMssg('Oops, something wasn\'t quite right');

			$('#update-course').prop('disabled', false);
			$('.loader').remove();
		});
	});

	$('#course-form-container').on('change', '.ticket-select', function(event) {
		var $self     = $(event.target);
		var $quantity = $self.siblings('.quantity-input').first();
		var $prices   = $self.siblings('.ticket-prices').first();

		var id = $self.val(), disabledInputs, numberOfDisabledInputs;

		if(id == "0") {
			// Reset
			$quantity.prop('disabled', true);
			$quantity.attr('name', '');
			$quantity.val('');

			$prices.html( $prices.attr('data-default') );

			// Check if more than one empty ticket-selects exist and if so, remove the extra one
			disabledInputs         = $('.ticket-list').find('.quantity-input[disabled]');
			numberOfDisabledInputs = disabledInputs.length;
			if( numberOfDisabledInputs > 1) {
				disabledInputs.last().parent().remove();
			}
		}
		else {
			$quantity.prop('disabled', false);
			$quantity.attr('name', 'tickets[' + id + '][quantity]');
			$quantity.val(1);

			$quantity.trigger('change');

			// Check if empty ticket-select exists and if not, create and append one
			disabledInputs         = $('.ticket-list').find('.quantity-input[disabled]');
			numberOfDisabledInputs = disabledInputs.length;
			if( numberOfDisabledInputs === 0) {
				$('.ticket-list').append( ticketSelectTemplate({available_tickets: window.tickets}) );
			}
		}
	});

	$('#course-form-container').on('change', '.quantity-input', function(event) {
		var $quantity = $(event.target);
		var $prices   = $quantity.siblings('.ticket-prices').first();
		var $ticket   = $quantity.siblings('.ticket-select').first();
		var id = $ticket.val();

		/*
		var html = '';
		_.each(window.tickets[id].prices, function(p, index, list) {
			html += '<span style="border: 1px solid lightgray; padding: 0.25em 0.5em;">' + p.fromDay + '/' + p.fromMonth + ' - ' + p.untilDay + '/' + p.untilMonth + ': ' + window.company.currency.symbol + ' ' + ($quantity.val() * p.decimal_price).toFixed(2) + '</span> ';
		});

		$prices.html(html);
		*/
	});

	$('#course-form-container').on('click', '.remove-course', function(event) {
    event.preventDefault();
		var check = confirm('Do you really want to remove this course?');
		if(check){
			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Course.delete({
				'id'    : $('#update-course-form input[name=id]').val(),
				'_token': $('[name=_token]').val()
			}, function success(data){

				pageMssg(data.status, true);

				renderCourseList();

				renderEditForm();
			}, function error(xhr){

				pageMssg('Oops, something wasn\'t quite right');

				$('.remove-course').prop('disabled', false);
				$('#save-loader').remove();
			});
		}
	});

	$("#course-list-container").on('click', '#change-to-add-course', function(event){

		event.preventDefault();
		renderEditForm();
	});

	$('#course-form-container').on('click', '.add-base-price', function(event) {
		event.preventDefault();

		window.sw.default_base_price.id = randomString();
		$(event.target).before( priceInputTemplate(window.sw.default_base_price) );
		initPriceDatepickers();
	});

	$('#course-form-container').on('click', '.add-price', function(event) {
		
		event.preventDefault();
		window.sw.default_price.id = randomString();
		$(event.target).before( priceInputTemplate(window.sw.default_price) );
		initPriceDatepickers();
	});

	$('#course-form-container').on('click', '.remove-price', function(event) {
		
		event.preventDefault();
		$(event.target).parent().remove();
	});

});

function renderCourseList(callback) {

	$('#course-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Course.getAll(function success(data) {

		window.courses = _.indexBy(data, 'id');
		console.log(window.courses);
		$('#course-list').remove();
		$('#course-list-container .loader').remove();

		$("#course-list-container").append( courseList({courses : data}) );

		// (Re)Assign eventListener for package clicks
		$('#course-list').on('click', 'li, strong', function(event) {

			if( $(event.target).is('strong') )
				event.target = event.target.parentNode;

			renderEditForm( event.target.getAttribute('data-id') );
		});

		if( typeof callback === 'function')
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

	var course;

	if(id) {
		course = window.courses[id];

		course.task   = 'update';
		course.update = true;

		_.each(course.tickets, function(value) {
			value.existing = true;
			value.available_tickets = window.tickets;
		});

		_.each(course.base_prices, function(value) {
			value.isBase   = true;

			if(value.from == '0000-00-00')
				value.isAlways = true;
		});
	}
	else {
		// Set defaults for a new package form
		course = {
			task: 'add',
			update: false,
			base_prices: [ window.sw.default_first_base_price ],
		};
	}

	course.available_tickets = window.tickets;
	course.available_training = window.training;
	course.default_price     = window.sw.default_price;

	$('#course-form-container').empty().append( courseForm(course) );

	if(!id)
		$('input[name=name]').focus();

	CKEDITOR.replace( 'description' );

	initPriceDatepickers();

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}

function showMe(box, self) {

	var div = $(box);

	if( $(self).is(':checked') ) {
		div.show(0);
		div.find('input, select').prop('disabled', false);
	}
	else {
		div.hide(0);
		div.find('input, select').prop('disabled', true);
	}
}