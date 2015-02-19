//interactions with the API

$.ajaxSetup({
	// Disable caching of AJAX responses
	cache: false,
	beforeSend: function(xhr, options) {

		// Only continue if we have to remap a API request
		if(options.url.substr(0, 4) !== '/api') return true;

		// Figure out correct url prefix
		var prefix = window.location.hostname === 'rms.scubawhere.com' ? 'api' : 'api-test';

		// Start new AJAX request with changed url
		$.ajax(
			$.extend(this, {
				url: '//' + prefix + '.scubawhere.com' + options.url
			})
		);

		// Cancel original request
		return false;
	}
});

//run on page load
$(function(){

	// Error handling
	$(document).ajaxComplete(function(event, xhr, options) {
		if(xhr.status >= 400) {
			pageMssg('<b>' + xhr.status + ' ' + xhr.statusText + '</b> - No separate error message? Contact the developer!', 'info');
		}
	});

	$("#logout").click(function(e){
		$.ajax({
			url: "/api/logout",
			type: "GET",
			dataType: "json",
			success: function(log) {

				location = '/';

				window.location.href = location;
			}
		});
		e.preventDefault();
	});

	//token
	if(typeof window.token === 'undefined')
		getToken();
});

//************************************
// FUNCTIONS
//************************************

function getToken(callback) {
	if(typeof window.token === 'string' && window.token.length > 0) {
		if(typeof callback === 'function') callback(window.token);
		return window.token;
	}

	$.ajax({
		url: "/token",
		type: "GET",
		success: function(data){
			window.token = data;
			if(typeof callback === 'function') callback(window.token);
		}
	});

	return false;
}

function reproColor(id) { // Stands for: reproducible color

	var colors = [ // 14 options
		{bgcolor: '#001F3F', txtcolor: '#FFFFFF'}, // navy
		{bgcolor: '#0074D9', txtcolor: '#FFFFFF'}, // blue
		{bgcolor: '#7FDBFF', txtcolor: '#000000'}, // aqua
		{bgcolor: '#39CCCC', txtcolor: '#000000'}, // teal
		{bgcolor: '#3D9970', txtcolor: '#000000'}, // olive
		{bgcolor: '#2ECC40', txtcolor: '#000000'}, // green
		{bgcolor: '#01FF70', txtcolor: '#000000'}, // lime
		{bgcolor: '#FFDC00', txtcolor: '#000000'}, // yellow
		{bgcolor: '#FF851B', txtcolor: '#FFFFFF'}, // orange
		{bgcolor: '#FF4136', txtcolor: '#FFFFFF'}, // red
		{bgcolor: '#85144B', txtcolor: '#FFFFFF'}, // maroon
		{bgcolor: '#F012BE', txtcolor: '#FFFFFF'}, // fuchsia
		{bgcolor: '#B10DC9', txtcolor: '#FFFFFF'}, // purple
		{bgcolor: '#DDDDDD', txtcolor: '#000000'}, // silver
	];

	var length = colors.length;

	if(id === undefined) // return default
		return colors[0];

	return colors[ (id % length) ];
}

function colorOpacity(hex, opa) {

	// validate hex string
	hex = String(hex).replace(/[^0-9a-f]/gi, '');
	if (hex.length < 6) {
		hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
	}
	opa = opa || 1;

	// convert to decimal and change luminosity
	var rgb = "rgba(", c, i;
	for (i = 0; i < 3; i++) {
		c = parseInt(hex.substr(i*2,2), 16);
		rgb += c + ', ';
	}
	rgb += opa + ')';

	return rgb;
}

window.sw.randomStrings = [];
function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 15;
	var result = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		result += chars.substring(rnum,rnum+1);
	}

	if(_.indexOf(window.sw.randomStrings, result) >= 0)
	{
		// If the random string is not unique (unlikely, but possible) the function recursively calls itself again
		return randomString();
	}
	else
	{
		// When the random string has been approved as unique, it is added to the list of generated strings and then returned
		window.sw.randomStrings.push(result);
		return result;
	}
}
