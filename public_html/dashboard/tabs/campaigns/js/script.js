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

$(function() {

    viewCampaignsTemplate = Handlebars.compile($("#campaigns-template").html());
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

    createCampaignTemplate = Handlebars.compile($("#create-campaign-template").html());
    campaignsGroupsTemplate = Handlebars.compile($("#view-campaigns-groups-template").html());

    $('#campaign-container').append(campaignsGroupsTemplate());

    Agency.getAll(function sucess(data) {
        window.agencies = _.indexBy(data, 'id');
        Certificate.getAll(function success(data) {
            window.certificates = _.indexBy(data, 'id');
            Ticket.getAllTickets(function success(data) {
                window.tickets = _.indexBy(data, 'id');
                Class.getAll(function success(data) {
                    window.trainings = _.indexBy(data, 'id');
                    renderGroupList(renderCampaignTable());
                });
            });
        });
    });

    $('#campaign-form-container').on('click', '.remove-customer-group', function(event) {
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

    $("#campaign-form-container").on('submit', '#add-group-form', function(event) {
        event.preventDefault();
        var params = $(this).serializeObject();
        params._token = window.token;
        console.log(params);

        CustomerGroup.create(params, function success(data) {
                pageMssg(data.status, true);
                renderGroupList(renderCampaignTable);
            },
            function error(xhr) {
                pageMssg(xhr.responseText);
                console.log(xhr);
            });
    });

    $("#campaign-form-container").on('submit', '#update-group-form', function(event) {
        event.preventDefault();
        var params = $(this).serializeObject();
        params._token = window.token;
        console.log(params);

        CustomerGroup.update(params, function success(data) {
                pageMssg(data.status, true);
                renderGroupList(renderCampaignTable);
            },
            function error(xhr) {
                pageMssg(xhr.responseText);
                console.log(xhr);
            });
    });

    $("#campaign-form-container").on('click', '#create-campaign', function(event) {
        event.preventDefault();
        renderCampaignForm();
    });

});

function renderCampaignTable() {
    $(".return-campaign").remove();
    Campaign.getAll(function sucess(data) {
        console.log(data);
        $("#campaign-form-container").empty().append(viewCampaignsTemplate({
            campaigns: data
        }));
    });
}

function renderCampaignForm(id) {

    var campaign;

    if (id) {
        campaign = window.campaigns[id];
        campaign.task = 'view';
        campaign.update = true;
    } else {
        campaign = {
            task: 'create',
            update: false
        };
    }

    $("#campaign-container").empty().append(createCampaignTemplate(campaign));

    $('#show-email-browser').on('click', function(event) {
        event.preventDefault();

        var w = window.open('');
        w.document.write(processEmailHtml());
        w.document.close();
    });

    if (!id)
        $('input[name=name]').focus();

    setToken('[name=_token]');

    $("#add-customer-group-to-campaign").find('#customer_group_id').empty().append(groupSelectTemplate({
        groups: window.groups
    }));

    $('#add-customer-group-to-campaign').on('click', '.add-group', function(event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)

        var self = $(this);
        var group_dropdown = self.closest('fieldset').find('#customer_group_id');

        if (group_dropdown.val() === "") return false;

        self.closest('fieldset').find('#selected-customer-groups').append(selectedCustomerGroupTemplate({
            id: group_dropdown.val(),
            name: window.groups[group_dropdown.val()].name
        }));
    });

    $('#add-customer-group-to-campaign').on('click', '.remove-group', function() {
        $(this).parent().remove();
    });

    $('#select-campaign-template').on('click', function(event) {
        event.preventDefault();
        $('#select-email-template-modal').modal('show');
        $('.email-template-option').on('click', function() {
            $('.email-template-option').css('border', 'none');
            $(this).css('border', '3px solid #FF7163');
            $.get($(this).attr('data-url'), function(response) {
                var email_preview_frame = document.getElementById('email-template-option-preview');
                email_preview_frame.contentWindow.document.open();
                email_preview_frame.contentWindow.document.write(processEmailHtml(response));
                email_preview_frame.contentWindow.document.close();
            });
            selected_email_template = $(this).attr('data-url');
        });
        $('#select-email-template').on('click', function() {
            $('#email-template-editor').css('display', 'inline');
            $('#show-email-browser').css('display', 'inline');
            $('#email-template-editor').attr('src', selected_email_template);
            $('#select-email-template-modal').modal('hide');
        });
    })

    if (id) {

        $('#campaign-container').find('#selected-rules').empty();
        _.each(window.groups[id].rules, function(rule) {
            if (rule.certificate_id !== null || rule.certificate_id !== undefined) {
                $('#campaign-form-container').find('#selected-rules').append(selectedCertificateTemplate({
                    id: rule.certificate_id,
                    abbreviation: window.certificates[rule.certificate_id].agency.abbreviation,
                    name: window.certificates[rule.certificate_id].name
                }));
            }
        });

    } else {

        $('#send-email-campaign').on('submit', '#create-campaign-form', function(event) {
            event.preventDefault();
            var params = $(this).serializeObject();
            params.html_string = processEmailHtml();
            for (var i = 0; i < params.groups.length; i++) {
                params.groups[i] = parseInt(params.groups[i]);
            }
            Campaign.create(params, function success(data) {
                    console.log(data);
                    pageMssg(data.status, true);
                },
                function error(xhr) {
                    console.log(xhr.responseText);
                });
        });

    }

    $(".return-campaigns").on('click', function(event) {
        event.preventDefault();
        Campaign.getAll(function sucess(data) {
            console.log(data);
            $('#campaign-container').empty().append(campaignsGroupsTemplate());
            renderGroupList();
            $("#campaign-form-container").empty().append(viewCampaignsTemplate({
                campaigns: data
            }));
            $("#campaign-form-container").on('click', '#create-campaign', function(event) {
                event.preventDefault();
                renderCampaignForm();
            });
        });
    });
}

function processEmailHtml(html_string) {

    if(!html_string) {
        var html_string = document.getElementById("email-template-editor").contentWindow.document.documentElement.outerHTML;
    }

    var script_pos = html_string.indexOf('<script type="text/javascript" src="js/medium-editor.js"></script>');
    var html_compiled = html_string.substring(0, script_pos) + '</body></html>';

    var find = 'contenteditable="true"';
    var re = new RegExp(find, 'g');
    html_compiled = html_compiled.replace(re, '');

    find = 'data-medium-editor-element="true"';
    re = new RegExp(find, 'g');
    html_compiled = html_compiled.replace(re, '');

    find = 'role="textbox"';
    re = new RegExp(find, 'g');
    html_compiled = html_compiled.replace(re, '');

    // ADD ABILITY TO REG EXP OUT THE INPUT FILE

    return html_compiled;
}

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

    $("#campaign-form-container").empty().append(addCustomerGroupTemplate(group));

    if (!id)
        $('input[name=name]').focus();

    CKEDITOR.replace('description');

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

        if (self.val() === "") self.closest('fieldset').find('#certificate_id').empty();

        var certificate_dropdown = self.closest('fieldset').find('#certificate_id');

        certificate_dropdown.html(certificatesTemplate({
            certificates: window.agencies[self.val()].certificates
        }));
        certificate_dropdown.select2("val", "");
    });

    $('#add-certificates').on('click', '.add-certificate', function(event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)
        var self = $(this);
        var agency_dropdown = self.closest('fieldset').find('#agency_id');
        var certificate_dropdown = self.closest('fieldset').find('#certificate_id');

        if (agency_dropdown.val() === "" || certificate_dropdown.val() === "") return false;

        if (certificate_dropdown.val() === 'all') {
            $('#campaign-form-container').find('#selected-rules').append(selectedAgencyTemplate({
                id: agency_dropdown.val(),
                abbreviation: window.agencies[agency_dropdown.val()].abbreviation
            }));
        } else {
            $('#campaign-form-container').find('#selected-rules').append(selectedCertificateTemplate({
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
        var ticket_dropdown = self.closest('fieldset').find('#ticket_id');

        if (ticket_dropdown.val() === "") return false;

        $('#campaign-form-container').find('#selected-rules').append(selectedTicketTemplate({
            id: ticket_dropdown.val(),
            name: window.tickets[ticket_dropdown.val()].name,
        }));
    });

    $('#add-classes').on('click', '.add-class', function(event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)
        var self = $(this);
        var class_dropdown = self.closest('fieldset').find('#class_id');

        if (class_dropdown.val() === "") return false;

        $('#campaign-form-container').find('#selected-rules').append(selectedClassTemplate({
            id: class_dropdown.val(),
            name: window.trainings[class_dropdown.val()].name,
        }));
    });

    if (id) {

        $('#add-certificates').find('#selected-rules').empty();
        _.each(window.groups[id].rules, function(rule) {
            if (rule.certificate_id !== null) {
                $('#campaign-form-container').find('#selected-rules').append(selectedCertificateTemplate({
                    id: rule.certificate_id,
                    abbreviation: window.certificates[rule.certificate_id].agency.abbreviation,
                    name: window.certificates[rule.certificate_id].name,
                }));
            } else if (rule.ticket_id !== null) {
                $('#campaign-form-container').find('#selected-rules').append(selectedTicketTemplate({
                    id: rule.ticket_id,
                    name: window.tickets[rule.ticket_id].name
                }));
            } else if (rule.training_id !== null) {
                $('#campaign-form-container').find('#selected-rules').append(selectedClassTemplate({
                    id: rule.training_id,
                    name: window.trainings[rule.training_id].name
                }));
            } else if (rule.agency_id !== null) {
                $('#campaign-form-container').find('#selected-rules').append(selectedAgencyTemplate({
                    id: rule.agency_id,
                    abbreviation: window.agencies[rule.agency_id].abbreviation
                }));
            }
        });

    }

    $('#campaign-form-container').on('click', '.remove-certificate', function() {
        $(this).parent().remove();
    });

    $('#campaign-form-container').on('click', '.remove-ticket', function() {
        $(this).parent().remove();
    });

    $('#campaign-form-container').on('click', '.remove-class', function() {
        $(this).parent().remove();
    });

    $(".return-campaign").remove();
    $("#customer-group-container").append('<button class="btn btn-primary return-campaign pull-right">Return to Campaigns</button>');

    $(".return-campaign").on('click', function(event) {
        event.preventDefault();
        renderCampaignTable();
    });

}

function unsavedChanges() {
    return $('form').data('hasChanged');
}

function renderGroupList(callback) {

    $('#customer-group-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

    CustomerGroup.getAll(function success(data) {

        window.groups = _.indexBy(data, 'id');
        $('#customer-group-container .loader').remove();

        $("#customer-group-list").empty().append(customerGroupListTemplate({
            groups: data
        }));

        // (Re)Assign eventListener for addon clicks
        $('#customer-group-list').on('click', 'li, strong', function(event) {

            if ($(event.target).is('strong'))
                event.target = event.target.parentNode;

            renderGroupEditForm(event.target.getAttribute('data-id'));
        });

        if (typeof callback === 'function')
            callback();
    });

    $("#add-customer-group").on('click', function(event) {
        event.preventDefault();
        renderGroupEditForm();
    });
}