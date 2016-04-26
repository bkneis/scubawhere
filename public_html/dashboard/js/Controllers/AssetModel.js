// WORK IN PROGRESS
"use strict";
class AssetModel {

	constructor(modelName) {
		this.modelName = modelName;
		this.list = Handlebars.compile( $('#' + this.modelName + '-list-template').html() );
		this.form = Handlebars.compile( $('#' + this.modelName +  '-form-template').html() );
		this.formatEditData = undefined;
	}

	errorFn(xhr) {
		console.log(xhr);

		let data = JSON.parse(xhr.responseText);
		let errorsHTML = Handlebars.compile( $("#errors-template").html() );
		errorsHTML = errorsHTML(data);

		// Render error messages
		$('.errors').remove();
		$('#add-' + this.modelName + '-form').prepend(errorsHTML);
		$('#add-' + this.modelName).prop('disabled', false);
		$('.loader').remove();
	}

	successFn(data) {
		pageMssg(data.status, true);

		console.log(data);

		$('form').data('hasChanged', false);

		this.renderList(function() {
			this.renderEditForm(data.id);
		});
	}

	asyncCall(type, url, params, handleData, handleErr) {
		/*if(params === undefined)
			params = {}*/

		$.ajax({
			type: type,
			url: url,
			data: params,
			sucess: successFn,
			error: handleErr
		});
	}

	get(id, handleData, handleErr) {
		if(handleErr === undefined)
			handleErr = errorFn;

		if(handleData === undefined)
			handleData = successFn;

		let url = "/api/" + this.modelName;
		let params = { id: id };
		asyncCall("GET", url, params, handleData, handleErr);
	}

	getAll(handleData, handleErr) {
		if(handleErr === undefined)
			handleErr = errorFn;

		if(handleData === undefined)
			handleData = successFn;

		let url = "/api/" + this.modelName + "/all";
		asyncCall("GET", url, handleData, handleErr);
	}

	getAllWithTrashed(handleData, handleErr) {
		if(handleErr === undefined)
			handleErr = errorFn;

		if(handleData === undefined)
			handleData = successFn;

		let url = "/api/" + this.modelName + "/all-with-trashed";
		asyncCall("GET", url, handleData, handleErr);
	}

	create(params, handleData, handleErr) {
		if(handleErr === undefined)
			handleErr = errorFn;

		if(handleData === undefined)
			handleData = successFn;

		let url = "/api/" + this.modelName + "/add";
		asyncCall("POST", url, params, handleData, handleErr);
	}

	delete(id, handleData, handleErr) {
		if(handleErr === undefined)
			handleErr = errorFn;

		if(handleData === undefined)
			handleData = successFn;

		let params = { id: id };
		let url = "/api/" + this.modelName + "/delete";
		asyncCall("POST", url, params, handleData, handleErr);
	}

	update(id, params, handleData, handleErr) {
		if(handleErr === undefined)
			handleErr = errorFn;

		if(handleData === undefined)
			handleData = successFn;

		let url = "/api/" + this.modelName + "/edit";
		asyncCall("POST", url, params, handleData, handleErr);
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
