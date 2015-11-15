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
			// pageMssg('<strong>' + xhr.status + ' ' + xhr.statusText + '</strong> - No separate error message? Contact the developer!', 'info');

			var data = JSON.parse(xhr.responseText);
			if(data.error) pageMssg('<b>' + data.error.message + '</b> in ' + data.error.file + ':' + data.error.line, 'danger', true);
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
		var notificationTemplate = Handlebars.compile( $("#notification-message-template").html() );
		window.notifications = createNotifications(data);
		$('#notification-messages').append(notificationTemplate({notifications : window.notifications}));
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
var tokenRequestUnderway = false;
var tokenRequestCallbacks = [];

function getToken(callback) {
	if(typeof window.token === 'string' && window.token.length > 0) {
		if(typeof callback === 'function') callback(window.token);
		return window.token;
	}

	tokenRequestCallbacks.push(callback);

	if(tokenRequestUnderway) {
		return false;
	}

	window.tokenRequestUnderway = true;

	$.ajax({
		url: "/api/token",
		type: "GET",
		success: function(data){
			window.token = data;
			window.tokenRequestUnderway = false;

			for(var i = 0; i < tokenRequestCallbacks.length; i++) {
				if(typeof tokenRequestCallbacks[i] === 'function') tokenRequestCallbacks[i](window.token);
			}
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
	var notifications = [];
	// console.log(data);

	// check if they have done the tour
	if(data.init) notifications.push(data.init);

	// check outstanding bookings
	for(var i = 0; i < data.overdue.length; i++) {
		notifications.push("Booking " + data.overdue[i][0] + " has " + window.company.currency.symbol + data.overdue[i][1] + " outstanding to pay");
	}

	// check bookings expiring within 30 minutes
	for(var i = 0; i < data.expiring.length; i++) {
		notifications.push("Booking " + data.expiring[i][0] + " is going to expire soon!");
	}

	return notifications;
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

Handlebars.registerHelper('compare', function (lvalue, operator, rvalue, options) {
	// Taken from http://doginthehat.com.au/2012/02/comparison-block-helper-for-handlebars-templates/#comment-44 and extended with '&&', '||' cases
	var operators, result;

	if (arguments.length < 3) {
		throw new Error("Handlerbars Helper 'compare' needs 2 parameters");
	}

	if (options === undefined) {
		options  = rvalue;
		rvalue   = operator;
		operator = "===";
	}

	operators = {
		'=='    : function (l, r) { return l == r; },
		'==='   : function (l, r) { return l === r; },
		'!='    : function (l, r) { return l != r; },
		'!=='   : function (l, r) { return l !== r; },
		'<'     : function (l, r) { return l < r; },
		'>'     : function (l, r) { return l > r; },
		'<='    : function (l, r) { return l <= r; },
		'>='    : function (l, r) { return l >= r; },
		'&&'    : function (l, r) { return l && r; },
		'||'    : function (l, r) { return l || r; },
		'typeof': function (l, r) { return typeof l == r; }
	};

	if (!operators[operator]) {
		throw new Error("Handlerbars Helper 'compare' doesn't know the operator " + operator);
	}

	result = operators[operator](lvalue, rvalue);

	if(result)
		return options.fn(this);
	else
		return options.inverse(this);

});

Handlebars.registerHelper("notEmptyObj", function (item, options) {
	return $.isEmptyObject(item) ? options.inverse(this) : options.fn(this);
});

function decRound(number, places) {

	if(places < 1) return Math.round(number);

	return Math.round(number * Math.pow(10, places)) / Math.pow(10, places);
}
