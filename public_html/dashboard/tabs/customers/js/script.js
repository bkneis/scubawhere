Handlebars.registerHelper('currency', function() {
	return window.company.currency.symbol;
});

Handlebars.registerHelper('addTransactionButton', function(id) {
	return new Handlebars.SafeString('<button onclick="addTransaction(' + id + ', this);" class="btn btn-default"><i class="fa fa-credit-card fa-fw"></i> Transactions</button>');
});

Handlebars.registerHelper('getCountry', function(id) {
	if(id == null) return "unknown";
	else return window.countries[id].name; 
});

Handlebars.registerHelper("friendlyDate", function(date) {
	return friendlyDate(date);
});

Handlebars.registerHelper("friendlyDateNoTime", function(date) {
	return friendlyDateNoTime(date);
});

Handlebars.registerHelper('sourceIcon', function() {
	var icon = '',
	tooltip = '';

	switch(this.source) {
		case null:         icon = 'fa-user';     tooltip = 'Agent';         break;
		case 'telephone':  icon = 'fa-phone';    tooltip = 'Telephone';     break;
		case 'email':      icon = 'fa-envelope'; tooltip = 'Email';         break;
		case 'facetoface': icon = 'fa-eye';      tooltip = 'Face-to-face';  break;
		default:           icon = 'fa-question'; tooltip = 'Not specified';
	}

	return new Handlebars.SafeString('<i class="fa ' + icon + ' fa-fw"></i> ' + tooltip);
});

Handlebars.registerHelper("tripFinish", function(start, duration) {
	startDate = friendlyDate(start);
	endDate   = friendlyDate( moment(start).add(duration, 'hours') );

	if(startDate.substr(0, 11) === endDate.substr(0, 11))
		// Only return the time, if the date is the same
		return endDate.substr(12);
	else
		// Only return the date and the Month (and time)
		return endDate.substr(0, 6) + ' ' + endDate.substr(12);
});

Handlebars.registerHelper('checkNull', function(value) {
	if(value == null || value == undefined || value == "") return "unknown";
	else return value;
});

$(function() {

	$.get("/api/country/all", function success(data) {
		window.countries = _.indexBy(data, 'id');

		Agency.getAll(function(data){
			window.agencies = _.indexBy(data, 'id');
			Customer.getAllCustomers(function(data) {

				console.log(data);
				window.customers = _.indexBy(data, 'id');
				renderCustomerList(window.customers);

			});
		});

	});

	$('#customer-list').on('click', '.accordion-header', function() {
		$(this).toggleClass('expanded');
		$('.accordion-' + this.getAttribute('data-id')).toggle();
	});

	$('#find-customer-form').on('submit', function(event) {
		event.preventDefault();

		var btn = $('#find-customer');
		btn.append(' <i class="fa fa-cog fa-spin"></i>');

		var params = $(this).serializeObject();

		Customer.filter(params, function success(data) {
			console.log(data);
			renderCustomerList(data);
			btn.html('Find Customer');
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0]);
			btn.html('Find Customer');
		});
	});

	$('#find-customer-form').on('reset', function(event) {
		renderCustomerList(window.customers);
	});

	$('#edit-customer-modal').on('submit', '#edit-customer-form', function(e) {
		e.preventDefault();

		var btn = $(this).find('button[type="submit"]');
		btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

		var params = $(this).serializeObject();
		params._token = window.token;
		console.log(params);

		Customer.updateCustomer(params, function success(data) {
			pageMssg(data.status, 'success');
			btn.html('Save');
			$('#edit-customer-modal').modal('hide');
			Customer.getCustomer("id="+params.id, function(data) {
				window.customers[params.id] = data;
				renderCustomerList(window.customers);
			});
		}, function error(xhr) {
			var data = JSON.parse(xhr.responseText);
			pageMssg(data.errors[0], 'danger');
			btn.html('Save');
		});
	});

	$('#email-customer-modal').on('submit', '#email-customer-form', function(e) {
		e.preventDefault();

		var btn = $(this).find('button[type="submit"]');
		btn.html('<i class="fa fa-cog fa-spin"></i> Sending...');

		var params = $(this).serializeObject();
		params._token = window.token;
		console.log(params);

		Company.sendEmail(params, function success(data){
			pageMssg('Thank you, your email has been sent', true);
			btn.html('Send');
			$('#email-customer-modal').modal('hide');
		},
		function error(xhr) {
			console.log(xhr);
			var data = JSON.parse(xhr.responseText);
			btn.html('Send');
			pageMssg(data.error.message, 'danger');
			
		});

	});

});

var customerListItem = Handlebars.compile( $('#customer-list-item-template').html() );
function renderCustomerList(customers) {

	$('#customer-table-div').html( customerListItem({customers: customers}) );

	if(customers.length != 0) createDataTable();

}

function format ( id ) {
    // `d` is the original data object for the row

    var customerButtonsTemplate = Handlebars.compile($('#customer-buttons-template').html());

    //var customer = _.where(window.customers, {id: parseInt(id)});

    //console.log(customer);
    console.log(id);

    return customerButtonsTemplate({customerID : parseInt(id)});

}

