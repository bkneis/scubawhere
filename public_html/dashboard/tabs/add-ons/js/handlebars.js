(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['addonForm'] = template({"1":function(container,depth0,helpers,partials,data) {
    return "				<span class=\"btn btn-danger pull-right remove-addon\">Remove</span>\n";
},"3":function(container,depth0,helpers,partials,data) {
    var stack1;

  return ((stack1 = container.invokePartial(partials.price_input,depth0,{"name":"price_input","data":data,"indent":"\t\t\t\t","helpers":helpers,"partials":partials,"decorators":container.decorators})) != null ? stack1 : "");
},"5":function(container,depth0,helpers,partials,data) {
    return "checked";
},"7":function(container,depth0,helpers,partials,data) {
    var helper;

  return "			<input type=\"hidden\" name=\"id\" value=\""
    + container.escapeExpression(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"id","hash":{},"data":data}) : helper)))
    + "\">\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<div class=\"panel-heading\">\n	<h4 class=\"panel-title\">"
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + " add-on</h4>\n</div>\n<div class=\"panel-body\">\n	<form id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-addon-form\">\n		<div class=\"form-row\">\n			<label class=\"field-label\">Add-on Name</label>\n			<input id=\"addon-name\" type=\"text\" name=\"name\" value=\""
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.update : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		</div>\n		<div class=\"form-row\">\n			<label class=\"field-label\">Add-on Description</label>\n			<textarea name=\"description\" style=\"height: 243px;\">"
    + ((stack1 = ((helper = (helper = helpers.description || (depth0 != null ? depth0.description : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea>\n		</div>\n		<div class=\"form-row prices\">\n			<p><strong>Set prices for this add-on:</strong></p>\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.base_prices : depth0),{"name":"each","hash":{},"fn":container.program(3, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "			<button class=\"btn btn-default btn-sm add-base-price\"> &plus; Add another price</button>\n		</div>\n		<div class=\"form-row\" id=\"addon-compulsory-div\" data-step=\"3\" data-position=\"left\" data-intro=\"Additionally, you can set an addon to be compulsory for all bookings. For example, governmental dive taxes.\">\n			<label class=\"field-label\">Compulsory?</label>\n			<input id=\"addon-compulsory\" type=\"checkbox\" name=\"compulsory\" value=\"1\" "
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.compulsory : depth0),{"name":"if","hash":{},"fn":container.program(5, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "> Automatically add this add-on to every trip during booking.\n		</div>\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.update : depth0),{"name":"if","hash":{},"fn":container.program(7, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		<input type=\"hidden\" name=\"_token\">\n		<input type=\"submit\" class=\"btn btn-lg btn-primary text-uppercase pull-right\" id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-addon\" value=\"SAVE\">\n	</form>\n</div>";
},"usePartial":true,"useData":true});
templates['addonList'] = template({"1":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "		<li data-id=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"><strong>"
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</strong> | "
    + alias4((helpers.pricerange || (depth0 && depth0.pricerange) || alias2).call(alias1,(depth0 != null ? depth0.base_prices : depth0),{"name":"pricerange","hash":{},"data":data}))
    + "</li>\n";
},"3":function(container,depth0,helpers,partials,data) {
    return "		<p id=\"no-addons\">No addons available.</p>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<ul id=\"addon-list\" class=\"entity-list\">\n"
    + ((stack1 = helpers.each.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.addons : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(3, data, 0),"data":data})) != null ? stack1 : "")
    + "</ul>";
},"useData":true});
templates['errors'] = template({"1":function(container,depth0,helpers,partials,data) {
    return "			<li>"
    + container.escapeExpression(container.lambda(depth0, depth0))
    + "</li>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<div class=\"yellow-helper errors\" style=\"color: #E82C0C;\">\n	<strong>There are a few problems with the form:</strong>\n	<ul>\n"
    + ((stack1 = helpers.each.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.errors : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "	</ul>\n</div>";
},"useData":true});
templates['priceInput'] = template({"1":function(container,depth0,helpers,partials,data) {
    return " class=\"new_price\"";
},"3":function(container,depth0,helpers,partials,data) {
    var helper;

  return "		<span class=\"amount\">"
    + container.escapeExpression(((helper = (helper = helpers.decimal_price || (depth0 != null ? depth0.decimal_price : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"decimal_price","hash":{},"data":data}) : helper)))
    + "</span>\n";
},"5":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {};

  return "		<input type=\"number\" id=\"acom-price\" name=\""
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.isBase : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "prices["
    + container.escapeExpression(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "][new_decimal_price]\" placeholder=\"00.00\" min=\"0\" step=\"0.01\" style=\"width: 100px;\">\n";
},"6":function(container,depth0,helpers,partials,data) {
    return "base_";
},"8":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "	\n"
    + ((stack1 = helpers["if"].call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"if","hash":{},"fn":container.program(9, data, 0),"inverse":container.program(11, data, 0),"data":data})) != null ? stack1 : "")
    + "\n";
},"9":function(container,depth0,helpers,partials,data) {
    var helper;

  return "			from <big>"
    + container.escapeExpression(((helper = (helper = helpers.from || (depth0 != null ? depth0.from : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"from","hash":{},"data":data}) : helper)))
    + "</big>\n";
},"11":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "			from <input type=\"text\" name=\""
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.isBase : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "prices["
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "][from]\" class=\"datepicker\" data-date-format=\"YYYY-MM-DD\" value=\""
    + alias4(((helper = (helper = helpers.from || (depth0 != null ? depth0.from : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"from","hash":{},"data":data}) : helper)))
    + "\" style=\"width: 125px;\">\n";
},"13":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "\n		from <strong>the beginning of time</strong>\n"
    + ((stack1 = helpers.unless.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"unless","hash":{},"fn":container.program(14, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n";
},"14":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "			<input type=\"hidden\" name=\""
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.isBase : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "prices["
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "][from]\" value=\""
    + alias4(((helper = (helper = helpers.from || (depth0 != null ? depth0.from : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"from","hash":{},"data":data}) : helper)))
    + "\">\n";
},"16":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "\n"
    + ((stack1 = helpers["if"].call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"if","hash":{},"fn":container.program(17, data, 0),"inverse":container.program(19, data, 0),"data":data})) != null ? stack1 : "")
    + "\n";
},"17":function(container,depth0,helpers,partials,data) {
    var helper;

  return "			until <big>"
    + container.escapeExpression(((helper = (helper = helpers.until || (depth0 != null ? depth0.until : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"until","hash":{},"data":data}) : helper)))
    + "</big>\n";
},"19":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "			until <input type=\"text\" name=\""
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.isBase : depth0),{"name":"if","hash":{},"fn":container.program(6, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "prices["
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "][until]\" class=\"datepicker\" data-date-format=\"YYYY-MM-DD\" value=\""
    + alias4(((helper = (helper = helpers.until || (depth0 != null ? depth0.until : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"until","hash":{},"data":data}) : helper)))
    + "\" style=\"width: 125px;\">\n";
},"21":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "\n"
    + ((stack1 = helpers.unless.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"unless","hash":{},"fn":container.program(22, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		\n";
},"22":function(container,depth0,helpers,partials,data) {
    return "			<button class=\"btn btn-danger remove-price\">&#215;</button>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {};

  return "<p"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.decimal_price : depth0),{"name":"unless","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ">\n	<span class=\"currency\">"
    + container.escapeExpression(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + "</span>\n\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.decimal_price : depth0),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.program(5, data, 0),"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.isAlways : depth0),{"name":"unless","hash":{},"fn":container.program(8, data, 0),"inverse":container.program(13, data, 0),"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.isBase : depth0),{"name":"unless","hash":{},"fn":container.program(16, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.isAlways : depth0),{"name":"unless","hash":{},"fn":container.program(21, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "</p>";
},"useData":true});
})();