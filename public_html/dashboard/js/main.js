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
    		console.log(xhr.status + " " + xhr.statusText + ": /" + options.url);
    		alert(xhr.status + " " + xhr.statusText + ":\n\n/" + options.url);
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
