
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

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
var selected_email_template;
var optionListTemplate;
var campaignAnalyticsTable;

Handlebars.registerHelper('listGroups', function(groups) {
    if(groups.length == 0 || groups == undefined || groups == null) return "All customers";
    else {
        var groups_html = "";
        for(var i=0; i < groups.length; i++) {
            groups_html += '<span style="margin-right:5px;">' + groups[i].name + '</span>';
        }
        return new Handlebars.SafeString(groups_html);
    }
});

$(function () {

    viewCampaignsTemplate = Handlebars.compile($("#campaigns-template").html());
    groupSelectTemplate = Handlebars.compile($("#group-select-template").html());
    selectedCustomerGroupTemplate = Handlebars.compile($("#selected-group-template").html());
    createCampaignTemplate = Handlebars.compile($("#create-campaign-template").html());
    optionListTemplate = Handlebars.compile($("#layout-options-list-template").html());

    campaignAnalyticsTable = $('#campaign-analytics-table').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     false,
        "columnDefs" : [
            {
                "targets" : [3],
                "visible" : false
            }
        ]
    });

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
                        console.log(xhr);
                    });
                });
            });
        });
    });

});

function renderCampaignTable() {
    Campaign.getAll(function sucess(data) {
        console.log(data);
        $("#campaign-container").empty().append(viewCampaignsTemplate({
            campaigns: data
        }));
        $('.view-email-campaign').on('click', function() {
            showEmailBrowser($(this).attr('data-html'));
        });
        $('.view-email-analytics').on('click', function() {
            showEmailAnalytics($(this).attr('data-campaign-id'));
        });
        $('.resend-email-campaign').on('click', function() {
            var params = {};
            params.id = $(this).attr('data-campaign-id');
            Campaign.get(params, function success(data) {
                $('#resend-email-modal').modal('show');
                console.log(data);
                var new_params= {};
                new_params.email_html = data.email_html;
                new_params.groups = [];
                for(i in data.groups)
                {
                   new_params.groups.push(data.groups[i].id);
                }
                $('#btn-resend-campaign').on('click', function(event) {
                    event.preventDefault();
                    new_params.subject = $('#resend-email-subject').val();
                    new_params.name = $('#resend-campaign-name').val();
					new_params.is_campaign = 1;
					new_params.sendallcustomers = 0;
                    new_params._token = window.token;
                    sendCampaign(new_params);
                    renderCampaignTable();
                    $('#resend-email-modal').modal('hide');
                });
            });
        });
        $('.delete-email-campaign').on('click', function(event) {
            var params = {};
            params.id = $(this).attr('data-campaign-id');
            params._token = window.token;
            Campaign.delete(params, function(data) {
                pageMssg(data.status, true);
                renderCampaignTable();
            },
            function error(xhr) {
                console.log(xhr);
                pageMssg('Campaign could not be deleted', false);
            });
        });
    });
    $("#campaign-container").on('click', '#create-campaign', function(event) {
        event.preventDefault();
        renderCampaignForm();
    });
}

function isCustomerUnsubscribed(customer, campaign_id)
{
    return (customer.unsubscribed_campaign_id == campaign_id) ? "Unsubscribed" : "";
}

function showEmailAnalytics(id) {
    $('#show-email-analytics-modal').modal('show');
    var params = {};
    params.id = id;
    Campaign.getAnalytics(params, function success(data) {
        console.log('analytics', data);
        campaignAnalyticsTable.clear();
        var opened_date;
        for(i in data.analytics) {
			if(data.analytics[i].opened_time === null)
			{
				opened_date = 'Not Opened Yet.';
			}
			else
			{
				opened_date = data.analytics[i].opened_time;
			}
            campaignAnalyticsTable.row.add([
                data.analytics[i].customer.firstname + ' ' + data.analytics[i].customer.lastname,
                data.analytics[i].customer.email,
                data.analytics[i].opened,
                data.analytics[i].customer.id,
                isCustomerUnsubscribed(data.analytics[i].customer.crm_subscription, data.analytics[i].campaign_id),
                opened_date
            ]);
        }
        campaignAnalyticsTable.draw(); // how to order ???
        $('#total-emails-seen').html(data.total_seen + ' emails viewed');
        $('#total-emails-sent').html(data.total_sent + ' emails sent');
        $('#average-open-rate').html(parseInt((data.total_seen / data.total_sent) * 100) + ' %' + ' Avg Opened Rate');
        //$('#average-click-rate').html(parseInt((data.num_links_clicked/ data.total_sent) * 100) + ' % Avg Click Rate');
        $('#num-unsubscribed').html(data.num_unsubscriptions + ' No. of unsubscribes');

        $('#campaign-analytics-table tr').on('click', function () {
            console.log('clicke');
            var tr = $(this).closest('tr');
            var row = campaignAnalyticsTable.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else 
			{
                // Open this row
				row.child(getLinkAnalytics(row.data()[3], data.link_analytics)).show();
				tr.addClass('shown');
				/*if(data.link_analytics.length > 1)
				{
					row.child(getLinkAnalytics(row.data()[3], data.link_analytics)).show();
                	tr.addClass('shown');
				}*/
            }
        });
    },
    function error(xhr) {
        console.log(xhr);
        pageMssg(xhr.responseText, false);
    });
}

