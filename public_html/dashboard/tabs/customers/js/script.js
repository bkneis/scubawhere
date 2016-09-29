
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

Handlebars.registerHelper('currency', function () {
    return window.company.currency.symbol;
});

Handlebars.registerHelper('addTransactionButton', function (id) {
    return new Handlebars.SafeString('<button onclick="addTransaction(' + id + ', this);" class="btn btn-default"><i class="fa fa-credit-card fa-fw"></i> Transactions</button>');
});

Handlebars.registerHelper('getCountry', function (id) {
    if (id == null) return "unknown";
    else return window.countries[id].name;
});

Handlebars.registerHelper("friendlyDate", function (date) {
    return friendlyDate(date);
});

Handlebars.registerHelper("friendlyDateNoTime", function (date) {
    return friendlyDateNoTime(date);
});

Handlebars.registerHelper('sourceIcon', function () {
    var icon = '',
        tooltip = '';

    switch (this.source) {
        case null:
            icon = 'fa-user';
            tooltip = 'Agent';
            break;
        case 'telephone':
            icon = 'fa-phone';
            tooltip = 'Telephone';
            break;
        case 'email':
            icon = 'fa-envelope';
            tooltip = 'Email';
            break;
        case 'facetoface':
            icon = 'fa-eye';
            tooltip = 'Face-to-face';
            break;
        default:
            icon = 'fa-question';
            tooltip = 'Not specified';
    }

    return new Handlebars.SafeString('<i class="fa ' + icon + ' fa-fw"></i> ' + tooltip);
});

Handlebars.registerHelper("tripFinish", function (start, duration) {
    startDate = friendlyDate(start);
    endDate = friendlyDate(moment(start).add(duration, 'hours'));

    if (startDate.substr(0, 11) === endDate.substr(0, 11))
    // Only return the time, if the date is the same
        return endDate.substr(12);
    else
    // Only return the date and the Month (and time)
        return endDate.substr(0, 6) + ' ' + endDate.substr(12);
});

Handlebars.registerHelper('checkNull', function (value) {
    if (value == null || value == undefined || value == "") return "unknown";
    else return value;
});

var editCustomerTemplate = Handlebars.compile($("#edit-customer-template").html());
var countriesTemplate = Handlebars.compile($("#countries-template").html());
var agenciesTemplate = Handlebars.compile($("#agencies-template").html());
var customerDivingInformationTemplate = Handlebars.compile($("#customer-diving-information-template").html());
var certificatesTemplate = Handlebars.compile($("#certificates-template").html());
var selectedCertificateTemplate = Handlebars.compile($("#selected-certificate-template").html());
var templateColumnFormatSelect = Handlebars.compile($("#template-column-format-select").html());
var templateImportErrors = Handlebars.compile($('#template-import-errors').html());
var templateImpotCustomers = Handlebars.compile($('#template-import-customers').html());

