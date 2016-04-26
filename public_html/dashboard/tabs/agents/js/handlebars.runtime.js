(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['agentForm'] = template({"1":function(container,depth0,helpers,partials,data) {
    return " checked";
},"3":function(container,depth0,helpers,partials,data) {
    return "display: none; ";
},"5":function(container,depth0,helpers,partials,data) {
    return " disabled";
},"7":function(container,depth0,helpers,partials,data) {
    var helper;

  return "			<input type=\"hidden\" name=\"id\" value=\""
    + container.escapeExpression(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"id","hash":{},"data":data}) : helper)))
    + "\">\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<div class=\"panel-heading\">\n	<h4 class=\"panel-title\">"
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + " agent</h4>\n</div>\n<div class=\"panel-body\">\n	<form id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-agent-form\">\n		<div class=\"form-row\">\n			<label class=\"field-label\">Agent Name</label>\n			<input id=\"agent-name\" type=\"text\" name=\"name\" value=\""
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n		</div>\n\n		<div class=\"form-row\">\n			<label class=\"field-label\">Agent&rsquo;s Website (Optional)</label>\n			<input id=\"agent-web\" type=\"text\" name=\"website\" placeholder=\"http://\" value=\""
    + ((stack1 = ((helper = (helper = helpers.website || (depth0 != null ? depth0.website : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"website","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n		</div>\n\n		<div class=\"form-row\">\n			<label class=\"field-label\">Branch Name</label>\n			<input id=\"branch-name\" type=\"text\" name=\"branch_name\" value=\""
    + ((stack1 = ((helper = (helper = helpers.branch_name || (depth0 != null ? depth0.branch_name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"branch_name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n		</div>\n\n		<div class=\"form-row\">\n			<label class=\"field-label\">Branch Address</label>\n			<textarea id=\"branch-address\" name=\"branch_address\" rows=\"3\" cols=\"10\">"
    + ((stack1 = ((helper = (helper = helpers.branch_address || (depth0 != null ? depth0.branch_address : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"branch_address","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea>\n		</div>\n\n		<div class=\"form-row\">\n			<label class=\"field-label\">Branch Telephone</label>\n			<input id=\"branch-phone\" type=\"text\" name=\"branch_phone\" placeholder=\"+00 000 ...\" value=\""
    + ((stack1 = ((helper = (helper = helpers.branch_phone || (depth0 != null ? depth0.branch_phone : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"branch_phone","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n		</div>\n\n		<div class=\"form-row\">\n			<label class=\"field-label\">Branch E-Mail</label>\n			<input id=\"branch-email\" type=\"text\" name=\"branch_email\" placeholder=\"email@agent.com\" value=\""
    + ((stack1 = ((helper = (helper = helpers.branch_email || (depth0 != null ? depth0.branch_email : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"branch_email","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n		</div>\n\n		<div class=\"form-row\">\n			<label>\n				<input type=\"checkbox\" onchange=\"showMe('#billing-div', this);\""
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.has_billing_details : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ">\n				Enter different billing details?\n			</label>\n		</div>\n		<div id=\"billing-div\" class=\"dashed-border\" style=\""
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.has_billing_details : depth0),{"name":"unless","hash":{},"fn":container.program(3, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\">\n			<h3>Billing Information</h3>\n			<div class=\"form-row\">\n				<label class=\"field-label\">Billing Address</label>\n				<textarea name=\"billing_address\" rows=\"3\" cols=\"10\""
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.has_billing_details : depth0),{"name":"unless","hash":{},"fn":container.program(5, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ">"
    + ((stack1 = ((helper = (helper = helpers.billing_address || (depth0 != null ? depth0.billing_address : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"billing_address","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea>\n			</div>\n\n			<div class=\"form-row\">\n				<label class=\"field-label\">Billing Phone Number</label>\n				<input type=\"text\" name=\"billing_phone\""
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.has_billing_details : depth0),{"name":"unless","hash":{},"fn":container.program(5, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + " placeholder=\"+00 000 ...\" value=\""
    + ((stack1 = ((helper = (helper = helpers.billing_phone || (depth0 != null ? depth0.billing_phone : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"billing_phone","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n			</div>\n\n			<div class=\"form-row\">\n				<label class=\"field-label\">Billing E-Mail Address</label>\n				<input type=\"text\" name=\"billing_email\""
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.has_billing_details : depth0),{"name":"unless","hash":{},"fn":container.program(5, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + " placeholder=\"billing@agent.com\" value=\""
    + ((stack1 = ((helper = (helper = helpers.billing_email || (depth0 != null ? depth0.billing_email : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"billing_email","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n			</div>\n		</div>\n\n		<div class=\"form-row\" id=\"commission-div\" data-step=\"3\" data-position=\"left\" data-intro=\"Enter the percentage of commission the agent recieves for each booking.\">\n			<label class=\"field-label\">Commission</label>\n			<input id=\"commission-amount\" type=\"text\" name=\"commission\" size=\"4\" placeholder=\"00.00\" value=\""
    + alias4(((helper = (helper = helpers.commission || (depth0 != null ? depth0.commission : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"commission","hash":{},"data":data}) : helper)))
    + "\"> %\n		</div>\n\n		<div class=\"form-row\" data-step=\"4\" data-position=\"top\" data-intro=\"Define your relationship to the agent with one of the drop down options. 'Deposit only' means the agent will take the commission percentage directly, and the remaning balance will be paid directly to you. 'Full amount' means the agent gets paid the full amount for the reservation, then you will invoice the agent for payment. 'Banned' means that the agent is blocked and they are no longer allowed to make reservations. Lastly, click 'save' to add your agent.\">\n			<label class=\"field-label\">Business Terms</label>\n			<select id=\"terms\" name=\"terms\">\n				<option>Please select..</option>\n				<option value=\"fullamount\""
    + alias4((helpers.selected || (depth0 && depth0.selected) || alias2).call(alias1,"fullamount",{"name":"selected","hash":{},"data":data}))
    + ">Full Amount</option>\n				<option value=\"deposit\""
    + alias4((helpers.selected || (depth0 && depth0.selected) || alias2).call(alias1,"deposit",{"name":"selected","hash":{},"data":data}))
    + ">Deposit Only</option>\n				<option value=\"banned\""
    + alias4((helpers.selected || (depth0 && depth0.selected) || alias2).call(alias1,"banned",{"name":"selected","hash":{},"data":data}))
    + ">Banned</option>\n			</select>\n		</div>\n\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.update : depth0),{"name":"if","hash":{},"fn":container.program(7, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		<input type=\"hidden\" name=\"_token\">\n\n		<button class=\"btn btn-primary btn-lg text-uppercase pull-right\" id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-agent\">SAVE</button>\n	</form>\n</div>";
},"useData":true});
templates['agentList'] = template({"1":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "		<li data-id=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\""
    + alias4(((helper = (helper = helpers.isBanned || (depth0 != null ? depth0.isBanned : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"isBanned","hash":{},"data":data}) : helper)))
    + "><strong>"
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</strong> | "
    + ((stack1 = ((helper = (helper = helpers.branch_name || (depth0 != null ? depth0.branch_name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"branch_name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</li>\n";
},"3":function(container,depth0,helpers,partials,data) {
    return "		<p id=\"no-agents\">No agents available.</p>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<ul id=\"agent-list\" class=\"entity-list\">\n"
    + ((stack1 = helpers.each.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.agents : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(3, data, 0),"data":data})) != null ? stack1 : "")
    + "</ul>";
},"useData":true});
})();