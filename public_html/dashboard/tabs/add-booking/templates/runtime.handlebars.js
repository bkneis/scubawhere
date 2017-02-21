(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['override-price-form'] = template({"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<label class=\"field-label\">New Price : </label>\n<div class=\"input-group\">\n    <span class=\"input-group-addon\"><i class=\"fa fa-money\"></i></span>\n    <input id=\"override-price\"\n           type=\"number\"\n           placeholder=\"00.00\"\n           min=\"0\"\n           data-booking-id=\""
    + alias4(((helper = (helper = helpers.bookingId || (depth0 != null ? depth0.bookingId : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"bookingId","hash":{},"data":data}) : helper)))
    + "\"\n           data-booking-detail-id=\""
    + alias4(((helper = (helper = helpers.bookingDetailId || (depth0 != null ? depth0.bookingDetailId : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"bookingDetailId","hash":{},"data":data}) : helper)))
    + "\"\n           data-type=\""
    + alias4(((helper = (helper = helpers.type || (depth0 != null ? depth0.type : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"type","hash":{},"data":data}) : helper)))
    + "\"\n           data-id=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"\n           data-start=\""
    + alias4(((helper = (helper = helpers.start || (depth0 != null ? depth0.start : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"start","hash":{},"data":data}) : helper)))
    + "\"\n           step=\"0.01\"/>\n</div>\n";
},"useData":true});
templates['price-breakdown'] = template({"1":function(container,depth0,helpers,partials,data) {
    return "                                <td class=\"title-dark\" width=\"20\">\n                                    Commissioned\n                                </td>\n";
},"3":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "                            <tr>\n"
    + ((stack1 = helpers["if"].call(alias1,(depths[1] != null ? depths[1].agent_id : depths[1]),{"name":"if","hash":{},"fn":container.program(4, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "                                <td class=\"inner-item-col\">\n                                    <i class=\"fa fa-tags fa-fw\"></i> "
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right;\">\n                                    x1\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right; padding-right: 20px;\">\n                                    "
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.item_price || (depth0 != null ? depth0.item_price : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"item_price","hash":{},"data":data}) : helper)))
    + "\n                                </td>\n                            </tr>\n";
},"4":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var helper, alias1=container.escapeExpression, alias2=depth0 != null ? depth0 : {}, alias3=helpers.helperMissing, alias4="function";

  return "                                    <td class=\"inner-item-col\">\n                                        <input type=\"checkbox\"\n                                               data-type=\"package\"\n                                               data-booking-id=\""
    + alias1(container.lambda((depths[1] != null ? depths[1].id : depths[1]), depth0))
    + "\"\n                                               data-booking-detail-id=\""
    + alias1(((helper = (helper = helpers.bookingdetail_id || (depth0 != null ? depth0.bookingdetail_id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"bookingdetail_id","hash":{},"data":data}) : helper)))
    + "\"\n                                               data-id=\""
    + alias1(((helper = (helper = helpers.facade_id || (depth0 != null ? depth0.facade_id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"facade_id","hash":{},"data":data}) : helper)))
    + "\"\n                                               class=\"itemCommissionable\"\n                                            "
    + alias1((helpers.checkIf || (depth0 && depth0.checkIf) || alias3).call(alias2,(depth0 != null ? depth0.isCommissioned : depth0),{"name":"checkIf","hash":{},"data":data}))
    + " />\n                                    </td>\n";
},"6":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "                            <tr>\n"
    + ((stack1 = helpers["if"].call(alias1,(depths[1] != null ? depths[1].agent_id : depths[1]),{"name":"if","hash":{},"fn":container.program(7, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "                                <td class=\"inner-item-col\">\n                                    <i class=\"fa fa-graduation-cap fa-fw\"></i> "
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right;\">\n                                    x1\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right; padding-right: 20px;\">\n                                    "
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.item_price || (depth0 != null ? depth0.item_price : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"item_price","hash":{},"data":data}) : helper)))
    + "\n                                </td>\n                            </tr>\n";
},"7":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var helper, alias1=container.escapeExpression, alias2=depth0 != null ? depth0 : {}, alias3=helpers.helperMissing, alias4="function";

  return "                                    <td class=\"inner-item-col\">\n                                        <input type=\"checkbox\"\n                                               data-type=\"course\"\n                                               data-booking-id=\""
    + alias1(container.lambda((depths[1] != null ? depths[1].id : depths[1]), depth0))
    + "\"\n                                               data-booking-detail-id=\""
    + alias1(((helper = (helper = helpers.bookingdetail_id || (depth0 != null ? depth0.bookingdetail_id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"bookingdetail_id","hash":{},"data":data}) : helper)))
    + "\"\n                                               data-id=\""
    + alias1(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"\n                                               class=\"itemCommissionable\"\n                                            "
    + alias1((helpers.checkIf || (depth0 && depth0.checkIf) || alias3).call(alias2,(depth0 != null ? depth0.isCommissioned : depth0),{"name":"checkIf","hash":{},"data":data}))
    + " />\n                                    </td>\n";
},"9":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "                            <tr>\n"
    + ((stack1 = helpers["if"].call(alias1,(depths[1] != null ? depths[1].agent_id : depths[1]),{"name":"if","hash":{},"fn":container.program(10, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "                                <td class=\"inner-item-col\">\n                                    <i class=\"fa fa-ticket fa-fw\"></i> "
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right;\">\n                                    x1\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right; padding-right: 20px;\">\n                                    "
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.item_price || (depth0 != null ? depth0.item_price : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"item_price","hash":{},"data":data}) : helper)))
    + "\n                                </td>\n                            </tr>\n";
},"10":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var helper, alias1=container.escapeExpression, alias2=depth0 != null ? depth0 : {}, alias3=helpers.helperMissing, alias4="function";

  return "                                    <td class=\"inner-item-col\">\n                                        <input type=\"checkbox\"\n                                               data-type=\"ticket\"\n                                               data-booking-id=\""
    + alias1(container.lambda((depths[1] != null ? depths[1].id : depths[1]), depth0))
    + "\"\n                                               data-booking-detail-id=\""
    + alias1(((helper = (helper = helpers.bookingdetail_id || (depth0 != null ? depth0.bookingdetail_id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"bookingdetail_id","hash":{},"data":data}) : helper)))
    + "\"\n                                               data-id=\""
    + alias1(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"\n                                               class=\"itemCommissionable\"\n                                               "
    + alias1((helpers.checkIf || (depth0 && depth0.checkIf) || alias3).call(alias2,(depth0 != null ? depth0.isCommissioned : depth0),{"name":"checkIf","hash":{},"data":data}))
    + " />\n                                    </td>\n";
},"12":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "                            <tr>\n"
    + ((stack1 = helpers["if"].call(alias1,(depths[1] != null ? depths[1].agent_id : depths[1]),{"name":"if","hash":{},"fn":container.program(13, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "                                <td class=\"inner-item-col\">\n                                    <i class=\"fa fa-cart-plus fa-fw\"></i> "
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right;\">\n                                    x"
    + alias4(((helper = (helper = helpers.qtySummary || (depth0 != null ? depth0.qtySummary : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"qtySummary","hash":{},"data":data}) : helper)))
    + "\n                                </td>\n                                <td class=\"inner-item-col\" style=\"text-align: right; padding-right: 20px;\">\n                                    "
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.item_price || (depth0 != null ? depth0.item_price : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"item_price","hash":{},"data":data}) : helper)))
    + "\n                                </td>\n                            </tr>\n";
},"13":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var helper, alias1=container.escapeExpression, alias2=depth0 != null ? depth0 : {}, alias3=helpers.helperMissing, alias4="function";

  return "                                    <td class=\"inner-item-col\">\n                                        <input type=\"checkbox\"\n                                               data-type=\"addon\"\n                                               data-booking-id=\""
    + alias1(container.lambda((depths[1] != null ? depths[1].id : depths[1]), depth0))
    + "\"\n                                               data-booking-detail-id=\""
    + alias1(((helper = (helper = helpers.bookingdetail_id || (depth0 != null ? depth0.bookingdetail_id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"bookingdetail_id","hash":{},"data":data}) : helper)))
    + "\"\n                                               data-id=\""
    + alias1(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias3),(typeof helper === alias4 ? helper.call(alias2,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"\n                                               class=\"itemCommissionable\"\n                                            "
    + alias1((helpers.checkIf || (depth0 && depth0.checkIf) || alias3).call(alias2,(depth0 != null ? depth0.isCommissioned : depth0),{"name":"checkIf","hash":{},"data":data}))
    + " />\n                                    </td>\n";
},"15":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1;

  return ((stack1 = helpers.unless.call(depth0 != null ? depth0 : {},((stack1 = (depth0 != null ? depth0.pivot : depth0)) != null ? stack1.packagefacade_id : stack1),{"name":"unless","hash":{},"fn":container.program(16, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "");
},"16":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "                                <tr>\n"
    + ((stack1 = helpers["if"].call(alias1,(depths[1] != null ? depths[1].agent_id : depths[1]),{"name":"if","hash":{},"fn":container.program(17, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "                                    <td class=\"inner-item-col\">\n                                        <i class=\"fa fa-bed fa-fw\"></i> "
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\n                                    </td>\n                                    <td class=\"inner-item-col\" style=\"text-align: right;\">\n                                        "
    + alias4((helpers.numberOfNights || (depth0 && depth0.numberOfNights) || alias2).call(alias1,((stack1 = (depth0 != null ? depth0.pivot : depth0)) != null ? stack1.start : stack1),((stack1 = (depth0 != null ? depth0.pivot : depth0)) != null ? stack1.end : stack1),{"name":"numberOfNights","hash":{},"data":data}))
    + "\n                                    </td>\n                                    <td class=\"inner-item-col\" style=\"text-align: right; padding-right: 20px;\">\n                                        "
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.accommodation_item_price || (depth0 != null ? depth0.accommodation_item_price : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"accommodation_item_price","hash":{},"data":data}) : helper)))
    + "\n                                    </td>\n                                </tr>\n";
},"17":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=container.lambda, alias2=container.escapeExpression, alias3=depth0 != null ? depth0 : {}, alias4=helpers.helperMissing, alias5="function";

  return "                                        <td class=\"inner-item-col\">\n                                            <input type=\"checkbox\"\n                                                   data-type=\"accommodation\"\n                                                   data-booking-id=\""
    + alias2(alias1((depths[1] != null ? depths[1].id : depths[1]), depth0))
    + "\"\n                                                   data-booking-detail-id=\""
    + alias2(((helper = (helper = helpers.bookingdetail_id || (depth0 != null ? depth0.bookingdetail_id : depth0)) != null ? helper : alias4),(typeof helper === alias5 ? helper.call(alias3,{"name":"bookingdetail_id","hash":{},"data":data}) : helper)))
    + "\"\n                                                   data-start=\""
    + alias2(alias1(((stack1 = (depth0 != null ? depth0.pivot : depth0)) != null ? stack1.start : stack1), depth0))
    + "\"\n                                                   data-id=\""
    + alias2(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias4),(typeof helper === alias5 ? helper.call(alias3,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"\n                                                   class=\"itemCommissionable\"\n                                                "
    + alias2((helpers.checkIf || (depth0 && depth0.checkIf) || alias4).call(alias3,((stack1 = (depth0 != null ? depth0.pivot : depth0)) != null ? stack1.commissionable : stack1),{"name":"checkIf","hash":{},"data":data}))
    + " />\n                                        </td>\n";
},"19":function(container,depth0,helpers,partials,data) {
    return "                                <td class=\"item-col commissioned\" style=\"border-top: 1px solid #cccccc;\"></td>\n";
},"21":function(container,depth0,helpers,partials,data) {
    return "                                    <span class=\"total-space\">Credit Card Surcharge</span><br />\n";
},"23":function(container,depth0,helpers,partials,data) {
    return "                                    <span class=\"total-space\">Discount</span><br />\n";
},"25":function(container,depth0,helpers,partials,data) {
    var helper;

  return "                                    <span class=\"total-space\" style=\"font-weight: bold; color: #4d4d4d\">Gross</span><br />\n                                    <span class=\"total-space\">"
    + container.escapeExpression(((helper = (helper = helpers.commission_percentage || (depth0 != null ? depth0.commission_percentage : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"commission_percentage","hash":{},"data":data}) : helper)))
    + " Commission</span><br />\n                                    <span class=\"total-space\" style=\"font-weight: bold; color: #4d4d4d\">Net</span>\n";
},"27":function(container,depth0,helpers,partials,data) {
    return "                                    <span class=\"total-space\" style=\"font-weight: bold; color: #4d4d4d\">Total</span>\n";
},"29":function(container,depth0,helpers,partials,data) {
    var helper;

  return "                                    <span class=\"total-space\">"
    + container.escapeExpression(((helper = (helper = helpers.totalSurcharged || (depth0 != null ? depth0.totalSurcharged : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"totalSurcharged","hash":{},"data":data}) : helper)))
    + "</span><br />\n";
},"31":function(container,depth0,helpers,partials,data) {
    return "                                    <span class=\"total-space\">- "
    + container.escapeExpression((helpers.decimalise || (depth0 && depth0.decimalise) || helpers.helperMissing).call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.discount : depth0),{"name":"decimalise","hash":{},"data":data}))
    + "</span><br />\n";
},"33":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "                                    <span class=\"total-space\" style=\"font-weight: bold; color: #4d4d4d;\">"
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.decimal_price || (depth0 != null ? depth0.decimal_price : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"decimal_price","hash":{},"data":data}) : helper)))
    + "</span><br />\n                                    <span class=\"total-space\">- "
    + alias4(((helper = (helper = helpers.commission_amount || (depth0 != null ? depth0.commission_amount : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"commission_amount","hash":{},"data":data}) : helper)))
    + "</span><br />\n                                    <span style=\"font-weight: bold; color: #4d4d4d; border-bottom: 1px solid;\">"
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.netTotal || (depth0 != null ? depth0.netTotal : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"netTotal","hash":{},"data":data}) : helper)))
    + "</span>\n";
},"35":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "                                    <span style=\"font-weight: bold; color: #4d4d4d; border-bottom: 1px solid;\">"
    + alias4(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + " "
    + alias4(((helper = (helper = helpers.totalSum || (depth0 != null ? depth0.totalSum : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"totalSum","hash":{},"data":data}) : helper)))
    + "</span>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data,blockParams,depths) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing;

  return "<td align=\"center\" valign=\"top\" width=\"100%\" style=\"background-color: #ffffff;	border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;\">\n    <center>\n        <table cellpadding=\"0\" cellspacing=\"0\" width=\"600\" class=\"w320\">\n            <tr>\n                <td class=\"item-table\">\n                    <h4>Price Breakdown</h4>\n                    <table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n                        <tr>\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.agent_id : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "                            <td class=\"title-dark\">\n                                Item\n                            </td>\n                            <td class=\"title-dark\" width=\"100\" style=\"text-align:right\">Qty</td>\n                            <td class=\"title-dark\" width=\"100\" style=\"text-align:right\">Price</td>\n                        </tr>\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.packagesSummary : depth0),{"name":"each","hash":{},"fn":container.program(3, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.coursesSummary : depth0),{"name":"each","hash":{},"fn":container.program(6, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.ticketsSummary : depth0),{"name":"each","hash":{},"fn":container.program(9, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.addonsSummary : depth0),{"name":"each","hash":{},"fn":container.program(12, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.accommodations : depth0),{"name":"each","hash":{},"fn":container.program(15, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n                        <tr>\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.agent_id : depth0),{"name":"if","hash":{},"fn":container.program(19, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "                            <td class=\"item-col item\" style=\"border-top: 1px solid #cccccc;\">\n                            </td>\n                            <td class=\"item-col quantity\" style=\"width: 30%; text-align: right; padding-right: 10px; border-top: 1px solid #cccccc;\">\n                                <span class=\"total-space\">Subtotal</span><br />\n\n"
    + ((stack1 = (helpers.compare || (depth0 && depth0.compare) || alias2).call(alias1,(depth0 != null ? depth0.totalSurcharged : depth0),"!==","0",{"name":"compare","hash":{},"fn":container.program(21, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = (helpers.compare || (depth0 && depth0.compare) || alias2).call(alias1,(depth0 != null ? depth0.discount : depth0),"!==","0.00",{"name":"compare","hash":{},"fn":container.program(23, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.agent_id : depth0),{"name":"if","hash":{},"fn":container.program(25, data, 0, blockParams, depths),"inverse":container.program(27, data, 0, blockParams, depths),"data":data})) != null ? stack1 : "")
    + "                            </td>\n                            <td class=\"item-col price\" style=\"text-align: right; border-top: 1px solid #cccccc; padding-right: 20px;\">\n                                <span class=\"total-space\">"
    + container.escapeExpression(((helper = (helper = helpers.decimal_price_without_discount_applied || (depth0 != null ? depth0.decimal_price_without_discount_applied : depth0)) != null ? helper : alias2),(typeof helper === "function" ? helper.call(alias1,{"name":"decimal_price_without_discount_applied","hash":{},"data":data}) : helper)))
    + "</span><br />\n\n"
    + ((stack1 = (helpers.compare || (depth0 && depth0.compare) || alias2).call(alias1,(depth0 != null ? depth0.totalSurcharged : depth0),"!==","0",{"name":"compare","hash":{},"fn":container.program(29, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = (helpers.compare || (depth0 && depth0.compare) || alias2).call(alias1,(depth0 != null ? depth0.discount : depth0),"!==","0.00",{"name":"compare","hash":{},"fn":container.program(31, data, 0, blockParams, depths),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.agent_id : depth0),{"name":"if","hash":{},"fn":container.program(33, data, 0, blockParams, depths),"inverse":container.program(35, data, 0, blockParams, depths),"data":data})) != null ? stack1 : "")
    + "                            </td>\n                        </tr>\n                    </table>\n                </td>\n            </tr>\n        </table>\n    </center>\n</td>\n";
},"useData":true,"useDepths":true});
})();