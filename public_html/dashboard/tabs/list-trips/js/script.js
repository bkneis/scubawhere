$(function(){
	var tripSource = $("#trip").html(); 
	var tripTemplate = Handlebars.compile(tripSource);
	
	$.ajax({
			url: "/company/trips",
			type: "GET",
			dataType: "json",
			async: false,
			success: function(data){
				$.each(data, function(){
					console.log(tripTemplate(this));
					$("#trips").append(tripTemplate(this));
				});
				
			}
		});
});