$(function () {

    var num_import_columns = 1;

    var sModelImportCustomers = $('#modal-import-customers');

    $.get("/api/country/all", function success(data) {
        window.countries = _.indexBy(data, 'id');

        Agency.getAll(function (data) {
            window.agencies = _.indexBy(data, 'id');
            Customer.getAllCustomers(function (data) {

                //console.log(data);
                window.customers = _.indexBy(data, 'id');
                renderCustomerList(window.customers);

            });
        });

    });

    $('#customer-list').on('click', '.accordion-header', function () {
        $(this).toggleClass('expanded');
        $('.accordion-' + this.getAttribute('data-id')).toggle();
    });

    $('#find-customer-form').on('submit', function (event) {
        event.preventDefault();

        var btn = $('#find-customer');
        btn.append(' <i class="fa fa-cog fa-spin"></i>');

        var params = $(this).serializeObject();

        Customer.filter(params, function success(data) {
            //console.log(data);
            renderCustomerList(data);
            btn.html('Find Customer');
        }, function error(xhr) {
            var data = JSON.parse(xhr.responseText);
            pageMssg(data.errors[0]);
            btn.html('Find Customer');
        });
    });

    $('#find-customer-form').on('reset', function (event) {
        renderCustomerList(window.customers);
    });

    $('#edit-customer-modal').on('submit', '#edit-customer-form', function (e) {
        e.preventDefault();

        var btn = $(this).find('button[type="submit"]');
        btn.html('<i class="fa fa-cog fa-spin"></i> Saving...');

        var params = $(this).serializeObject();
        params._token = window.token;
        //console.log(params);

        if (params.mode === 'edit') {
            delete params.mode;

            Customer.updateCustomer(params, function success(data) {
                pageMssg(data.status, 'success');
                btn.html('Save');
                $('#edit-customer-modal').modal('hide');
                Customer.getCustomer("id=" + params.id, function (data) {
                    window.customers[params.id] = data;
                    renderCustomerList(window.customers);
                });
            }, function error(xhr) {
                var data = JSON.parse(xhr.responseText);
                if (data.errors) pageMssg(data.errors[0], 'danger');
                btn.html('Save');
            });
        }
        else if (params.mode === 'add') {
            delete params.mode;
            delete params.id;

            Customer.createCustomer(params, function success(data) {
                pageMssg(data.status, 'success');
                btn.html('Save');
                $('#edit-customer-modal').modal('hide');
                Customer.getCustomer("id=" + data.id, function (data) {
                    window.customers[data.id] = data;
                    renderCustomerList(window.customers);
                });
            }, function error(xhr) {
                var data = JSON.parse(xhr.responseText);
                if (data.errors) pageMssg(data.errors[0], 'danger');
                btn.html('Save');
            });
        }
    });

    $('#edit-customer-modal').on('change', '#agency_id', function () {
        var self = $(this);

        if (self.val() === "") self.closest('fieldset').find('#certificate_id').empty();

        var certificate_dropdown = self.closest('fieldset').find('#certificate_id');

        certificate_dropdown.html(certificatesTemplate({certificates: window.agencies[self.val()].certificates}));
        certificate_dropdown.select2("val", "");
    });

    $('#edit-customer-modal').on('click', '.add-certificate', function (event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)

        var self = $(this);
        var agency_dropdown = self.closest('fieldset').find('#agency_id');
        var certificate_dropdown = self.closest('fieldset').find('#certificate_id');

        if (agency_dropdown.val() === "" || certificate_dropdown.val() === "") return false;

        self.closest('fieldset').find('#selected-certificates').append(selectedCertificateTemplate({
            id: certificate_dropdown.val(),
            abbreviation: window.agencies[agency_dropdown.val()].abbreviation,
            name: _.find(window.agencies[agency_dropdown.val()].certificates, function (certificate) {
                return certificate.id == certificate_dropdown.val();
            }).name,
        }));
    });

    $('#edit-customer-modal').on('click', '.remove-certificate', function () {
        $(this).parent().remove();
    });

    $('#email-customer-modal').on('submit', '#email-customer-form', function (e) {
        e.preventDefault();

        var btn = $(this).find('button[type="submit"]');
        btn.html('<i class="fa fa-cog fa-spin"></i> Sending...');

        var params = $(this).serializeObject();
        params._token = window.token;
        //console.log(params);

        Company.sendEmail(params, function success(data) {
                pageMssg('Thank you, your email has been sent', true);
                btn.html('Send');
                $('#email-customer-modal').modal('hide');
            },
            function error(xhr) {
                //console.log(xhr);
                var data = JSON.parse(xhr.responseText);
                btn.html('Send');
                pageMssg(data.error.message, 'danger');

            });
    });

    $('#search-customer-container').on('click', '#add-new-customer', function (event) {
        event.preventDefault();
        editDetails();
    });

    $('#btn-import-customers').on('click', function (event) {
        event.preventDefault();
        num_import_columns = 1;
        $('#import-customer-data-body').empty().append(templateImpotCustomers);
        $('#modal-import-customers').modal('show');
    });

    sModelImportCustomers.on('click', '#btn-add-column', function (event) {
        event.preventDefault();
        $('#column-csv-format').append(templateColumnFormatSelect(num_import_columns));
        num_import_columns++;
    });

    sModelImportCustomers.on('click', '.remove-column', function (event) {
        event.preventDefault();
        num_import_columns--;
        $(this).parent().remove();
        // Loop through the column labels so they represent the correct column when one is removed, i.e 1 2 3, 2 is removed so 3 becomes 2
        $('.label-column').each(function (index) {
            $(this).html('Column ' + (index + 1) + ' :');
        });
    });

    sModelImportCustomers.on('submit', '#frm-import-customer-data', function (event) {
        event.preventDefault();
        // Check for HTML5 file reader support
        if (!window.FileReader) {
            alert('Im so sorry but your browser is not supported. So that scubawhere can offer you the best possible system, we require you to use the most up to date chrome. (change this)');
            return;
        }
        // Get data from the form and serialize it into an array
        var data = $(this).serializeArray();

        // Get the file from the html input
        var customerDataCSV = $('#in-customer-data-csv').prop('files')[0];

        if(customerDataCSV == undefined || customerDataCSV == null) {
            pageMssg("Please upload a csv file to import your customer data");
            return;
        }

        var reader = new FileReader();
        // Read file into memory as UTF-8
        reader.readAsText(customerDataCSV);

        reader.onload = function (evt) {
            loadCSVFile(evt, data);
        };
        reader.onerror = errorCSVFile;
    });

	sModelImportCustomers.on('click', '#btn-download-error-file', function(event) {
		event.preventDefault();
		Customer.getLastImportFileUrl(function success(data) {
			window.open(data.url, '_self');
		});
	});

    /*sModelImportCustomers.on('click', '#btn-download-error-file', function(event) {
        event.preventDefault();
        Customer.getLastImportErrorFile(
            function success(data) {},
            function error(xhr) {
                alert(xhr.responseText);
                console.log(xhr);
            }
        );
    });*/

});

