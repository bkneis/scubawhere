<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;" data-step="1" data-intro="Now, we need to add your boats. Boats are assigned to trips once activated. This allows you to manage the schedule of your boats."></div>

	<div class="row">
		<div class="col-md-4">
			<div id="boats-list-div" class="panel panel-default" data-step="8" data-position="right" data-intro="Once a boat is saved, you will see it in your list. Click on a boat to view/edit the details.">
				<div class="panel-heading">
					<h4 class="panel-title">Available Boats</h4>
				</div>
				<div class="panel-body" id="boat-list-container">
					<button id="change-to-add-boat" class="btn btn-success text-uppercase" data-step="4" data-position="right" data-intro="Once you have added all of your cabins, you can start creating your boats. Click this button">&plus; Add Boat</button>
					<script type="text/x-handlebars-template" id="boat-list-template">
						<ul id="boat-list" class="entity-list">
							{{#each boats}}
								<li data-id="{{id}}"><strong>{{name}}</strong> | Capacity: {{capacity}}</li>
							{{else}}
								<p id="no-boats">No boats available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>

			<div class="panel panel-default" data-step="2" data-position="right" data-intro="If your dive operation offers liveaboards, you will need to declare the diffrent types of cabins.">
				<div class="panel-heading">
					<h4 class="panel-title">Available Cabins</h4>
				</div>
				<div class="panel-body" id="boatroom-list-container">
					<button class="btn btn-success text-uppercase change-to-add-boatroom" data-step="3" data-position="right" data-intro="To 'add cabin', click here. Then enter a name and description.">&plus; Add Cabin</button>
					<script type="text/x-handlebars-template" id="boatroom-list-template">
						<ul id="boatroom-list" class="entity-list">
							{{#each boatrooms}}
								<li data-id="{{id}}"><strong>{{name}}</strong></li>
							{{else}}
								<p>No cabins available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="boat-form-container" data-step="5" data-position="left" data-intro="Enter a name and description for the boat.">
				<script type="text/x-handlebars-template" id="boat-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} boat</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-boat-form" accept-charset="utf-8">
						{{#if update}}
							<span class="btn btn-danger pull-right remove-boat">Remove</span>
						{{/if}}
							<div class="form-row">
								<label class="field-label">Boat name</label>
								<input id="boat-name" type="text" name="name" value="{{{name}}}">
							</div>
							<div class="form-row">
								<label class="field-label">Boat description</label>
								<textarea id="boat-description" name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>
							<div class="form-row" data-step="6" data-position="left" data-intro="Enter your boat capacity, excluding your crew.">
								<label class="field-label">Boat capacity</label>
								<input id="boat-capacity" type="number" name="capacity" value="{{capacity}}" placeholder="0" style="width: 100px;" min="0">
							</div>
							<div id="boat-cabins" class="form-row" data-step="7" data-position="left" data-intro="Here shows a summary of the cabins available for this boat. To attach a cabin to a boat, click add cabin and select the cabin type and number of rooms">
								<div id="room-types">
								<h4>Cabins on this boat</h4>
									{{#each boatrooms}}
										{{> boatroom_show}}
									{{/each}}
								</div>
								<button id="add-room" class="btn btn-success text-uppercase"> &plus; Add cabin</button>
							</div>
							{{#if update}}
								<input type="hidden" name="id" value="{{id}}">
							{{/if}}
							<input type="hidden" name="_token">
							<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-boat" value="SAVE">
						</form>
					</div>
				</script>
			</div>

			<script type="text/x-handlebars-template" id="add-room-template">
				<p>
					<select class="room-type-select"
					onchange="$(this).siblings('input').attr('name', 'boatrooms['+ $(this).val() +'][capacity]');">
						{{#each boatrooms}}
							<option value="{{id}}">{{name}}</option>
						{{/each}}
					</select>
					Number of beds:
					<input type="number" name="boatrooms[{{firstID boatrooms}}][capacity]" value="{{capacity}}" placeholder="0" style="width: 100px;" min="0">
					<button class="btn btn-danger remove-room">&#215;</button>
				</p>
			</script>

			<script type="text/x-handlebars-template" id="show-room-template">
				<p>
					<span class="boatroom-name">{{name}}</span>
					Number of Beds:
					<input type="number" name="boatrooms[{{id}}][capacity]" value="{{pivot.capacity}}" placeholder="0" style="width: 100px;" min="0">
					<button class="btn btn-danger remove-room">&#215;</button>
				</p>
			</script>

			<div class="panel panel-default" style="display: none;" id="boatroom-form-container">
				<script type="text/x-handlebars-template" id="boatroom-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} cabin</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-boatroom-form" accept-charset="utf-8">
							{{#if update}}
								<span class="btn btn-danger pull-right remove-boatroom">Remove</span>
							{{/if}}
							<div class="form-row">
								<label class="field-label">Cabin name</label>
								<input type="text" name="name" value="{{{name}}}">
							</div>
							<div class="form-row">
								<label class="field-label">Cabin description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>
							{{#if update}}
								<input type="hidden" name="id" value="{{id}}">
							{{/if}}
							<input type="hidden" name="_token">
							<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-boatroom" value="SAVE">
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

	<!--<script src="/tabs/boats/handlebars.runtime.js"></script>-->
	<script src="/tabs/boats/js/script.js"></script>
</div>
