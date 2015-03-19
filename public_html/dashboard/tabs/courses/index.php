<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Now, we need to add your classes. A class can be valid for many trips. A class is a single reservation for a trip. For an educational course please create a package (see next page)."></div>

	<div class="row">
		<div class="col-md-4">
			<div id="classes-list-div" class="panel panel-default" data-step="7" data-position="right" data-intro="Once a class is saved, you will see it in your list. Click on a class to view/edit the details.">
				<div class="panel-heading">
					<h4 class="panel-title">Available Courses</h4>
				</div>
				<div class="panel-body" id="course-list-container">
					<button id="change-to-add-course" class="btn btn-success text-uppercase">&plus; Add course</button>
					<script type="text/x-handlebars-template" id="course-list-template">
						<ul id="course-list" class="entity-list">
							{{#each courses}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong></li>
							{{else}}
								<p id="no-courses">No courses available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="course-form-container" data-step="2" data-position="left" data-intro="Enter a name, description and base price for the class.">
				<script type="text/x-handlebars-template" id="course-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} Course</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-course-form">

							<div class="form-row">
								<label class="field-label">Course Name</label>
								<input id="course-name" type="text" name="name" value="{{{name}}}">
							</div>

							<div class="form-row">
								<label class="field-label">Course Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row" data-step="6" data-position="left" data-intro="Enter your boat capacity, excluding your crew.">
								<label class="field-label">Course capacity</label>
								<input id="course-capacity" type="number" name="capacity" value="{{capacity}}" placeholder="0" style="width: 100px;" min="0">
							</div>

							<div id="course-classes" class="form-row" data-step="7" data-position="left" data-intro="Here shows a summary of the cabins available for this boat. To attach a cabin to a boat, click add cabin and select the cabin type and number of rooms">
								<div id="class-types">
								<h4>Classes for this course</h4>
									{{#each classes}}
										{{> classes_show}}
									{{/each}}
								</div>
								<button id="add-class" class="btn btn-success text-uppercase"> &plus; Add class</button>
							</div>

							{{#if update}}
								<input type="hidden" name="id" value="{{id}}">
							{{/if}}
							
							<input type="hidden" name="_token">

							<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-course" value="SAVE">

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

	<script type="text/x-handlebars-template" id="class-select-template">
		<p>
			<select class="class-select" onchange="">
				{{#each classes}}
					<option value="{{id}}">{{name}}</option>
				{{/each}}
			</select>
			Number of sessions:
			<input type="number" name="class[{{id}}]" value="{{capacity}}" placeholder="0" style="width: 100px;" min="0">
			<button class="btn btn-danger remove-class">&#215;</button>
		</p>
	</script>

	<script src="/js/Controllers/Class.js"></script>
	<script src="/js/Controllers/Course.js"></script>
	<script src="/tabs/courses/js/script.js"></script>
</div>
