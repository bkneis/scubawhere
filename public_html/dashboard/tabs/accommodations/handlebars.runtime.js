(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['accommodationPriceInput'] = template({"1":function(container,depth0,helpers,partials,data) {
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

  return ((stack1 = helpers["if"].call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"if","hash":{},"fn":container.program(9, data, 0),"inverse":container.program(11, data, 0),"data":data})) != null ? stack1 : "");
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

  return "		from <strong>the beginning of time</strong>\n"
    + ((stack1 = helpers.unless.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"unless","hash":{},"fn":container.program(14, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "");
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

  return ((stack1 = helpers["if"].call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"if","hash":{},"fn":container.program(17, data, 0),"inverse":container.program(19, data, 0),"data":data})) != null ? stack1 : "");
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

  return ((stack1 = helpers.unless.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.decimal_price : depth0),{"name":"unless","hash":{},"fn":container.program(22, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "");
},"22":function(container,depth0,helpers,partials,data) {
    return "			<button class=\"btn btn-danger remove-price\">&#215;</button>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {};

  return "<p"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.decimal_price : depth0),{"name":"unless","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + ">\n	<span class=\"currency\">"
    + container.escapeExpression(((helper = (helper = helpers.currency || (depth0 != null ? depth0.currency : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(alias1,{"name":"currency","hash":{},"data":data}) : helper)))
    + "</span>\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.decimal_price : depth0),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.program(5, data, 0),"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.isAlways : depth0),{"name":"unless","hash":{},"fn":container.program(8, data, 0),"inverse":container.program(13, data, 0),"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.isBase : depth0),{"name":"unless","hash":{},"fn":container.program(16, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "\n"
    + ((stack1 = helpers.unless.call(alias1,(depth0 != null ? depth0.isAlways : depth0),{"name":"unless","hash":{},"fn":container.program(21, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "</p>";
},"useData":true});
templates['accomodationForm'] = template({"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    return "";
},"useData":true});
templates['accomodationList'] = template({"1":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "		<li data-id=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"><strong>"
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</strong> | "
    + alias4(((helper = (helper = helpers.capacity || (depth0 != null ? depth0.capacity : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"capacity","hash":{},"data":data}) : helper)))
    + " | "
    + alias4((helpers.pricerange || (depth0 && depth0.pricerange) || alias2).call(alias1,(depth0 != null ? depth0.base_prices : depth0),(depth0 != null ? depth0.prices : depth0),{"name":"pricerange","hash":{},"data":data}))
    + "</li>\n";
},"3":function(container,depth0,helpers,partials,data) {
    return "		<p id=\"no-accommodations\">No accommodations available.</p>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<ul id=\"accommodation-list\" class=\"entity-list\">\n"
    + ((stack1 = helpers.each.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.accommodations : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(3, data, 0),"data":data})) != null ? stack1 : "")
    + "</ul>";
},"useData":true});
})();