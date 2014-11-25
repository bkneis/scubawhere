<div id="wrapper">
	<div class="row">
		<div class="box30">
			<label class="dgreyb">Current Boats</label>
			<div class="padder" id="boats-list-container">
				<button id="change-to-add-boat" style="padding: 0.5em 1em;" class="bttn greenb">&plus; Add Boat</button>
				<script type="text/x-handlebars-template" id="boats-list-template">
					<ul id="boats-list" class="entity-list">
						{{#each boats}}
							<li data-id="{{id}}"><strong>{{name}}</strong> | Capacity: {{capacity}}</li>
						{{else}}
							<p>No boats available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
			<label class="dgreyb">Current Room Types</label>
			<div class="padder" id="boatrooms-list-container">
				<button id="change-to-add-boatroom" style="padding: 0.5em 1em;" class="bttn greenb">&plus; Add Room Type</button>
				<script type="text/x-handlebars-template" id="boatrooms-list-template">
					<ul id="boatrooms-list" class="entity-list">
						{{#each boatrooms}}
							<li data-id="{{id}}"><strong>{{name}}</strong></li>
						{{else}}
							<p>No room types available.</p>
						{{/each}}
					</ul>
				</script>
			</div>
		</div>

		<div class="box70" id="boats-form-container"></div>

		<script type="text/x-handlebars-template" id="boats-form-template">
				<label class="dgreyb">{{task}} boat</label>
				<div class="padder">
					<form id="{{task}}-boats-form" accept-charset="utf-8">
					{{#if update}}
						<span class="box-tool redb remove-boat" style="color: white;">Remove</span>
					{{/if}}
						<div class="form-row">
							<label class="field-label">Boat Name</label>
							<input type="text" name="name" value="{{{name}}}">
						</div>
						<div class="form-row">
							<label class="field-label">Boat Description</label>
							<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
						</div>
						<div class="form-row">
							<label class="field-label">Boat Capacity</label>
							<input type="number" name="capacity" value="{{capacity}}" placeholder="0" style="width: 100px;" min="0">
						</div>
						<div class="form-row">
							<div id="room-types">
							<h3>Boat rooms</h3>
								{{#each boatrooms}}
									{{> boatroom_show}}
								{{/each}}
							</div>
							<button id="add-room" class="bttn greenb"> &plus; Add room</button>
						</div>
						{{#if update}}
						<input type="hidden" name="id" value="{{id}}">
						{{/if}}
						<input type="hidden" name="_token">
						<input type="submit" class="bttn blueb big-bttn" id="{{task}}-boat" value="{{task}} Boat">
					</form>
				</div>
		</script>

		<script type="text/x-handlebars-template" id="add-room-template">
			<p>
				<select class="room-type-select"
				onchange="$(this).siblings('input').attr('name', 'boatrooms['+ $(this).val() +'][capacity]');">
				{{#each boatrooms}}
					<option value="{{id}}">{{name}}</option>
				{{/each}}
				</select>
				Number of Beds:
				<input type="number" name="boatrooms[{{firstID boatrooms}}][capacity]" value="{{capacity}}" placeholder="0" style="width: 100px;" min="0">
				<button class="bttn redb remove-room">&nbsp;&#215;&nbsp;</button>
			</p>
		</script>

		<script type="text/x-handlebars-template" id="show-room-template">
			<p>
				<span class="boatroom-name">{{name}}</span>
				Number of Beds:
				<input type="number" name="boatrooms[{{id}}][capacity]" value="{{pivot.capacity}}" placeholder="0" style="width: 100px;" min="0">
				<button class="bttn redb remove-room">&nbsp;&#215;&nbsp;</button>
			</p>
		</script>

		<div style="display:none" class="box70" id="boatrooms-form-container">
			<script type="text/x-handlebars-template" id="boatrooms-form-template">
				<label class="dgreyb">{{task}} room</label>
				<div class="padder">
				<form id="{{task}}-boatrooms-form" accept-charset="utf-8">
					{{#if update}}
						<span class="box-tool redb remove-boatroom" style="color: white;">Remove</span>
					{{/if}}
					<div class="form-row">
						<label class="field-label">Room type</label>
						<input type="text" name="name" value="{{{name}}}">
					</div>
					<div class="form-row">
						<label class="field-label">Room Description</label>
						<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
					</div>
					{{#if update}}
					<input type="hidden" name="id" value="{{id}}">
					{{/if}}
					<input type="hidden" name="_token">
					<input type="submit" class="bttn blueb big-bttn" id="{{task}}-boatroom" value="{{task}} Room type">
				</form>
				</div>
			</script>

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
</div>

<script src="/dashboard/js/Controllers/Boat.js"></script>
<script src="/dashboard/js/Controllers/Boatroom.js"></script>
<script src="tabs/boats/js/script.js"></script>
