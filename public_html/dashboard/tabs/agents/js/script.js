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

var agentForm,
	agentList;

$(function(){

	// Render initial agent list
	agentList = Handlebars.compile( $("#agent-list-template").html() );
	renderAgentList();

	// Default view: show create agent form
	agentForm = Handlebars.compile( $("#agent-form-template").html() );
	renderEditForm();

	$("#agent-form-container").on('click', '#add-agent', function(event) {

		event.preventDefault();

		// Show loading indicator
		$(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

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

			renderAgentList();

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

	if(window.tourStart) {

		introJs().setOptions( {
			showStepNumbers : false,
			exitOnOverlayClick : false,
            exitOnEsc : false
			}).start().onchange(function(targetElement) {
				switch (targetElement.id) {  
			        case "agent-form-container": 
			            $("#agent-name").val("John doe");
			        	$("#agent-web").val("http://www.onlinescubaholidays.com");
			        	$("#branch-name").val("Scuba holidays R us");
			        	$("#branch-address").val("46 grand avenue tenerife");
			        	$("#branch-phone").val("+44 7866565047");
			        	$("#branch-email").val("john.doe@scubaholidays.com");
			        break;

			        case "commission-div":
			        	$("#commission-amount").val(20);
			        	break;

			        case "agent-list-div":
			        	$("#agent-list").append('<li id="dummy-agent"><strong>John doe</strong> | Scuba holidays r us</li>');
			        	break;
		        }
			}).oncomplete(function() {
				$("#dummy-agent").remove();
				clearForm();
			});

		$("#tour-next-step").on("click", function() {
			window.location.href = "#boats";
			window.currentStep = "#boats";
		});
	}

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

function setToken(element) {
	if( window.token ) {
		$(element).val( window.token );
	}
	else {
		$.get('/token', function success(data) {
			window.token = data;
			setToken(element);
		});
	}
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
