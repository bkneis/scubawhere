
// Check that the company has gone through the setup wizard
if(window.company.initialised !== 1 && (!window.tourStart))
{
	window.location.href = '#dashboard';
}

// Needs to be declared before the $(function) call
Handlebars.registerHelper('selected', function(selectObject) {
	if(this.terms == selectObject)
		return ' selected';
	else
		return '';
});
Handlebars.registerHelper('isBanned', function() {
	if(this.terms == 'banned')
		return new Handlebars.SafeString(' class="banned"');
});

Handlebars.registerHelper('currency', function () {
	return new Handlebars.SafeString(window.company.currency.symbol);
});

Handlebars.registerHelper('commissionRulesSelect', function() {
	return new Handlebars.SafeString(commisssionRulesSelect({
		tickets: window.tickets,
		packages: window.packages,
		addons: window.addons,
		courses: window.courses,
		accommodations: window.accommodations,
	}));
});

Handlebars.registerHelper('calcCommission', function(rule) {
	return rule.commission !== null ? rule.commission : (parseInt(rule.commission_value) / 100).toFixed(2);
});

Handlebars.registerHelper('isAmount', function (rule, options) {
	if (rule.commission_value === null) {
		return options.fn(this);
	}
	return options.inverse(this);
})

var agentForm,
	agentList,
	commisssionRulesSelect;

$(function(){

	var gotData = $.Deferred();

	Package.getAllPackages(function (res) {
		window.packages = res;
		Course.getAll(function (res) {
			window.courses = res;
			Addon.getAllAddons(function (res) {
				window.addons = res;
				Ticket.getAllTickets(function (res) {
					window.tickets = res;
					Accommodation.getAll(function (res) {
						window.accommodations = res;
						gotData.resolve();
					});
				});
			})
		})
	});
	
	$.when(gotData).then(function () {
		// Render initial agent list
		agentList = Handlebars.compile( $("#agent-list-template").html() );
		renderAgentList();

		commisssionRulesSelect = Handlebars.compile( $('#commission-select-template').html() );

		// Default view: show create agent form
		agentForm = Handlebars.compile( $("#agent-form-template").html() );
		renderEditForm();
		TourManager.getAgentsTour();

		$("#agent-form-container").on('click', '#add-agent', function(event) {

			event.preventDefault();

			// Show loading indicator
			//$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Agent.createAgent( $('#add-agent-form').serialize(), function success(data) {

				pageMssg(data.status, true);

				$('form').data('hasChanged', false);

				renderAgentList(function() {
					renderEditForm(data.id);
				});

			}, function error(xhr) {

				var data = JSON.parse(xhr.responseText);
				console.log(data);

				if(data.errors.length > 0) {

					var errorsHTML = Handlebars.compile( $("#errors-template").html() );
					errorsHTML = errorsHTML(data);

					// Render error messages
					$('.errors').remove();
					$('#add-agent-form').prepend(errorsHTML);
					$('#add-agent').before(errorsHTML);
				}
				else {
					alert(xhr.responseText);
				}

				pageMssg('Oops, something wasn\'t quite right');

				$('#add-agent').prop('disabled', false);
				$('#add-agent-form').find('#save-loader').remove();
			});
		});

		$("#agent-form-container").on('click', '#update-agent', function(event) {

			event.preventDefault();

			// Show loading indicator
			$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

			Agent.updateAgent( $('#update-agent-form').serialize(), function success(data) {

				pageMssg(data.status, true);

				renderAgentList(renderEditForm);

				$('form').data('hasChanged', false);

				// Because the page is not re-rendered like with add-agent, we need to manually remove the error messages
				$('.errors').remove();

				$('#update-agent').prop('disabled', false);
				$('#update-agent-form').find('#save-loader').remove();

			}, function error(xhr) {

				var data = JSON.parse(xhr.responseText);
				console.log(data);

				if(data.errors.length > 0) {

					var errorsHTML = Handlebars.compile( $("#errors-template").html() );
					errorsHTML = errorsHTML(data);

					// Render error messages
					$('.errors').remove();
					$('#update-agent-form').prepend(errorsHTML);
					$('#update-agent').before(errorsHTML);
				}
				else {
					alert(xhr.responseText);
				}

				pageMssg('Oops, something wasn\'t quite right');
				$('#update-agent').prop('disabled', false);
				$('#save-loader').remove();
			});
		});

		$("#agent-list-container").on('click', '#change-to-add-agent', function(event){

			event.preventDefault();

			renderEditForm();
		});

		$("#agent-form-container").on('click', '.remove-agent', function(event) {
			event.preventDefault();
			var check = confirm('Do you really want to remove this agent?');
			if(check){
				// Show loading indicator
				$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

				var id = $('#update-agent-form input[name=id]').val();

				Agent.delete({
					'id'    : id,
					'_token': $('[name=_token]').val()
				}, function success(data){

					pageMssg(data.status, true);

					delete window.agents[id];

					$('.remove-agent').prop('disabled', false);
					$('#save-loader').remove();

					renderAgentList(renderEditForm);
				}, function error(xhr){

					pageMssg('Oops, something wasn\'t quite right');

					$('.remove-agent').prop('disabled', false);
					$('#save-loader').remove();
				});
			}
		});

		$('#agent-form-container').on('click', '#add-commission-rule', function (event) {
			event.preventDefault();
			$(this).before(commisssionRulesSelect({
				tickets: window.tickets,
				packages: window.packages,
				addons: window.addons,
				courses: window.courses,
				accommodations: window.accommodations,
				id: randomString(),
				rule: {
					unit: '',
					commission: null,
					commission_value: null,
					owner_id: 'default',
					owner_type: 'ticket'
				}
			}));
			$(this).siblings('p')
				.last()
				.children('select.select2')
				.select2()
				.on('select2-selecting', function (e) {
					var data = e.object.element[0].dataset;
					var id = e.val;
					id = id.substring(id.indexOf('-') + 1, id.length);
					console.log('id', id);
					$('#rule-type-' + data.ruleId).val(data.ruleType);
					$('#rule-id-' + data.ruleId).val(id);
					console.log('data', data);
				});
		});

		$('#agent-form-container').on('click', '.remove-commission-rule', function(event) {
			event.preventDefault();
			$(event.target).parent().remove();
		});
	});


});

