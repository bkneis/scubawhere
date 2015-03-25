<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Now, we need to add your classes. A class is any event that requires students to participate in learning for a qualification"></div>

	<div class="row">
		<div class="col-md-4">
			<div id="classes-list-div" class="panel panel-default" data-step="3" data-position="right" data-intro="Once a class is saved, you will see it in your list. Click on a class to view/edit the details.">
				<div class="panel-heading">
					<h4 class="panel-title">Available Classes</h4>
				</div>
				<div class="panel-body" id="class-list-container">
					<button id="change-to-add-class" class="btn btn-success text-uppercase">&plus; Add class</button>
					<script type="text/x-handlebars-template" id="class-list-template">
						<ul id="class-list" class="entity-list">
							{{#each classes}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong></li>
							{{else}}
								<p id="no-classes">No classes available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="class-form-container" data-step="2" data-position="left" data-intro="Enter a name, description and duration for the class.">
				<script type="text/x-handlebars-template" id="class-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} Class</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-class-form">
							<div class="form-row">
								<label class="field-label">Class Name</label>
								<input id="class-name" type="text" name="name" value="{{{name}}}">

								{{#if update}}
									<span class="btn btn-danger pull-right remove-class">Remove</span>
								{{/if}}
							</div>

							<div class="form-row">
								<label class="field-label">Class Duration</label>
								<input type="number" min="1" step="0.1" name="duration" id="tripDuration" rows="3" cols="10" value="{{duration}}" style="width: 70px;"> hours
								<strong><span id="readableDuration" style="margin-left: 2em;">{{readable duration}}</span></strong>
								<button class="btn btn-primary btn-sm add1d" style="margin-left: 2em;">+1 day</button>
								<button class="btn btn-primary btn-sm sub1d">-1 day</button>
							</div>

							<div class="form-row">
								<label class="field-label">Class Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							{{#if update}}
								<input type="hidden" name="id" value="{{id}}">
							{{/if}}
							
							<input type="hidden" name="_token">

							<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-class" value="SAVE">

						</form>
					</div>
				</script>
			</div>
		</div>
	</div><!-- .row -->

	<script type="text/x-handlebars-template" id="errors-template">
		<div class="yellow-helper errors" style="color: #E82C0C;">
			<strong>There are a few problems with the form:</strong>
			<ul>
				{{#each errors}}
					<li>{{this}}</li>
				{{/each}}
			</ul>
		</div>
	</script>

	<script src="/js/Controllers/Class.js"></script>
	<script src="/tabs/classes/js/script.js"></script>
</div>
