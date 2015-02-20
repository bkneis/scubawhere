<div id="wrapper" class="clearfix">
<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Now, we need to add your trips. A trip consists of all the information for a dive, and are used to create tickets."></div>
	<div class="col-md-4">
		<div class="panel panel-default" id="trips-list-div" data-step="5" data-position="right" data-intro="Once a trip is saved, you will see it in your list. Click on a trip to view/edit the details.">
			<div class="panel-heading">
				<h4 class="panel-title">Available Trips</h4>
			</div>
			<div class="panel-body" id="trip-list-container">
				<button id="change-to-add-trip" class="btn btn-success text-uppercase">&plus; Add Trip</button>
				<script type="text/x-handlebars-template" id="trip-list-template">
					<ul id="trip-list" class="entity-list">
						{{#each trips}}
							<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{readable duration}}</li>
						{{else}}
							<p id="no-trips">No trips available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div class="panel panel-default" id="trip-form-container" data-step="2" data-position="left" data-intro="Enter a name, description and duration for the trip. Please note trip duration is in hours.">
			<script type="text/x-handlebars-template" id="trip-form-template">
				<div class="panel-heading">
					<h4 class="panel-title">{{task}} Trip</h4>
				</div>
				<div class="panel-body">
					<form id="{{task}}-trip-form">
						<div class="form-row">
							<label class="field-label">Trip Name</label>
							<input id="trip-name" type="text" name="name" value="{{{name}}}" style="width: 350px;">

							{{#if update}}
								{{#if deletable}}
									<span class="btn btn-danger pull-right remove-trip" style="color: white;">Remove</span>
								{{else}}
									<span class="questionmark-tooltip pull-right" title="This trip has tickets or sessions associated with it. It can not be removed.">?</span><span class="btn btn-danger pull-right disabled" style="color: white;">Remove</span>
								{{/if}}
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Trip Duration</label>
							<input type="number" min="1" step="0.1" name="duration" id="tripDuration" rows="3" cols="10" value="{{duration}}" style="width: 70px;"> hours
							<strong><span id="readableDuration" style="margin-left: 2em;">{{readable duration}}</span></strong>
							<button class="btn btn-primary btn-sm add1d" style="margin-left: 2em;">+1 day</button>
							<button class="btn btn-primary btn-sm sub1d">-1 day</button>
						</div>

						<div class="form-row">
							<label class="field-label">Trip Description</label>
							<textarea name="description" id="description" rows="3" cols="10" style="height: 243px;">{{{description}}}</textarea>
						</div>

						<div class="form-row" id="locationsList" data-step="3" data-position="top" data-intro="Next, select the locations of the trip">
							<h4>Select the locations that this trip will go to:</h4>
							{{#each available_locations}}
								<label class="location{{inArray id ../locations}}">
									<input type="checkbox" name="locations[]" value="{{id}}"{{inArray id ../locations}} onchange="changeParent(this)">
									<strong>{{name}}</strong><br>
									Lon: {{longitude}} | Lat: {{latitude}}
								</label>
							{{/each}}
						</div>

						<div class="form-row" id="tagsList" data-step="4" data-position="left" data-intro="Next, select any tags that describes what is offered in the trip. These tags will be searchable when scubawhere.com is launched. Lastly click 'Save' to create the trip.">
							<h4>Select the tags of this trip:</h4>
							{{#each available_tags}}
								<label class="tag{{inArray id ../tags}}">
									<input type="checkbox" name="tags[]" value="{{id}}"{{inArray id ../tags}} onchange="changeParent(this)">
									<strong>{{name}}</strong>
								</label>
							{{/each}}
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-trip" value="SAVE">

					</form>
				</div>
			</script>
		</div>
	</div>

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

	<script src="/js/Controllers/Trip.js"></script>
	<script src="/js/Controllers/Location.js"></script>
	<script src="/tabs/trips/js/script.js"></script>
</div>