function editDetails(id) {

	$('#edit-customer-modal').modal('show');
	var editCustomerTemplate = Handlebars.compile($("#edit-customer-template").html());
	var countriesTemplate = Handlebars.compile($("#countries-template").html());
	var agenciesTemplate            = Handlebars.compile($("#agencies-template").html());
	var customerDivingInformationTemplate = Handlebars.compile($("#customer-diving-information-template").html());
	var certificatesTemplate        = Handlebars.compile($("#certificates-template").html());
	var selectedCertificateTemplate = Handlebars.compile($("#selected-certificate-template").html());
	$("#country_id").html(countriesTemplate({countries : window.countries}));
	$('#country_id').val(window.customers[id].country_id);
	$("#edit-customer-details").html(editCustomerTemplate(window.customers[id]));
	$("#customer-diving-information").html(customerDivingInformationTemplate(window.customers[id]));
	// Activate datepickers
	$('#edit-customer-modal input.datepicker').datetimepicker({
		pickDate: true,
		pickTime: false,
		icons: {
			time: 'fa fa-clock-o',
			date: 'fa fa-calendar',
			up:   'fa fa-chevron-up',
			down: 'fa fa-chevron-down'
		},
	});

	// Set the last_dive date
	$('#edit-customer-modal').find('.last_dive').val(window.customers[id].last_dive);

	$("#edit-customer-agencies").find('#agency_id').html(agenciesTemplate({agencies:window.agencies}));

	$('#edit-customer-modal').on('change', '#agency_id', function() {
		var self = $(this);

		if(self.val() === "") self.closest('fieldset').find('#certificate_id').empty();

		var certificate_dropdown = self.closest('fieldset').find('#certificate_id');

		certificate_dropdown.html(certificatesTemplate({certificates: window.agencies[self.val()].certificates}));
		certificate_dropdown.select2("val", "");
	});

	$('#edit-customer-modal').on('click', '.add-certificate', function(event) {
		event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)

		var self = $(this);
		var agency_dropdown      = self.closest('fieldset').find('#agency_id');
		var certificate_dropdown = self.closest('fieldset').find('#certificate_id');

		if(agency_dropdown.val() === "" || certificate_dropdown.val() === "") return false;

		self.closest('fieldset').find('#selected-certificates').append(selectedCertificateTemplate({
			id: certificate_dropdown.val(),
			abbreviation: window.agencies[agency_dropdown.val()].abbreviation,
			name: _.find(window.agencies[agency_dropdown.val()].certificates, function(certificate) {
				return certificate.id == certificate_dropdown.val();
			}).name,
		}));
	});

	$('#edit-customer-modal').on('click', '.remove-certificate', function() {
		$(this).parent().remove();
	});

}

function emailCustomer(id) {

	$('#email-customer-modal').modal('show');
	var emailCustomerTemplate = Handlebars.compile($("#email-customer-template").html());
	$("#email-customer-details").html(emailCustomerTemplate(window.customers[id]));

}

function viewBookings(id) {

	var summaryTable = Handlebars.compile($('#booking-summary-template').html());

	Booking.getCustomerBookings("customer_id="+id, function success(data) {
		//console.log(data);
		// add options to select box
		for(var i in data) {
			console.log(data[i]);
			$('#customer-bookings-ref').append('<option data-bookingID="'+data[i].id+'">'+data[i].reference+'</option>');
		}
		if(data[0] != null) {
			Booking.get(data[0].id, function sucess(data) {
				console.log(data);
				$("#customer-booking").html(summaryTable(data));
			});
			$('#customer-bookings-ref').on('change', function() {
				Booking.get($(this).find(':selected').attr('data-bookingID'), function sucess(data) {
					console.log(data);
					$("#customer-booking").html(summaryTable(data));
				});
			});
		}
	},
	function error(xhr) {
		var data = JSON.parse(xhr.responseText);
		pageMssg(data.message, 'danger');
	});
	$('#customer-bookings-modal').modal('show');

}

function getBooking(id) {

	
	Booking.get(id, function sucess(data) {
		console.log(data);
		$("#customer-booking").html(summaryTable(data));
	});
}

function friendlyDate(date) {
	// return moment(date).format('DD/MM/YYYY HH:mm');
	return moment(date).format('DD MMM YYYY HH:mm');
}

function friendlyDateNoTime(date) {
	// return moment(date).format('DD/MM/YYYY HH:mm');
	return moment(date).format('DD MMM YYYY');
}

function createDataTable() {
	var customerTable = $('#customers-table').DataTable({
		"paging":   true,
		"ordering": false,
		"info":     false,
		"pageLength" : 10,
		"searching" : false,
		"dom": 'T<"clear">lfrtip',
		"tableTools": {
			"sSwfPath": "/common/vendor/datatables-tabletools/swf/copy_csv_xls_pdf.swf"
		}
	});

	// Add event listener for opening and closing details
    $('#customer-list').on('click', 'tr', function () {
        var tr = $(this).closest('tr');
        var row = customerTable.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format($(this).attr('data-id')) ).show();
            tr.addClass('shown');
        }
    } );
}