function getLinkAnalytics (customer_id, data) {
    var accordian = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"><tr><p><strong>Link Analytics</strong></p></tr>';
    var customer_clicks = 0;
	console.log(data);
    for(var i = 0; i < data.length; i++)
    {
        customer_clicks = 0;
        for(var j in data[i].analytics)
        {
            if(data[i].analytics[j].customer_id == customer_id) {
                customer_clicks = data[i].analytics[j].count;
                break;
            }
        }
        accordian += '<tr><p><a target="_blank" href="'+data[i].link+'">'+data[i].link+'</a> : '+customer_clicks+' clicks</p></tr>'
    }
    accordian += '</table>';
    return accordian;
}

function showEmailBrowser(html) {
    var w = window.open('');
    w.document.write(html);
    w.document.close();
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
        showEmailBrowser(processEmailHtml());
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

    $('#save-as-template').on('click', function(event) {
        event.preventDefault();
        $('#save-email-template-modal').modal('show');
        $('#btn-save-template').on('click', function(event) {
            event.preventDefault();
            var params = {};
            params.html_string = document.getElementById("email-template-editor").contentWindow.document.documentElement.outerHTML;
            params.name = $('#template-name').val();
            params._token = window.token;
            Campaign.createTemplate(params, function success(data) {
                console.log(data);
                pageMssg(data.status, true);
                $('#template-name').val('');
                $('#save-email-template-modal').modal('hide');
                renderCampaignTable();
            },
            function error(xhr) {
                console.log(xhr);
                pageMssg(xhr.responseText, false);
            });
        });
    });

    $('#select-campaign-template').on('click', function(event) {
        event.preventDefault();
        $('#select-email-template-modal').modal('show');
        var email_template_html = '';
        var email_preview_frame = document.getElementById('email-template-option-preview');
        var email_editor_frame = document.getElementById('email-template-editor');
        var using_layout = true;
        var layout_html_string = '';

        $('.option-button').on('click', function() {
            $('.email-template-option').css('border', 'none');
            $('.email-options-list').css('display', 'none');
            $('#' + $(this).attr('display')).css('display', 'block');
            $('.option-button').removeClass('btn-primary');
            $(this).addClass('btn-primary');
            Campaign.getAllTemplates(function success(data) {
                console.log(data);
                $('#layout-options-list').empty().append(optionListTemplate({layout:data}));
                $('.email-layout-option').on('click', function() {
                    layout_html_string = $(this).attr('data-html');
                    email_template_html = processEmailHtml(layout_html_string);
                    email_preview_frame.contentWindow.document.open();
                    email_preview_frame.contentWindow.document.write(email_template_html);
                    email_preview_frame.contentWindow.document.close();
                });
            });
        });

        $('.email-template-option').on('click', function() {
            $('.email-template-option').css('border', 'none');
            $(this).css('border', '3px solid #FF7163');
            $.get($(this).attr('data-url'), function(response) {
                email_template_html = processEmailHtml(response);
                email_preview_frame.contentWindow.document.open();
                email_preview_frame.contentWindow.document.write(email_template_html);
                email_preview_frame.contentWindow.document.close();
                using_layout = false;
            });
            selected_email_template = $(this).attr('data-url');
        });

        $('#select-email-template').on('click', function() {
            $('#save-as-template').removeAttr('disabled');
            $('#email-editor-tips').css('display', 'none');
            $('#email-template-editor').css('display', 'inline');
            $('#show-email-browser').css('display', 'inline');
            if(using_layout) {
                email_editor_frame.contentWindow.document.open();
                email_editor_frame.contentWindow.document.write(layout_html_string);
                email_editor_frame.contentWindow.document.close();
            }
            else $('#email-template-editor').attr('src', selected_email_template);
            $('#select-email-template-modal').modal('hide');
        });
    });

    $('.return-campaigns').on('click', function(event) {
        event.preventDefault();
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
            params.email_html = processEmailHtml();
            for (var i = 0; i < params.groups.length; i++) {
                params.groups[i] = parseInt(params.groups[i]);
            }
            sendCampaign(params);
        });

    }
}

function sendCampaign(params)
{
    Campaign.create(params, function success(data) {
        console.log(data);
        pageMssg(data.status, true);
        renderCampaignTable();
    },
    function error(xhr) {
        console.log(xhr);
        pageMssg(xhr.responseText, false);
    });
}

function processEmailHtml(html_string)
{

    if(!html_string) {
        var html_string = document.getElementById("email-template-editor").contentWindow.document.documentElement.outerHTML;
    }

    var script_pos = html_string.indexOf('<script type="text/javascript" src="/tabs/campaigns/email-templates/js/medium-editor.js"></script>');
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
