<div id="wrapper" class="clearfix">
	<div class="col-md-6">
		<h2>Frequently Asked Questions (FAQ)</h2>

			<!--
			#############################################################################
			### TO EDIT THE FAQ, PLEASE EDIT THE JSON OBJECT IN THE SCRIPT.JS FILE!!! ###
			#############################################################################
			-->

		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			<span class="loader"></span>
		</div>

		<script type="text/x-handlebars-template" id="faq-template">
			{{#each faq}}
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading-{{@index}}">
						<h4 class="panel-title">
							<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{@index}}" aria-expanded="true" aria-controls="collapse-{{@index}}">
								{{question}}
							</a>
						</h4>
					</div>
					<div id="collapse-{{@index}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-{{@index}}">
						<div class="panel-body">
							{{{answer}}}
						</div>
					</div>
				</div>
			{{/each}}
		</script>

	</div>

	<script src="tabs/help/js/script.js"></script>
</div>
