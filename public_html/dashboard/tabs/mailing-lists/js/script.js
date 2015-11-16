var viewCampaignsTemplate;
var customerGroupListTemplate;
var addCustomerGroupTemplate;
var selectCustomersModal;
var agenciesTemplate;
var certificatesTemplate;
var selectedCertificateTemplate;
var selectedCustomerGroupTemplate;
var createCampaignTemplate;
var groupSelectTemplate;
var campaignsGroupsTemplate;
var selected_email_template;
var groupCustomersTable;

$(function() {

    customerGroupListTemplate = Handlebars.compile($("#group-list-template").html());
    addCustomerGroupTemplate = Handlebars.compile($("#customer-group-form-template").html());
    groupSelectTemplate = Handlebars.compile($("#group-select-template").html());
    agenciesTemplate = Handlebars.compile($("#agencies-template").html());
    ticketsTemplate = Handlebars.compile($("#tickets-template").html());
    classesTemplate = Handlebars.compile($("#classes-template").html());
    certificatesTemplate = Handlebars.compile($("#certificates-template").html());
    selectedCertificateTemplate = Handlebars.compile($("#selected-certificate-template").html());
    selectedCustomerGroupTemplate = Handlebars.compile($("#selected-group-template").html());
    selectedTicketTemplate = Handlebars.compile($("#selected-ticket-template").html());
    selectedClassTemplate = Handlebars.compile($("#selected-class-template").html());
    selectedAgencyTemplate = Handlebars.compile($("#selected-agency-template").html());

    Agency.getAll(function sucess(data) {
        window.agencies = _.indexBy(data, 'id');
        Certificate.getAll(function success(data) {
            window.certificates = _.indexBy(data, 'id');
            Ticket.getAllTickets(function success(data) {
                window.tickets = _.indexBy(data, 'id');
                Class.getAll(function success(data) {
                    window.trainings = _.indexBy(data, 'id');
                    renderGroupList();
                    renderGroupEditForm();
                });
            });
        });
    });

    $('#group-form-container').on('click', '.remove-customer-group', function(event) {
        event.preventDefault();
        var check = confirm('Do you really want to remove this customer group?');
        if (check) {
            // Show loading indicator
            $(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

            CustomerGroup.delete({
                'id': $('#update-group-form input[name=id]').val(),
                '_token': $('[name=_token]').val()
            }, function success(data) {

                pageMssg(data.status, true);

                renderGroupList(renderGroupEditForm);

            }, function error(xhr) {

                pageMssg('Oops, something wasn\'t quite right');

                $('.remove-customer-group').prop('disabled', false);
                $('#save-loader').remove();
            });
        }
    });

    $("#group-form-container").on('submit', '#add-group-form', function(event) {
        event.preventDefault();
        var params = $(this).serializeObject();
        params._token = window.token;
        console.log(params);

        CustomerGroup.create(params, function success(data) {
                pageMssg(data.status, true);
                renderGroupList();
                $('form').data('hasChanged', false);
                renderGroupEditForm();
            },
            function error(xhr) {
                pageMssg(xhr.responseText);
                console.log(xhr);
            });
    });

    $("#group-form-container").on('submit', '#update-group-form', function(event) {
        event.preventDefault();
        var params = $(this).serializeObject();
        params._token = window.token;
        console.log(params);

        CustomerGroup.update(params, function success(data) {
                pageMssg(data.status, true);
                renderGroupList();
            },
            function error(xhr) {
                pageMssg(xhr.responseText);
                console.log(xhr);
            });
    });
    
    groupCustomersTable = $('#group-customers-table').DataTable({
        "paging":   false,
        "ordering": false,
        "info":     false,
        "columnDefs" : [
            {
                "targets" : [2],
                "visible" : false
            }        
        ]
    });

});

function renderGroupEditForm(id) {

    if (unsavedChanges()) {
        var question = confirm("ATTENTION: All unsaved changes are lost!");
        if (!question) {
            return false;
        }
    }

    var group;

    if (id) {
        group = window.groups[id];
        group.task = 'update';
        group.update = true;
    } else {
        group = {
            task: 'add'
        };
        group.update = false;
    }

    $("#group-form-container").empty().append(addCustomerGroupTemplate(group));

    if (!id)
        $('input[name=name]').focus();

    setToken('[name=_token]');

    // Set up change monitoring
    $('form').on('change', 'input, select, textarea', function() {
        $('form').data('hasChanged', true);
    });

    $("#add-certificates").find('#agency_id').html(agenciesTemplate({
        agencies: window.agencies
    }));

    $("#add-tickets").find('#ticket_id').html(ticketsTemplate({
        tickets: window.tickets
    }));

    $("#add-classes").find('#class_id').html(classesTemplate({
        trainings: window.trainings
    }));

    $('#add-certificates').on('change', '#agency_id', function() {
        var self = $(this);

        var certificate_dropdown = self.closest('.form-row').find('#certificate_id');

        if (self.val() === "") certificate_dropdown.empty();

        certificate_dropdown.html(certificatesTemplate({
            certificates: window.agencies[self.val()].certificates
        }));
        certificate_dropdown.select2("val", "");
    });

    $('#add-certificates').on('click', '.add-certificate', function(event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)
        var self = $(this);
        var agency_dropdown = self.closest('.form-row').find('#agency_id');
        var certificate_dropdown = self.closest('.form-row').find('#certificate_id');

        if (agency_dropdown.val() === "" || certificate_dropdown.val() === "") return false;

        if (certificate_dropdown.val() === 'all') {
            $('#group-form-container').find('#selected-rules').append(selectedAgencyTemplate({
                id: agency_dropdown.val(),
                abbreviation: window.agencies[agency_dropdown.val()].abbreviation
            }));
        } else {
            $('#group-form-container').find('#selected-rules').append(selectedCertificateTemplate({
                id: certificate_dropdown.val(),
                abbreviation: window.agencies[agency_dropdown.val()].abbreviation,
                name: _.find(window.agencies[agency_dropdown.val()].certificates, function(certificate) {
                    return certificate.id == certificate_dropdown.val();
                }).name,
            }));
        }
    });

    $('#add-tickets').on('click', '.add-ticket', function(event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)
        var self = $(this);
        var ticket_dropdown = self.closest('.form-row').find('#ticket_id');

        if (ticket_dropdown.val() === "") return false;

        $('#group-form-container').find('#selected-rules').append(selectedTicketTemplate({
            id: ticket_dropdown.val(),
            name: window.tickets[ticket_dropdown.val()].name,
        }));
    });

    $('#add-classes').on('click', '.add-class', function(event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)
        var self = $(this);
        var class_dropdown = self.closest('.form-row').find('#class_id');

        if (class_dropdown.val() === "") return false;

        $('#group-form-container').find('#selected-rules').append(selectedClassTemplate({
            id: class_dropdown.val(),
            name: window.trainings[class_dropdown.val()].name,
        }));
    });
    
    $('#view-lists-customers').on('click', function(event) {
        event.preventDefault();
        $('#view-group-customers-modal').modal('show');
        var params = {};
        params.id = parseInt($(this).attr('data-id'));
        CustomerGroup.getCustomerAnalysis(params, function success(data) {
            console.log(data);
            groupCustomersTable.clear();
            for(var i in data.customers)
            {
                  groupCustomersTable.row.add([
                      data.customers[i].firstname + data.customers[i].lastname,
                      data.customers[i].email,
                      data.customers[i].id,
                      data.customers[i].emails_opened,
                      data.num_sent,
                      parseInt((data.customers[i].emails_opened / data.num_sent) * 100) + '%'
                  ]);
            }
            groupCustomersTable.draw();
        });
    });

    if (id) {

        $('#add-certificates').find('#selected-rules').empty();
        _.each(window.groups[id].rules, function(rule) {
            if (rule.certificate_id !== null) {
                $('#group-form-container').find('#selected-rules').append(selectedCertificateTemplate({
                    id: rule.certificate_id,
                    abbreviation: window.certificates[rule.certificate_id].agency.abbreviation,
                    name: window.certificates[rule.certificate_id].name,
                }));
            } else if (rule.ticket_id !== null) {
                $('#group-form-container').find('#selected-rules').append(selectedTicketTemplate({
                    id: rule.ticket_id,
                    name: window.tickets[rule.ticket_id].name
                }));
            } else if (rule.training_id !== null) {
                $('#group-form-container').find('#selected-rules').append(selectedClassTemplate({
                    id: rule.training_id,
                    name: window.trainings[rule.training_id].name
                }));
            } else if (rule.agency_id !== null) {
                $('#group-form-container').find('#selected-rules').append(selectedAgencyTemplate({
                    id: rule.agency_id,
                    abbreviation: window.agencies[rule.agency_id].abbreviation
                }));
            }
        });

    }

    $('#group-form-container').on('click', '.remove-certificate', function() {
        $(this).parent().remove();
    });

    $('#group-form-container').on('click', '.remove-ticket', function() {
        $(this).parent().remove();
    });

    $('#group-form-container').on('click', '.remove-class', function() {
        $(this).parent().remove();
    });
    
    $('#group-form-container').on('click', '.remove-agency', function() {
        $(this).parent().remove();
    });
    
    CKEDITOR.replace('description');

}

function unsavedChanges() {
    return $('form').data('hasChanged');
}

function renderGroupList(callback) {

    $('#customer-group-list').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

    CustomerGroup.getAll(function success(data) {

        window.groups = _.indexBy(data, 'id');
        $('#customer-group-list .loader').remove();

        $("#customer-group-list").empty().append(customerGroupListTemplate({
            groups: data
        }));
        
        $("#add-customer-group").on('click', function(event) {
            event.preventDefault();
            renderGroupEditForm();
        });

        // (Re)Assign eventListener for addon clicks
        $('#customer-group-list').on('click', 'li, strong', function(event) {

            if ($(event.target).is('strong'))
                event.target = event.target.parentNode;

            renderGroupEditForm(event.target.getAttribute('data-id'));
        });

        if (typeof callback === 'function')
            callback();
    });

}
