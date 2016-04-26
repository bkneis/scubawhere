// WORK IN PROGRESS

"use strict";
class AssetController extends AssetModel {

	constructor(modelName) {
		super(modelName);
		this.list = Handlebars.compile( $('#' + this.modelName + '-list-template').html() );
		this.form = Handlebars.compile( $('#' + this.modelName +  '-form-template').html() );
		this.formatEditData = undefined;
	}

	renderList(callback) {
		$('#' + this.modelName + '-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

		this.getAll(function success(data) {

			window.assets[this.modelName] = _.indexBy(data, 'id');
			//window.agents = _.indexBy(data, 'id');
			$('#' + this.modelName + '-list').remove();
			$('#' + + this.modelName + + '-list-container .loader').remove();
			//$('#' + this.modelName + '-list-container').append( this.list({agents : data}) );
			$('#' + this.modelName + '-list-container').append( this.list({objects : data}) );

			// (Re)Assign eventListener for agent clicks
			$('#' + this.modelName + '-list').on('click', 'li, strong', function(event) {

				if( $(event.target).is('strong') )
					event.target = event.target.parentNode;

				this.renderEditForm( event.target.getAttribute('data-id') );
			});

			if( typeof callback === 'function')
				callback();
		});
	}

	renderEditForm(id) {
		if( unsavedChanges() ) {
			var question = confirm("ATTENTION: All unsaved changes are lost!");
			if( !question) return false;
		}

		var asset = {};

		this.formatEditData(id, asset);

		$('#boat-form-container').empty().append( this.form({object : asset}) );

		if(!id) $('input[name=name]').focus();

		CKEDITOR.replace( 'description' );

		setToken('[name=_token]');

		// Set up change monitoring
		$('form').on('change', 'input, select, textarea', function() {
			$('form').data('hasChanged', true);
		});
	}

	clearForm() {
		let asset;

		asset = {
			task: 'add',
			update: false
		};

		$('#' + this.modelName + '-form-container').empty().append( this.form(asset) );

		$('input[name=name]').focus();

		CKEDITOR.replace( 'description' );

		setToken('[name=_token]');

		// Set up change monitoring
		$('form').on('change', 'input, select, textarea', function() {
			$('form').data('hasChanged', true);
		});
	}
}

var test = new AssetController("agent");

console.log(test.modelName);