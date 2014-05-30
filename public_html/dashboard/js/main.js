var _token;

//interactions with the API
//run on page load
$(function(){

	$(document).ajaxComplete(function(event, xhr, options) {
		/*
		console.log(event);
		console.log(xhr);
		console.log(options);
		*/
		if(xhr.status == 404) {
    		console.log(xhr.status + " " + xhr.statusText + ": " + xhr.responseText + " - " + options.url);
    		alert(xhr.status + " " + xhr.statusText + ":\n\n" + xhr.responseText + "\n\n" + options.url);
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
	_token = $.ajax({
		url: "/token",
		type: "GET",
		dataType: "html",
		async: false,
		success: function(data){
			_token = data;
		}
	});

	setPage();

});

//************************************
						//FUNCTIONS
//************************************
function setPage(){
	$.ajax({
        url: "/company",
        dataType: "json",
        type: "GET",
        async: false,
        success: function(data){
        	$("#dc-name").html("Name: " + data.name);
        	$("#dc-uname").html("Username: " + data.username);
	        $("#dc-veri").html("Verified: " + data.verified);
        },
        error: function(){
	        window.location.href = "/dashboard/login/";
        }
    });
}

function reproColor(id) { // Stands for: reproducible color

	var colors = [ // 14 options
		{bgcolor: '#001F3F', txtcolor: 'white'}, // navy
		{bgcolor: '#0074D9', txtcolor: 'white'}, // blue
		{bgcolor: '#7FDBFF', txtcolor: 'black'}, // aqua
		{bgcolor: '#39CCCC', txtcolor: 'black'}, // teal
		{bgcolor: '#3D9970', txtcolor: 'black'}, // olive
		{bgcolor: '#2ECC40', txtcolor: 'black'}, // green
		{bgcolor: '#01FF70', txtcolor: 'black'}, // lime
		{bgcolor: '#FFDC00', txtcolor: 'black'}, // yellow
		{bgcolor: '#FF851B', txtcolor: 'white'}, // orange
		{bgcolor: '#FF4136', txtcolor: 'white'}, // red
		{bgcolor: '#85144B', txtcolor: 'white'}, // maroon
		{bgcolor: '#F012BE', txtcolor: 'white'}, // fuchsia
		{bgcolor: '#B10DC9', txtcolor: 'white'}, // purple
		{bgcolor: '#DDDDDD', txtcolor: 'black'}, // silver
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
