<!DOCTYPE html>
<html>

<head>

    <title>scubawhereRMS | Dashboard</title>

    <!-- favicon -->
    <link rel="icon" type="image/ico" href="../common/favicon.ico" />

    <!--Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="../common/css/bootstrap-scubawhere.css" />

    <!-- scubawhere styles -->
    <link rel="stylesheet" type="text/css" href="../common/css/universal-styles.css" />
    <link rel="stylesheet" type="text/css" href="../css/style.css" />

    <!-- Plugins -->
    <link rel='stylesheet' type="text/css" href='../common/css/fullcalendar.min.css' />
    <link rel="stylesheet" type="text/css" href="../common/css/jquery.reveal.css" />
    <link rel="stylesheet" type="text/css" href="../common/vendor/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../common/css/bootstrap-datetimepicker.css" />
    <link rel="stylesheet" type="text/css" href="../common/css/select2.css" />
    <link rel="stylesheet" type="text/css" href="../common/css/select2-bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../common/vendor/nprogress/nprogress.css" />
    <link rel="stylesheet" type="text/css" href="../common/css/bootstrap.datatables.css" />
    <link rel="stylesheet" type="text/css" href="../common/vendor/datatables-tabletools/css/dataTables.tableTools.css" />

    <!-- jQuery -->
    <script src="../common/js/jquery/jquery.min.js"></script>

    <!--Bootstrap js-->
    <script type="text/javascript" src="../common/bootstrap/js/bootstrap.min.js"></script>

    <!-- other -->
    <script type="text/javascript" src="../common/js/handlebars.min.js"></script>
    <script type="text/javascript" src="../common/js/underscore-min.js"></script>
    <script type="text/javascript" src="../common/js/jquery/jquery.serialize-object.min.js"></script>
    <script type="text/javascript" src="../common/js/jquery/jquery.reveal.js"></script>
    <script type="text/javascript" src="../common/js/moment.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>

    <!-- scubawhere files -->
    <script type="text/javascript">
        // Set scubawhere namespace
        window.sw = {};
        window.promises = {};
    </script>

    <script type="text/javascript" src="/js/main.js"></script>
    <script type="text/javascript" src="/js/ui.js"></script>

    <!-- Controllers -->
    <script type="text/javascript" src="../js/Controllers/Campaign.js"></script>
    <script type="text/javascript" src="../js/Controllers/CustomerGroup.js"></script>
    <script type="text/javascript" src="../js/Controllers/Company.js"></script>

    <!--Datetimepicker-->
    <script type="text/javascript" src="../common/js/bootstrap-datetimepicker.min.js"></script>

    <!--Select 2-->
    <script type="text/javascript" src="../common/js/select2.min.js"></script>

    <!--nprogress bar-->
    <script type="text/javascript" src="../common/vendor/nprogress/nprogress.js"></script>

    <meta http-equiv="X-UA-Compatible" content="IE=9">

</head>

