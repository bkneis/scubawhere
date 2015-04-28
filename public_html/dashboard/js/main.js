// Interactions with the API
// Shim for up to IE8
if (!Date.now) {
	Date.now = function() { return new Date().getTime(); }
}

$.ajaxSetup({
	beforeSend: function(xhr, options) {
		// Disable caching for API requests by default
		if(options.url.substr(0, 4) === '/api' && options.type !== 'POST') {
			$.extend(this, {
				url: options.url + (options.url.indexOf('?') === -1 ? '?_=' : '&_=') + Date.now(),
				cache: false,
			});
		}

		// Enable caching for .js scripts by default
		/*else if(options.dataType === 'script') {
			// Remove '?_={random number}'' from the request url
			$.extend(this, {
				url: options.url.split('?_=')[0],
				cache: true,
			});
}*/

		// Manually trigger progress bar for tab loads, which have been set to global:false
		if(options.url.indexOf('index.php') > -1)
			NProgress.start();

		// Since the help tab does not include API requests, the progress bar needs to be manually stopped
		if(options.url.indexOf('help/index.php') > -1)
			$.extend(this, {
				complete: function() {
					NProgress.done();
				}
			});
	}
});

// Set up hearbeats to fire every minute
window.setInterval(function() {
       Company.sendHeartbeat({'h': 1});
}, 60000);

// Run on page load
$(function(){

	// Error handling
	$(document).ajaxComplete(function(event, xhr, options) {
		if(xhr.status >= 400) {
			pageMssg('<strong>' + xhr.status + ' ' + xhr.statusText + '</strong> - No separate error message? Contact the developer!', 'info');
		}

		if(xhr.status === 503) {
			// Maintenance mode
			pageMssg('<strong>The application is in maintenance mode.</strong> Please check back in a few minutes.', 'warning');
		}
	});

	$(document).ajaxStart(function() {
		NProgress.start();
		var interval = 400 + Math.random() * 400;
		window.sw.nProgressInterval = window.setInterval(function(){NProgress.inc();}, interval);
	});
	$(document).ajaxStop(function() {
		window.clearInterval(window.sw.nProgressInterval);
		NProgress.done();
	});

	Company.getNotifications(function sucess(data) {
		//var notificationTemplate = Handlebars.compile( $("#notification-message-template").html() );
		window.notifications = data; //_.indexBy('id', createNotifications(data)); // change to time
		//$('#notification-messages').append(notificationTemplate({notifications : window.notifications}));
	});

	$(".notifications .messages").hide();
	$(".notifications").click(function() {
		if($(this).children(".messages").children().length > 0) {
			$(this).children(".messages").fadeToggle(300);
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
		url: "/api/token",
		type: "GET",
		success: function(data){
			window.token = data;
			if(typeof callback === 'function') callback(window.token);
		}
	});

	return false;
}

function setToken(element) {
	getToken(function(token) {
		$(element).val(token);
	});
}

function reproColor(id) { // Stands for: reproducible color

	// Colors from http://clrs.cc

	var colors = [ /* 14 options */
	{bgcolor: '#001F3F', txtcolor: '#FFFFFF'}, /* navy */
	{bgcolor: '#0074D9', txtcolor: '#FFFFFF'}, /* blue */
	{bgcolor: '#7FDBFF', txtcolor: '#000000'}, /* aqua */
	{bgcolor: '#39CCCC', txtcolor: '#000000'}, /* teal */
	{bgcolor: '#3D9970', txtcolor: '#000000'}, /* olive */
	{bgcolor: '#2ECC40', txtcolor: '#000000'}, /* green */
	{bgcolor: '#01FF70', txtcolor: '#000000'}, /* lime */
	{bgcolor: '#FFDC00', txtcolor: '#000000'}, /* yellow */
	{bgcolor: '#FF851B', txtcolor: '#000000'}, /* orange */
	{bgcolor: '#FF4136', txtcolor: '#FFFFFF'}, /* red */
	{bgcolor: '#85144B', txtcolor: '#FFFFFF'}, /* maroon */
	{bgcolor: '#F012BE', txtcolor: '#FFFFFF'}, /* fuchsia */
	{bgcolor: '#B10DC9', txtcolor: '#FFFFFF'}, /* purple */
	{bgcolor: '#DDDDDD', txtcolor: '#000000'}, /* silver */
	];

	var length = colors.length;

	if(id === undefined) // return default
		return colors[0];

	return colors[ (id % length) ];
}

function createNotifications(data) {
	// handle data from api call and create notification messages
	return data;

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
	var chars         = "ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 15;
	var result        = '';

	for (var i = 0; i < string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		result += chars.substring(rnum, rnum+1);
	}

	if(_.indexOf(window.sw.randomStrings, result) >= 0)
	{
		// If the random string is not unique (unlikely, but possible) the function recursively calls itself again
		return randomString();
	}

	// When the random string has been approved as unique, it is added to the list of generated strings and then returned
	window.sw.randomStrings.push(result);
	return result;
}

Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {

	switch (operator) {
		case '==':
		return (v1 == v2) ? options.fn(this) : options.inverse(this);
		case '===':
		return (v1 === v2) ? options.fn(this) : options.inverse(this);
		case '<':
		return (v1 < v2) ? options.fn(this) : options.inverse(this);
		case '<=':
		return (v1 <= v2) ? options.fn(this) : options.inverse(this);
		case '>':
		return (v1 > v2) ? options.fn(this) : options.inverse(this);
		case '>=':
		return (v1 >= v2) ? options.fn(this) : options.inverse(this);
		case '&&':
		return (v1 && v2) ? options.fn(this) : options.inverse(this);
		case '||':
		return (v1 || v2) ? options.fn(this) : options.inverse(this);
		default:
		return options.inverse(this);
	}
});

Handlebars.registerHelper('unlessCond', function (v1, operator, v2, options) {

	switch (operator) {
		case '==':
		return (v1 == v2) ? options.inverse(this) : options.fn(this);
		case '===':
		return (v1 === v2) ? options.inverse(this) : options.fn(this);
		case '<':
		return (v1 < v2) ? options.inverse(this) : options.fn(this);
		case '<=':
		return (v1 <= v2) ? options.inverse(this) : options.fn(this);
		case '>':
		return (v1 > v2) ? options.inverse(this) : options.fn(this);
		case '>=':
		return (v1 >= v2) ? options.inverse(this) : options.fn(this);
		case '&&':
		return (v1 && v2) ? options.inverse(this) : options.fn(this);
		case '||':
		return (v1 || v2) ? options.inverse(this) : options.fn(this);
		default:
		return options.inverse(this);
	}
});
