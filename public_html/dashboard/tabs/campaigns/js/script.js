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
    groupSelectTemplate = Handlebars.compile($("#group-select-template").html());
    selectedCustomerGroupTemplate = Handlebars.compile($("#selected-group-template").html());
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
                    CustomerGroup.getAll(function success(data) {
                        window.groups = _.indexBy(data, 'id');
                        renderCampaignTable();
                    },
                    function error(xhr) {

                    });
                });
            });
        });
    });

});

function renderCampaignTable() {
    Campaign.getAll(function sucess(data) {
        console.log(data);
        $("#campaign-form-container").empty().append(viewCampaignsTemplate({
            campaigns: data
        }));
    });
    $("#campaign-form-container").on('click', '#create-campaign', function(event) {
        event.preventDefault();
        renderCampaignForm();
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
    });

    $('.return-campaigns').on('click', function(event) {
        event.preventDefault();
        $('#campaign-container').empty().append(campaignsGroupsTemplate());
        renderCampaignTable();
    });

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