function loadCSVFile(evt, columnData) {

    var csv = evt.target.result;
	csv += '\n';
	console.log('csv str', csv);
    // @todo minor - Move this file processing to backend. Javascript should just send the file not strings
    var allTextLines = csv.split(/\r\n|\n/);
    var lines = [];
    for (var i = 0; i < (allTextLines.length - 1); i++) {
        var data = allTextLines[i].split(',');
        var tarr = [];
		// @note this is abit of a hack to remove empty lines
		console.log('l', data.length);
		console.log('v', data[0]);
		if(data.length === 1 && data[0] === '') continue;
        for (var j = 0; j < data.length; j++) {
            tarr.push(data[j]);
        }
        lines.push(tarr);
    }

    var csvData = {
        columns: [],
        customerData: lines,
        _token: getToken()
    };

    // Transform the form data to an array mapping column indexes to attributes
    for (var j = 0; j < columnData.length; j++) {
        csvData.columns.push(columnData[j].value);
    }

    // Validate that the required columns have been included
    if(csvData.columns.indexOf("firstname") < 0) {
        pageMssg("The first name column is required for our system");
        return;
    }
    if(csvData.columns.indexOf("lastname") < 0) {
        pageMssg("The last name column is required for our system");
        return;
    }

    console.log('csv data', csvData);
    Customer.importCSV(csvData,
        function success(data) {
            console.log(data);
            $('#import-customer-data-body').empty().append(templateImportErrors({errors : data.errors}));
			Customer.getAllCustomers(function (data) {
				window.customers = _.indexBy(data, 'id');
				renderCustomerList(window.customers);
			});
        },
        function error(xhr) {
            console.log(xhr);
			var data = JSON.parse(xhr.responseText);
			for(var i in data.errors)
			{
				pageMssg(data.errors[i], 'danger');
			}

        }
    );
}

function errorCSVFile(evt) {
    console.log(evt.target.error.name);
}

var customerListItem = Handlebars.compile($('#customer-list-item-template').html());
function renderCustomerList(customers) {

    $('#customer-table-div').html(customerListItem({customers: customers}));

    if (!_.isEmpty(customers)) createDataTable();

}

function format(id) {
    // `d` is the original data object for the row

    var customerButtonsTemplate = Handlebars.compile($('#customer-buttons-template').html());

    //var customer = _.where(window.customers, {id: parseInt(id)});

    //console.log(customer);
    //console.log(id);

    return customerButtonsTemplate({customerID: parseInt(id)});

}

