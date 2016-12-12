var optionListTemplate;
var groupSelectTemplate;
var selectedCustomerGroupTemplate;
var email_created = false;
window.token;
$(function () {
    
    optionListTemplate = Handlebars.compile($("#layout-options-list-template").html());
    groupSelectTemplate = Handlebars.compile($('#group-select-template').html());
    selectedCustomerGroupTemplate = Handlebars.compile($('#selected-group-template').html());
    
    var template_id;
    
    window.token = getToken();

    $('#save-as-template').on('click', function (event) {
        event.preventDefault();
        if(!email_created) {
            pageMssg('Please create an email to save it as a template', false);
            return false;
        }
        $('#save-email-template-modal').modal('show');
        $('#btn-save-template').on('click', function (event) {
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
                    window.location.href = "/#campaigns";
                },
                function error(xhr) {
                    console.log(xhr);
                    pageMssg(xhr.responseText, false);
                });
        });
    });
    
    $('#update-template').on('click', function (event) {
        event.preventDefault();
        if(!email_created) {
            pageMssg('Please create an email to save it as a template', false);
            return false;
        }
        if(confirm('Are you sure you want to update this template ?')) {
            var params = {};
            params.html_string = document.getElementById("email-template-editor").contentWindow.document.documentElement.outerHTML;
            params.template_id = template_id;
            params._token = window.token;
            Campaign.updateTemplate(params, function success(data) {
                console.log(data);
                pageMssg(data.status, true);
                //$('#update-email-template-modal').modal('hide');
                //window.location.href = "/#campaigns";
            },
            function error(xhr) {
                console.log(xhr);
                pageMssg(xhr.responseText, false);
            });
        }
    });

    $('#send-email').on('click', function (event) {
        event.preventDefault();
        if(!email_created) {
            pageMssg('Please create an email before trying to send', false);
            return false;
        }
        $('#send-email-modal').modal('show');
        CustomerGroup.getAll(function success(data) {
            window.groups = _.indexBy(data, 'id');
            $("#add-customer-group-to-campaign").find('#customer_group_id').empty().append(groupSelectTemplate({
                groups: data
            }));
        });
    });
    
    $('#cancel-email').on('click', function(event) {
        event.preventDefault();
        $('#send-email-modal').modal('hide');
    });

    /*$('#choose-template').on('click', function (event) {
        event.preventDefault();
        showTemplates();
    });*/

    var email_template_html = '';
    var email_preview_frame = document.getElementById('email-template-option-preview');
    var email_editor_frame = document.getElementById('email-template-editor');
    var using_layout = true;
    var layout_html_string = '';

    $('.option-button').on('click', function () {
        $('#update-template').css('display', 'inline');
        $('.email-template-option').css('border', 'none');
        $('.email-options-list').css('display', 'none');
        $('#' + $(this).attr('display')).css('display', 'block');
        $('.option-button').removeClass('btn-primary');
        $(this).addClass('btn-primary');
        $('.email-layout-option').on('click', function () {
            template_id = $(this).attr('data-id');
            layout_html_string = $(this).attr('data-html');
            email_template_html = processEmailHtml(layout_html_string);
            email_preview_frame.contentWindow.document.open();
            email_preview_frame.contentWindow.document.write(email_template_html);
            email_preview_frame.contentWindow.document.close();
        });
    });

    $('.email-template-option').on('click', function () {
        $('#update-template').css('display', 'none');
        $('.email-template-option').css('border', 'none');
        $(this).css('border', '3px solid #FF7163');
        $.get($(this).attr('data-url'), function (response) {
            email_template_html = processEmailHtml(response);
            email_preview_frame.contentWindow.document.open();
            email_preview_frame.contentWindow.document.write(email_template_html);
            email_preview_frame.contentWindow.document.close();
            using_layout = false;
        });
        selected_email_template = $(this).attr('data-url');
    });

    $('#select-email-template').on('click', function () {
        $('#save-as-template').removeAttr('disabled');
        $('#email-editor-tips').css('display', 'none');
        $('#email-template-editor').css('display', 'inline');
        $('#show-email-browser').css('display', 'inline');
        if (using_layout) {
            email_editor_frame.contentWindow.document.open();
            email_editor_frame.contentWindow.document.write(layout_html_string);
            email_editor_frame.contentWindow.document.close();
        } else $('#email-template-editor').attr('src', selected_email_template);
        $('#select-email-template-modal').modal('hide');
        email_created = true;
    });
    
    $('#add-customer-group-to-campaign').on('click', '.add-group', function(event) {
        event.preventDefault(); // Prevent form submission (some browsers treat any <button> press in a form as a submit)

        var self = $(this);
        var group_dropdown = self.closest('fieldset').find('#customer_group_id');
        console.log(group_dropdown.val());

        if (group_dropdown.val() === "") return false;

        self.closest('fieldset').find('#selected-customer-groups').append(selectedCustomerGroupTemplate({
            id: group_dropdown.val(),
            name: window.groups[group_dropdown.val()].name
        }));
    });

    $('#add-customer-group-to-campaign').on('click', '.remove-group', function() {
        $(this).parent().remove();
    });
    
    $('#create-campaign-form').on('submit', function(event) {
        event.preventDefault();
        var params = $(this).serializeObject();
        params.email_html = processEmailHtml();
        params._token = window.token;
        params.is_campaign = 1;
        params.sendallcustomers = $('#send-all-customers').val();
        if(params.groups != undefined) {
            for (var i = 0; i < params.groups.length; i++) {
                params.groups[i] = parseInt(params.groups[i]);
            }
        }
        sendCampaign(params);
    });
    
    $('#select-all-customers').on('change', function() {
        if($(this).is(':checked')) {
            $('.remove-group').parent().remove();
            $('#customer_group_id').attr('disabled', true);
            $('#send-all-customers').val(1);
        }
        else {
            $('#customer_group_id').attr('disabled', false);
            $('#send-all-customers').val(0);
        }
    });

    showTemplates();

    $('.remove-template').on('click', function () {
        if(template_id !== undefined) {
            Campaign.deleteTemplate(template_id, function (res) {
                pageMssg('Success. Your template has been deleted', 'success');
                showTemplates();
                email_preview_frame.contentWindow.document.open();
                email_preview_frame.contentWindow.document.write('');
                email_preview_frame.contentWindow.document.close();
            },
            function (xhr) {
                pageMssg('Oh oh. For some reason we cannot fufill your request right now. Please try again later.', 'danger');
            });
        }
    });

});

function sendCampaign(params)
{
    Campaign.create(params, function success(data) {
        console.log(data);
        pageMssg(data.status, true);
        window.location.href = '/#campaigns';
    },
    function error(xhr) {
        console.log(xhr);
        pageMssg(xhr.responseText, false);
    });
}

function showTemplates() 
{
    Campaign.getAllTemplates(function success(data) {
        console.log(data);
        $('#layout-options-list').empty().append(optionListTemplate({
            layout: data
        }));
        $('#select-email-template-modal').modal('show');
    });
}

function processEmailHtml(html_string) 
{

    if (!html_string) {
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