<body>
    <div id="nav">
        <div id="nav-wrapper">
            <h1 id="logo"><a href="/"><img src="/common/img/Scubawhere_logo.png"></a></h1>

            <button class="btn btn-default pull-right" id="logout">Logout</button>

            <div class="nav-opt pull-right">
                <a href="#settings" class="username"></a>
            </div>

        </div>
    </div>

    <!-- Container for page messages -->
    <div id="pageMssg"></div>

    <div id="modalWindows" style="height: 0;"></div>

    <iframe id="email-template-editor" style="position: fixed; border: none; top: 50px; right: 0; bottom: 0; left: 0; width: 100%; height: 100%; padding-bottom: 120px"></iframe>

    <div style="position: fixed; bottom: 0; width: 100%; height:80px" class="alert-warning clearfix">
        <div class="row">
            <div class="col-md-8 alert pull-right">
                <div class="pull-right">
                    <button id="send-email" class="btn btn-primary btn-lg pull-right">SEND</button>
                    <button id="save-as-template" class="btn btn-success btn-lg pull-right" style="margin-right: 15px;">Save as Template</button>
                    <button id="update-template" class="btn btn-success btn-lg pull-right" style="margin-right: 15px; display:none;">Update Template</button>
                    <a href="/#campaigns" class="btn btn-default btn-lg abandon-booking pull-right" style="margin-right: 15px;">Discard Email</a>
                    <!--<button id="choose-template" class="btn btn-primary btn-lg pull-left" style="margin-right: 15px;">Choose Different Template</button>-->
                </div>
            </div>
            <div class="col-md-4 pull-left">
                <p style="padding: 30px 0 0 30px"><strong>Please note : Maximum upload image size is 500 KB and only supports PNG and JPG file types</strong></p>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="select-email-template-modal">
        <div class="modal-dialog" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Choose Template</h4>
                </div>
                <div class="modal-body">
                    <div id="template-types" class="btn-group" style="margin-bottom:10px" role="group">
                        <button display="layout-options" type="button" class="btn btn-default btn-primary option-button">Example Layouts</button>
                        <button display="template-options" type="button" class="btn btn-default option-button">My Saved Templates</button>
                    </div>
                    <!-- loop through templates-->
                    <div style="margin: 0 auto;">
                        <div id="template-options" class="email-options-list" style="width:35%; float:left; max-height:620px; overflow: auto; display:none;">
                            <ul id="layout-options-list" style="margin-right:10px" class="entity-list"></ul>
                        </div>
                        <div id="layout-options" style="width:35%; float:left; max-height:620px; overflow: auto" class="email-options-list">
                            <img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_2column_query.html" src="/img/email-templates/email_template_1.jpg">
                            <img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_3column_query.html" src="/img/email-templates/email_template_2.jpg">
                            <img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_basic_body_image_query.html" src="/img/email-templates/email_template_3.jpg">
                            <img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_basic_query.html" src="/img/email-templates/email_template_4.jpg">
                            <img width="45%" height="200px" class="email-template-option" data-url="/tabs/campaigns/email-templates/base_boxed_body_image_2column_query.html" src="/img/email-templates/email_template_5.jpg">
                        </div>
                        <div style="width:65%; float:left">
                            <h4>Preview:</h4>
                            <iframe id="email-template-option-preview" width="100%" height="600px" style="border:none"></iframe>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger remove-template">Delete Template</button>
                        <button id="select-email-template" type="submit" class="btn btn-primary">Use Template</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="save-email-template-modal">
        <div class="modal-dialog" style="width:40%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Save Template</h4>
                </div>
                <div style="height:240px" class="modal-body">
                    <label for="template-name" class="control-label">Template name :</label>
                    <input type="text" id="template-name" class="form-control">
                    <div style="margin-top:10px; margin-bottom:10px;" class="alert alert-warning">
                        <p><strong>Where to find your templates</strong></p>
                        <p>Once saved, you can access and edit this template by clicking select email template, then go to 'My templates'</p>
                    </div>
                    <button id="btn-save-template" style="margin-top:10px" class="btn btn-primary btn-lg text-uppercase pull-right">SAVE</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="send-email-modal">
        <div class="modal-dialog" style="width:60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Send Email</h4>
                </div>
                <div class="modal-body">
                    <form id="create-campaign-form" accept-charset="utf-8">
                        <div class="form-row">
                            <label class="field-label">Campaign name</label>
                            <input id="campaign-name" type="text" name="name" style="width:100%">
                        </div>
                        <fieldset id="add-customer-group-to-campaign">
                            <div class="form-group">
                                <div class="col-md-10">
                                    <label for="customer_group_id" class="control-label">Which groups are you sending this to?</label>
                                    <select id="customer_group_id" class="form-control select2">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <br>
                                    <button class="btn btn-primary add-group" style="width: 100%;">Add</button>
                                </div>
                                <div style="padding-top:10px;" class="col-md-12">
                                    <input id="select-all-customers" type="checkbox">Send all customers
                                    <input id="send-all-customers" type="hidden" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" style="padding-top:10px" id="selected-customer-groups">
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-row">
                            <label class="field-label">Email Subject</label>
                            <input id="email_subject" type="text" name="subject" style="width:100%">
                        </div>
                        <button id="cancel-email" class="btn btn-danger btn-lg text-uppercase">CANCEL</button>
                        <input type="submit" class="btn btn-success btn-lg text-uppercase pull-right" value="SEND">
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Handlebars templates -->
    <script type="text/x-handlebars-template" id="layout-options-list-template">
        {{#each layout}}
            <li class="email-layout-option" data-id="{{id}}" data-html="{{html_string}}">{{name}}</li>
        {{/each}}
    </script>

    <script type="text/x-handlebars-template" id="group-select-template">
        <option value="">Choose group...</option>
        {{#each groups}}
        <option value="{{id}}">{{name}}</option>
        {{/each}}
    </script>

    <script type="text/x-handlebars-template" id="selected-group-template">
        <div class="pull-left selected-certificate">
            <input type="checkbox" name="groups[]" value="{{id}}" style="position: absolute; top: 0; left: -9999px;" checked="checked">
            <strong>{{{name}}}</strong>
            <i class="fa fa-times remove-group" style="cursor: pointer;"></i>
        </div>
    </script>

</body>

</html>