//var editCustomerTemplate = Handlebars.compile($("#edit-customer-template").html());
//var countriesTemplate = Handlebars.compile($("#countries-template").html());
//var agenciesTemplate = Handlebars.compile($("#agencies-template").html());
//var customerDivingInformationTemplate = Handlebars.compile($("#customer-diving-information-template").html());
//var certificatesTemplate = Handlebars.compile($("#certificates-template").html());
//var selectedCertificateTemplate = Handlebars.compile($("#selected-certificate-template").html());

function editDetails(id) {

    var customer;

    if (id) {
        customer = window.customers[id];
        customer.task = 'edit';
        customer.update = true;
    }
    else {
        customer = {
            task: 'add',
            update: false
        };
    }

    $("#edit-customer-agencies").find('#agency_id').html(agenciesTemplate({agencies: window.agencies}));

    if (id) {
        $('#country_id').val(customer.country_id);
        $("#edit-customer-details").html(editCustomerTemplate(customer));
        $("#customer-diving-information").html(customerDivingInformationTemplate(customer));

        // Set the last_dive date
        $('#edit-customer-modal').find('.last_dive').val(customer.last_dive);

        $('#edit-customer-agencies').find('#selected-certificates').empty();
        _.each(customer.certificates, function (certificate) {
            $('#edit-customer-agencies').find('#selected-certificates').append(selectedCertificateTemplate({
                id: certificate.id,
                abbreviation: certificate.agency.abbreviation,
                name: certificate.name,
            }));
        });
    }
    else {
        $("#edit-customer-details").html(editCustomerTemplate(customer));
        $("#customer-diving-information").html(customerDivingInformationTemplate({}));
    	$('#edit-customer-agencies').find('#selected-certificates').empty();
    }

    $('#edit-customer-modal').modal('show');

    $("#country_id").html(countriesTemplate({countries: window.countries}));

    // Activate datepickers
    $('#edit-customer-modal .datepicker').datetimepicker({
        // defaultDate: '1980-01-01',
        pickDate: true,
        pickTime: false,
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down'
        }
    });

    // Enable select2 dropdown for edit-form dropdown fields
    $('#edit-customer-countries').find('#country_id').select2();
    $('#edit-customer-agencies').find('#agency_id').select2();
    $('#edit-customer-agencies').find('#certificate_id').select2();

    // Set correct country
    if (id && customer.country_id) {
        $('#edit-customer-countries').find('#country_id').select2("val", customer.country_id);
    }
    else {
        $('#edit-customer-countries').find('#country_id').select2("val", company.country_id);
    }
}

function emailCustomer(id) {

    $('#email-customer-modal').modal('show');
    var emailCustomerTemplate = Handlebars.compile($("#email-customer-template").html());
    $("#email-customer-details").html(emailCustomerTemplate(window.customers[id]));

}

function viewBookings(id) {

    var summaryTable = Handlebars.compile($('#booking-summary-template').html());

    Booking.getCustomerBookings("customer_id=" + id, function success(data) {
            //console.log(data);
            // add options to select box
            for (var i in data) {
                //console.log(data[i]);
                $('#customer-bookings-ref').append('<option data-bookingID="' + data[i].id + '">' + data[i].reference + '</option>');
            }
            if (data[0] != null) {
                Booking.get(data[0].id, function sucess(data) {
                    //console.log(data);
                    $("#customer-booking").html(summaryTable(data));
                });
                $('#customer-bookings-ref').on('change', function () {
                    Booking.get($(this).find(':selected').attr('data-bookingID'), function sucess(data) {
                        //console.log(data);
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
        //console.log(data);
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
        "pageLength": 10,
		"dom": 'Bfrtlp',
		"buttons": [
			{
				extend : 'excel',
	   			title  : 'Customer List' 	
			},
			{
				extend : 'pdf',
				title  : 'Customer List'
			},
			{
				extend : 'print',
				title  : 'Customer List'
			}
		]
    });

    // Add event listener for opening and closing details
    $('#customer-list').on('click', 'tr', function () {
        var tr = $(this).closest('tr');
        var row = customerTable.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child(format($(this).attr('data-id'))).show();
            tr.addClass('shown');
        }
    });
}
