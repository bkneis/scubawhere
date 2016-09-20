<div id="wrapper" class="clearfix">

	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div id="markers-info" class="panel-heading">
					<h4 class="panel-title">
						<img src="//mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1" style="height: 1.5em; margin: -0.3em 0 -0.2em 0.5em; display: none;" id="legend-your-locations-icon" />
						<span class="loader" id="legend-your-locations-loader" style="top: 0;"></span>
						Your locations

						<img src="//mt.googleapis.com/vt/icon?color=ff004C13&name=icons/spotlight/spotlight-waypoint-blue.png&scale=1" style="height: 1.5em; margin: -0.3em 0 -0.2em 0.5em; display: none;" id="legend-available-locations-icon" />
						<span class="loader" id="legend-available-locations-loader" style="top: 0; margin-left: 2em;"></span>
						Available locations

						<img src="//mt.googleapis.com/vt/icon?psize=30&font=fonts/arialuni_t.ttf&color=ff304C13&name=icons/spotlight/spotlight-waypoint-a.png&ax=43&ay=48&text=%E2%80%A2&scale=1" style="height: 1.5em; margin: -0.3em 0 -0.2em 0.5em;" />
						New location
					</h4>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div id="add-location" class="panel">
				<div class="panel-heading text-right" style="padding: 0 1px;">
					Lat: <input type="number" placeholder="Latitude" step="0.1" min="-90" max="90" id="newMarkerLatitude" class="form-control" style="display: inline-block; width: 115px;" />
					Long: <input type="number" placeholder="Longitude" step="0.1" min="-180" max="180" id="newMarkerLongitude" class="form-control" style="display: inline-block; width: 115px;" />
					<button class="btn btn-default" style="margin-right: 5px;" id="showLocation">Show</button>
					<button class="btn btn-primary" id="createLocation">Create</button>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

		<div id="map-container" class="col-md-12">
			<div id="map" style="height: 100%;"></div>
		</div>
	</div><!-- .row -->

	<div id="modalWindows" style="height: 0;">
		<script id="location-template" type="text/x-handlebars-template">
			<div id="modal-{{id}}" class="reveal-modal">
				<p style="text-transform: uppercase; float: right; line-height: 2.8em;">
					{{#if attached}}
						Your location
					{{else}}
						Available location
					{{/if}}
				</p>
				<h3>{{{name}}}</h3>
				<span>{{latitude}}, {{longitude}}</span>

				<form id="edit-description-form">
					<h4>
						Description
						<p class="pull-right" style="margin-top: -5px;"{{#unless attached}} title="You need to add this location to your locations to be able to change the description."{{/unless}}>
							<input type="submit" value="Edit description" class="btn btn-sm btn-default"{{#unless attached}} disabled{{/unless}}>
						</p>
					</h4>
					<div id="description-container">
						{{#if pivot.description}}
							{{{pivot.description}}}
						{{else}}
							{{{description}}}
						{{/if}}
					</div>
				</form>

				<h4>Tags</h4>
				<div class="clearfix">
					{{#each tags}}
						<div class="tag"><strong>{{name}}</strong></div>
					{{else}}
						<h5 class="text-center text-muted" style="margin-bottom: 2em;">
							<img src="/common/img/lightsaber.png" style="margin-top: -1em; margin-left: -2em;">
							These arent the tags you are looking for&#8230;
						</h5>
					{{/each}}
				</div>

				<form>
					{{#if attached}}
						<input type="submit" value="Remove from your locations" class="detach-location btn btn-danger" />
					{{else}}
						<input type="submit" value="Add to your locations" class="attach-location btn btn-primary btn-lg" />
					{{/if}}
				</form>

				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>

		<script id="new-location-template" type="text/x-handlebars-template">
			<div id="modal-new" class="reveal-modal">
				<h4 class="margin-top: 0;">New location</h4>
				<form id="create-location-form">
					<div class="form-row">
						<label>Name</label>
						<input type="text" name="name" id="new-location-name" />
					</div>
					<div class="form-row">
						<label class="field-label">Description</label>
						<textarea name="description" id="description"></textarea>
					</div>
					<div class="form-row">
						<div class="field-label">Tags</div>
						<div class="clearfix">
							{{#each available_tags}}
								<label class="tag">
									<input type="checkbox" name="tags[]" value="{{id}}" onchange="changeParent(this)">
									<strong>{{name}}</strong>
								</label>
							{{/each}}
						</div>
					</div>

					<input type="hidden" name="latitude" value="{{latitude}}" />
					<input type="hidden" name="longitude" value="{{longitude}}" />

					<div style="margin-top: 1em; text-align: right">
						<a class="close-modal" title="Abort" style="margin-right: 2em;">Cancel</a>
						<input type="submit" value="Create" class="add-location btn btn-primary btn-lg" />
					</div>
				</form>

				<a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
			</div>
		</script>
	</div>

	<script>
		function loadGoogleMaps() {
			console.log('Start loading');

			if( window.google && google.maps ) {
				console.log('Google Maps already loaded');
				initialise();

				return true;
			}

			var key = '';
			if(window.location.host === 'rms.scubawhere.com') key = '&key=AIzaSyAzTfKvssUjEK4Reyg3RD7lBAT6vGZG4hk';

			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = '//maps.googleapis.com/maps/api/js?v=3.exp&callback=initialise' + key;

			document.body.appendChild(script);

			console.log('Google Maps script appended');
		}
	</script>

	<script src="/common/js/rAF.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-tour-standalone.min.css">
    <script type="text/javascript" src="/js/bootstrap-tour-standalone.min.js"></script>    
	<script type="text/javascript" src="/js/tour.js"></script>

	<script src="/tabs/locations/js/script.js"></script>
</div>
