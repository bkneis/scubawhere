var automatedEmailFormTemplate;
var automationRulesListTemplate;
$(function() {
    
    automatedEmailForm = Handlebars.compile($('#automated-email-form-template').html());
    automationRulesListTemplate = Handlebars.compile($('#automated-enabled-rules-list-template').html());
    
    renderAutomationRulesList();
    
});

function renderAutomationRulesList() 
{
    Campaign.getAutomationRules(function success(data) {
        $('#enabled-rules-list').empty().append(automationRulesListTemplate({rules : data}));
    });
}

function renderAutomationRulesForm()
{
    
}