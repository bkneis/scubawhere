<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="log-list-div">
				<div class="panel-heading">
					<h4 class="panel-title">Error Logs</h4>
				</div>
				<div class="panel-body" id="log-list-container">
					<script type="text/x-handlebars-template" id="log-list-template">
						<ul id="log-list" class="entity-list">
							{{#each logs}}
								<li data-id="{{id}}">{{{name}}}</li>
							{{else}}
								<p id="no-logs">No logs available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="log-details-container">
				<script type="text/x-handlebars-template" id="log-details-template">
					<div class="panel-heading">
						<h4 class="panel-title">View Log</h4>
                    </div>
                    <div class="panel-body">
                        {{#if id}}
                            <span class="btn btn-danger pull-right remove-log" data-id="{{id}}">Remove</span>
                            <p><strong>Name :</strong> {{name}}</p>
                            <p><strong>Errors : </strong></p>
                            <ul>
                                {{#each entries}}
									{{link description}}
                                {{/each}}
                            </ul>
						{{else}}
							<div style="height:100px;"></div>
                        {{/if}}
                    </div>
				</script>
			</div>
		</div>
	</div><!-- .row -->
    
    <script src="/dashboard/js/Repositories/LogRepo.js"></script>
    <script src="/dashboard/tabs/troubleshooting/js/LogService.js"></script>
    <script src="/dashboard/tabs/troubleshooting/js/LogObserver.js"></script>
    <script src="/dashboard/tabs/troubleshooting/js/main.js"></script>
</div>
