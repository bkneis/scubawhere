<div id="wrapper" class="clearfix">
	<div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default" id="packages-list-div">
				<div class="panel-heading">
					<h4 class="panel-title">Available Courses</h4>
				</div>
				<div class="panel-body" id="course-list-container">
					<button id="change-to-add-course" class="btn btn-success text-uppercase">&plus; Add Course</button>
					<script type="text/x-handlebars-template" id="course-list-template">
						<ul id="course-list" class="entity-list">
							{{#each courses}}
								<li data-id="{{id}}"><strong>{{{name}}}</strong> | {{count trainings}} classes | {{count tickets}} tickets</li>
							{{else}}
								<p id="no-courses">No courses available.</p>
							{{/each}}
						</ul>
					</script>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default" id="course-form-container">
				<script type="text/x-handlebars-template" id="course-form-template">
					<div class="panel-heading">
						<h4 class="panel-title">{{task}} Course</h4>
					</div>
					<div class="panel-body">
						<form id="{{task}}-course-form">
							<div class="form-row">
								{{#if update}}
									<span class="btn btn-danger pull-right remove-course">Remove</span>
								{{/if}}
								<label class="field-label">Course Name : <span class="text-danger">*</span></label>
								<input id="course-name" type="text" name="name" value="{{{name}}}">
							</div>

							<div class="form-row">
								<label class="field-label">For Certification</label>
								<select id="select-certification" name="certificate_id">
									<option value="">-</option>
									{{#each affiliated_agencies}}
										<optgroup label="{{abbreviation}}">
											{{#each certificates}}
												<option value="{{id}}"{{selected ../../certificate_id}}>{{{name}}}</option>
											{{/each}}
										</optgroup>
									{{/each}}
								</select>
							</div>

							<div class="form-row">
								<label class="field-label">Course Description</label>
								<textarea name="description" style="height: 243px;">{{{description}}}</textarea>
							</div>

							<div class="form-row">
								<label class="field-label">Course capacity : <span class="text-danger">*</span></label>
								<input id="course-capacity" type="number" name="capacity" value="{{capacity}}" placeholder="0" style="width: 100px;" min="0">
							</div>

							<div id="course-classes" class="form-row class-list">
								<strong>Select the classes to be included in this course:</strong>

								{{#if update}}
									{{> class_template}}
								{{else}}
									{{> class_select}}
								{{/if}}
							</div>

							<div id="course-tickets" class="form-row ticket-list">
								<strong>Select tickets to be included in this course:</strong>

								{{#if update}}
									{{> tickets_template}}
								{{else}}
									{{> ticket_select}}
								{{/if}}
							</div>

							<div id="course-base" class="form-row">
								<p><strong>Course price : <span class="text-danger">*</span></strong></p>
								{{#each base_prices}}
									{{> price_input}}
								{{/each}}
								<button class="btn btn-default btn-sm add-base-price"> &plus; Click here to add a price change for the future</button>
							</div>

							<div id="course-seasonal" class="form-row">
								<label>
									<input id="course-season-price" type="checkbox" onchange="showMe('#seasonal-prices-list', this);"{{#if prices}} checked{{/if}}>
									Add seasonal price changes?
								</label>
								<div class="dashed-border" id="seasonal-prices-list"{{#unless prices}} style="display: none;"{{/unless}}>
									<h3>Seasonal price changes</h3>
									{{#each prices}}
										{{> price_input}}
									{{else}}
										{{#with default_price}}
											{{> price_input}}
										{{/with}}
									{{/each}}
									<button class="btn btn-default btn-sm add-price"> &plus; Add another seasonal price</button>
								</div>
							</div>

							{{#if update}}
								<input type="hidden" id="course-id" name="id" value="{{id}}">
							{{/if}}
							<input type="hidden" name="_token">

							<input type="submit" class="btn btn-primary btn-lg text-uppercase pull-right" id="{{task}}-course" value="SAVE">

						</form>
					</div>
				</script>
			</div>
		</div>
	</div><!-- .row -->

	<script type="text/x-handlebars-template" id="ticket-select-template">
		<p>
			{{#notEmptyObj available_tickets}}
				<select class="ticket-select">
					<option value="0">Select a ticket</option>
					{{#each available_tickets}}
						<option value="{{id}}"
							{{#if ../existing}}
								{{selected ../../id}}
							{{/if}}
						>{{{name}}}</option>
					{{/each}}
				</select>
				Quantity: &nbsp;<input type="number" class="quantity-input"{{#if pivot.quantity}} name="tickets[{{id}}][quantity]"{{else}} disabled{{/if}} value="{{pivot.quantity}}" min="1" step="1" style="width: 50px;">
			{{else}}
				<div class="alert alert-danger clearfix">
					<i class="fa fa-exclamation-triangle fa-3x fa-fw pull-left"></i>
					<p class="pull-left">
						<strong>No tickets available!</strong><br>
						Please go to <a href="#tickets">Tickets</a> to create tickets for your courses.
					</p>
				</div>
			{{/notEmptyObj}}
		</p>
	</script>

	<script type="text/x-handlebars-template" id="tickets-template">
		{{#each tickets}}
			<p>
				<big class="margin-right">{{{name}}}</big> Quantity: <big class="margin-right">{{pivot.quantity}}</big>
			</p>
		{{else}}
			<p>-</p>
		{{/each}}
	</script>

	<script type="text/x-handlebars-template" id="class-select-template">
		<p>
			{{#notEmptyObj available_trainings}}
				<select id="class-select" class="class-select">
					<option value="0">Select a class</option>
					{{#each available_trainings}}
						<option value="{{id}}">{{name}}</option>
					{{/each}}
				</select>
				Number of sessions: &nbsp;<input type="number" class="quantity-input"{{#if pivot.quantity}} name="trainings[{{id}}][quantity]"{{else}} disabled{{/if}} value="{{pivot.quantity}}" min="1" step="1" style="width: 50px;">
			{{else}}
				<div class="alert alert-danger clearfix">
					<i class="fa fa-exclamation-triangle fa-3x fa-fw pull-left"></i>
					<p class="pull-left">
						<strong>No classes available!</strong><br>
						Please go to <a href="#classes">Classes</a> to create classes for your courses.
					</p>
				</div>
			{{/notEmptyObj}}
		</p>
	</script>

	<script type="text/x-handlebars-template" id="class-template">
		{{#each trainings}}
			<p>
				<big class="margin-right">{{{name}}}</big> Quantity: <big class="margin-right">{{pivot.quantity}}</big>
			</p>
		{{else}}
			<p>-</p>
		{{/each}}
	</script>

	<script type="text/x-handlebars-template" id="price-input-template">
		<p{{#unless decimal_price}} class="new_price"{{/unless}}>
            <span class="currency">{{currency}}</span>
            <input type="number" id="acom-price" name="prices[{{id}}][new_decimal_price]" placeholder="00.00" min="0" step="0.01" value="{{decimal_price}}" style="width: 100px;">

            {{#unless isAlways}}
                from <input type="text" name="prices[{{id}}][from]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{from}}" style="width: 125px;">
            {{else}}
                from <strong>the beginning of time</strong>
                <input type="hidden" name="prices[{{id}}][from]" value="0000-00-00">
            {{/unless}}

            {{#unless isBase}}
                until <input type="text" name="prices[{{id}}][until]" class="datepicker" data-date-format="YYYY-MM-DD" value="{{until}}" style="width: 125px;">
            {{/unless}}

            <button class="btn btn-danger remove-price">&#215;</button>
		</p>
	</script>

	<script type="text/x-handlebars-template" id="errors-template">
		<div class="yellow-helper errors" style="color: #E82C0C;">
			<strong>There are a few problems with the form:</strong>
			<ul>
				{{#each errors}}
					<li>{{{this}}}</li>
				{{/each}}
			</ul>
		</div>
	</script>

    <link rel="stylesheet" type="text/css" href="/css/bootstrap-tour-standalone.min.css">
    <script type="text/javascript" src="/js/bootstrap-tour-standalone.min.js"></script>    
	<script type="text/javascript" src="/js/tour.js"></script>
	<script src="/tabs/courses/js/script.js"></script>
</div>
