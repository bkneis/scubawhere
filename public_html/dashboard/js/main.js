//interactions with the API
//run on page load
$(function(){

	$.ajaxSetup({
		// Disable caching of AJAX responses
		cache: false
	});

	// Error handling
	$(document).ajaxComplete(function(event, xhr, options) {
		/*
		console.log(event);
		console.log(xhr);
		console.log(options);
		*/
		if(xhr.status == 404) {

    		if(xhr.responseText.length > 100) // Filter out HTML responses
    			xhr.responseText = '';
    		else
    			xhr.responseText += '\n';

			console.log(xhr.status + " " + xhr.statusText + ": " + xhr.responseText + " - " + options.url);
			alert(xhr.status + " " + xhr.statusText + ":\n\n" + xhr.responseText + options.url);
		}
		else if(xhr.status >= 400) {
			alert(xhr.status + " " + xhr.statusText + ":\n\n" + xhr.responseText);
		}
	});

	$("#logout").click(function(e){
		$.ajax({
			url: "/logout",
			type: "GET",
			dataType: "json",
			success: function(log){
				window.location.href = "/dashboard/login/";
			}
		});
		e.preventDefault();
	});

	//token
	if(!window._token)
		getToken();
});

//************************************
// FUNCTIONS
//************************************

function getToken() {
	if(window.token || window._token)
		return window.token || window._token;

	$.ajax({
		url: "/token",
		type: "GET",
		async: false,
		success: function(data){
			window._token = data;
			window.token  = data;
			return data;
		}
	});
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
