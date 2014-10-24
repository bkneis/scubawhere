<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Available trips</label>
			<div class="padder" id="trip-list-container">
				<!-- <div class="yellow-helper">
					Select a trip to change its details.
				</div> -->
				<button id="change-to-add-trip" style="padding: 0.5em 1em; margin: 0.4em;" class="bttn greenb">&plus; Add Trip</button>
				<script type="text/x-handlebars-template" id="trip-list-template">
					<ul id="trip-list" class="entity-list">
						{{#each trips}}
							<li data-id="{{id}}"{{#if trashed}} class="trashed"{{/if}}><strong>{{{name}}}</strong> | {{readable duration}}</li>
						{{else}}
							<p>No trips available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="trip-form-container">

			<script type="text/x-handlebars-template" id="trip-form-template">
				<label class="dgreyb">{{task}} trip</label>
				<div class="padder">
					<form id="{{task}}-trip-form">
						<div class="form-row">
							<label class="field-label">Trip Name</label>
							<input type="text" name="name" value="{{{name}}}" style="width: 350px;">
							{{#if trashed}}
								<strong style="color: #FF7163;">(Deactivated)</strong>
							{{/if}}

							{{#if update}}
								{{#if trashed}}
									<span class="box-tool blueb restore-trip" style="color: white;">Restore</span>
								{{else}}
									{{#if deletable}}
										<span class="box-tool redb remove-trip" style="color: white;">Remove</span>
									{{else}}
										<span class="questionmark-tooltip" title="This trip has tickets or sessions associated with it. It can not be removed." style="float: right;">?</span><span class="box-tool redb disabled" style="color: white;">Remove</span>
									{{/if}}
								{{/if}}
							{{/if}}
						</div>

						<div class="form-row">
							<label class="field-label">Trip Duration</label>
							<input type="number" min="1" step="1" name="duration" id="tripDuration" rows="3" cols="10" value="{{duration}}" style="width: 50px;"> hours
							<strong><span id="readableDuration" style="margin-left: 2em;">{{readable duration}}</span></strong>
							<button class="bttn blueb small-bttn add1d" style="margin-left: 2em;">+1 day</button>
							<button class="bttn blueb small-bttn sub1d">-1 day</button>
						</div>

						<div class="form-row">
							<label class="field-label">Trip Description</label>
							<textarea name="description" id="description" rows="3" cols="10" style="height: 243px;">{{{description}}}</textarea>
						</div>

						<div class="form-row" id="locationsList">
							<h3>Select the locations that this trip will go to:</h3>
							{{#each available_locations}}
								<label class="location{{inArray id ../locations}}">
									<input type="checkbox" name="locations[]" value="{{id}}"{{inArray id ../locations}} onchange="changeParent(this)">
									<strong>{{name}}</strong><br>
									Lon: {{longitude}} | Lat: {{latitude}}
								</label>
							{{/each}}
						</div>

						<div class="form-row" id="triptypesList">
							<h3>Select the triptypes of this trip:</h3>
							{{#each available_triptypes}}
								<label class="triptype{{inArray id ../triptypes}}">
									<input type="checkbox" name="triptypes[]" value="{{id}}"{{inArray id ../triptypes}} onchange="changeParent(this)">
									<strong>{{name}}</strong><br>
								</label>
							{{/each}}
						</div>

						{{#if update}}
							<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">

						<input type="submit" class="bttn blueb big-bttn" id="{{task}}-trip" value="{{task}} Trip">

					</form>
				</div>
			</script>

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
	</div>
</div>

<script src="/dashboard/js/Controllers/Trip.js"></script>
<script src="/dashboard/js/Controllers/Location.js"></script>
<script src="tabs/trips/js/script.js"></script>