function renderAgentList(callback) {

	$('#agent-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

	Agent.getAllAgents(function success(data) {

		window.agents = _.indexBy(data, 'id');
		$('#agent-list').remove();
		$('#agent-list-container .loader').remove();

		$("#agent-list-container").append( agentList({agents : data}) );

		// (Re)Assign eventListener for agent clicks
		$('#agent-list').on('click', 'li, strong', function(event) {

			if( $(event.target).is('strong') )
				event.target = event.target.parentNode;

			renderEditForm( event.target.getAttribute('data-id') );
		});

		if( typeof callback === 'function')
			callback();
	});
}

function renderEditForm(id) {

	if( unsavedChanges() ) {
		var question = confirm("ATTENTION: All unsaved changes are lost!");
		if( !question ) {
			return false;
		}
	}

	var agent;

	if( id ) {
		agent = window.agents[id];
		agent.task = 'update';
		agent.update = true;
	}
	else {
		agent = {
			task: 'add',
			/*name: 'TUI',
			website: 'http://tui.com',
			branch_name: 'Fishponds',
			branch_address: 'R8t down the road\nBristol, BS16 2HG',
			branch_phone: '+44791234567',
			branch_email: 'fishponds@uk.tui.com',
			commission: 12.8,
			terms: 'deposit',
			has_billing_details: function() {
				return this.billing_address || this.billing_email || this.billing_phone;
			},*/
		};
		agent.update = false;
	}

	agent.has_billing_details = agent.billing_address || agent.billing_email || agent.billing_phone;

	$('#agent-form-container').empty().append( agentForm(agent) );

	for (var i in agent.commission_rules) {
		var id = agent.commission_rules[i].owner_id;
		if (id === null) {
			id = 'default';
		}
		var value = agent.commission_rules[i].owner_type + '-' + id; 
		console.log(value);
		$('#add-commission-rule')
			.before(commisssionRulesSelect({
				tickets: window.tickets,
				packages: window.packages,
				addons: window.addons,
				courses: window.courses,
				accommodations: window.accommodations,
				id: randomString(),
				rule: {
					unit: agent.commission_rules[i].unit,
					commission: agent.commission_rules[i].commission,
					commission_value: agent.commission_rules[i].commission_value,
					owner_id: id,
					owner_type: agent.commission_rules[i].owner_type
				}
			}))
			.siblings('p')
			.last()
			.children('select.select2')
			.select2()
			.on('select2-selecting', function (e) {
				var data = e.object.element[0].dataset;
				console.log(data);
				var id = e.val;
				id = id.substring(id.indexOf('-') + 1, id.length);
				$('#rule-type-' + data.ruleId).val(data.ruleType);
				$('#rule-id-' + data.ruleId).val(id);
				console.log('id', id, 'e', e);
			})
			.val(value)
			.trigger('change');
		
	}

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}

function showMe(box, self) {

	var div = $(box);

	if( $(self).is(':checked') ) {
		div.show(0);
		div.find('input, textarea').prop('disabled', false);
	}
	else {
		div.hide(0);
		div.find('input, textarea').prop('disabled', true);
	}
}

function unsavedChanges() {
	return $('form').data('hasChanged');
}

function clearForm() {

	var agent;
		agent = {
			task: 'add',
		};
		agent.update = false;

	agent.has_billing_details = agent.billing_address || agent.billing_email || agent.billing_phone;

	$('#agent-form-container').empty().append( agentForm(agent) );

	setToken('[name=_token]');

	// Set up change monitoring
	$('form').on('change', 'input, select, textarea', function() {
		$('form').data('hasChanged', true);
	});
}
