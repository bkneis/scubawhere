(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['boatAddRoom'] = template({"1":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "			<option value=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</option>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3=container.escapeExpression;

  return "<p>\n	<select class=\"room-type-select\"\n	onchange=\"$(this).siblings('input').attr('name', 'boatrooms['+ $(this).val() +'][capacity]');\">\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.boatrooms : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "	</select>\n	Number of beds:\n	<input type=\"number\" name=\"boatrooms["
    + alias3((helpers.firstID || (depth0 && depth0.firstID) || alias2).call(alias1,(depth0 != null ? depth0.boatrooms : depth0),{"name":"firstID","hash":{},"data":data}))
    + "][capacity]\" value=\""
    + alias3(((helper = (helper = helpers.capacity || (depth0 != null ? depth0.capacity : depth0)) != null ? helper : alias2),(typeof helper === "function" ? helper.call(alias1,{"name":"capacity","hash":{},"data":data}) : helper)))
    + "\" placeholder=\"0\" style=\"width: 100px;\" min=\"0\">\n	<button class=\"btn btn-danger remove-room\">&#215;</button>\n</p>";
},"useData":true});
templates['boatForm'] = template({"1":function(container,depth0,helpers,partials,data) {
    return "		<span class=\"btn btn-danger pull-right remove-boat\">Remove</span>\n";
},"3":function(container,depth0,helpers,partials,data) {
    var stack1;

  return ((stack1 = container.invokePartial(partials.boatroom_show,depth0,{"name":"boatroom_show","data":data,"indent":"\t\t\t\t\t","helpers":helpers,"partials":partials,"decorators":container.decorators})) != null ? stack1 : "");
},"5":function(container,depth0,helpers,partials,data) {
    var helper;

  return "			<input type=\"hidden\" name=\"id\" value=\""
    + container.escapeExpression(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"id","hash":{},"data":data}) : helper)))
    + "\">\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<div class=\"panel-heading\">\n	<h4 class=\"panel-title\">"
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + " boat</h4>\n</div>\n<div class=\"panel-body\">\n	<form id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-boat-form\" accept-charset=\"utf-8\">\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.update : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		<div class=\"form-row\">\n			<label class=\"field-label\">Boat name</label>\n			<input id=\"boat-name\" type=\"text\" name=\"name\" value=\""
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n		</div>\n		<div class=\"form-row\">\n			<label class=\"field-label\">Boat description</label>\n			<textarea id=\"boat-description\" name=\"description\" style=\"height: 243px;\">"
    + ((stack1 = ((helper = (helper = helpers.description || (depth0 != null ? depth0.description : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea>\n		</div>\n		<div class=\"form-row\" data-step=\"6\" data-position=\"left\" data-intro=\"Enter your boat capacity, excluding your crew.\">\n			<label class=\"field-label\">Boat capacity</label>\n			<input id=\"boat-capacity\" type=\"number\" name=\"capacity\" value=\""
    + alias4(((helper = (helper = helpers.capacity || (depth0 != null ? depth0.capacity : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"capacity","hash":{},"data":data}) : helper)))
    + "\" placeholder=\"0\" style=\"width: 100px;\" min=\"0\">\n		</div>\n		<div id=\"boat-cabins\" class=\"form-row\" data-step=\"7\" data-position=\"left\" data-intro=\"Here shows a summary of the cabins available for this boat. To attach a cabin to a boat, click add cabin and select the cabin type and number of rooms\">\n			<div id=\"room-types\">\n			<h4>Cabins on this boat</h4>\n"
    + ((stack1 = helpers.each.call(alias1,(depth0 != null ? depth0.boatrooms : depth0),{"name":"each","hash":{},"fn":container.program(3, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "			</div>\n			<button id=\"add-room\" class=\"btn btn-success text-uppercase\"> &plus; Add cabin</button>\n		</div>\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.update : depth0),{"name":"if","hash":{},"fn":container.program(5, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		<input type=\"hidden\" name=\"_token\">\n		<input type=\"submit\" class=\"btn btn-primary btn-lg text-uppercase pull-right\" id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-boat\" value=\"SAVE\">\n	</form>\n</div>";
},"usePartial":true,"useData":true});
templates['boatList'] = template({"1":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "		<li data-id=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"><strong>"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</strong> | Capacity: "
    + alias4(((helper = (helper = helpers.capacity || (depth0 != null ? depth0.capacity : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"capacity","hash":{},"data":data}) : helper)))
    + "</li>\n";
},"3":function(container,depth0,helpers,partials,data) {
    return "		<p id=\"no-boats\">No boats available.</p>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<ul id=\"boat-list\" class=\"entity-list\">\n"
    + ((stack1 = helpers.each.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.boats : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(3, data, 0),"data":data})) != null ? stack1 : "")
    + "</ul>";
},"useData":true});
templates['boatroomForm'] = template({"1":function(container,depth0,helpers,partials,data) {
    return "			<span class=\"btn btn-danger pull-right remove-boatroom\">Remove</span>\n";
},"3":function(container,depth0,helpers,partials,data) {
    var helper;

  return "			<input type=\"hidden\" name=\"id\" value=\""
    + container.escapeExpression(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : helpers.helperMissing),(typeof helper === "function" ? helper.call(depth0 != null ? depth0 : {},{"name":"id","hash":{},"data":data}) : helper)))
    + "\">\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<div class=\"panel-heading\">\n	<h4 class=\"panel-title\">"
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + " cabin</h4>\n</div>\n<div class=\"panel-body\">\n	<form id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-boatroom-form\" accept-charset=\"utf-8\">\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.update : depth0),{"name":"if","hash":{},"fn":container.program(1, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		<div class=\"form-row\">\n			<label class=\"field-label\">Cabin name</label>\n			<input type=\"text\" name=\"name\" value=\""
    + ((stack1 = ((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "\">\n		</div>\n		<div class=\"form-row\">\n			<label class=\"field-label\">Cabin description</label>\n			<textarea name=\"description\" style=\"height: 243px;\">"
    + ((stack1 = ((helper = (helper = helpers.description || (depth0 != null ? depth0.description : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"description","hash":{},"data":data}) : helper))) != null ? stack1 : "")
    + "</textarea>\n		</div>\n"
    + ((stack1 = helpers["if"].call(alias1,(depth0 != null ? depth0.update : depth0),{"name":"if","hash":{},"fn":container.program(3, data, 0),"inverse":container.noop,"data":data})) != null ? stack1 : "")
    + "		<input type=\"hidden\" name=\"_token\">\n		<input type=\"submit\" class=\"btn btn-primary btn-lg text-uppercase pull-right\" id=\""
    + alias4(((helper = (helper = helpers.task || (depth0 != null ? depth0.task : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"task","hash":{},"data":data}) : helper)))
    + "-boatroom\" value=\"SAVE\">\n	</form>\n</div>";
},"useData":true});
templates['boatroomList'] = template({"1":function(container,depth0,helpers,partials,data) {
    var helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "		<li data-id=\""
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "\"><strong>"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</strong></li>\n";
},"3":function(container,depth0,helpers,partials,data) {
    return "		<p>No cabins available.</p>\n";
},"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1;

  return "<ul id=\"boatroom-list\" class=\"entity-list\">\n"
    + ((stack1 = helpers.each.call(depth0 != null ? depth0 : {},(depth0 != null ? depth0.boatrooms : depth0),{"name":"each","hash":{},"fn":container.program(1, data, 0),"inverse":container.program(3, data, 0),"data":data})) != null ? stack1 : "")
    + "</ul>";
},"useData":true});
templates['boatShowRoom'] = template({"compiler":[7,">= 4.0.0"],"main":function(container,depth0,helpers,partials,data) {
    var stack1, helper, alias1=depth0 != null ? depth0 : {}, alias2=helpers.helperMissing, alias3="function", alias4=container.escapeExpression;

  return "<p>\n	<span class=\"boatroom-name\">"
    + alias4(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"name","hash":{},"data":data}) : helper)))
    + "</span>\n	Number of Beds:\n	<input type=\"number\" name=\"boatrooms["
    + alias4(((helper = (helper = helpers.id || (depth0 != null ? depth0.id : depth0)) != null ? helper : alias2),(typeof helper === alias3 ? helper.call(alias1,{"name":"id","hash":{},"data":data}) : helper)))
    + "][capacity]\" value=\""
    + alias4(container.lambda(((stack1 = (depth0 != null ? depth0.pivot : depth0)) != null ? stack1.capacity : stack1), depth0))
    + "\" placeholder=\"0\" style=\"width: 100px;\" min=\"0\">\n	<button class=\"btn btn-danger remove-room\">&#215;</button>\n</p>";
},"useData":true});
})();