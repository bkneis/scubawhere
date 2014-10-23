$(function(){
	CKEDITOR.replace( 'description' );


	$("#trip-form").delegate("#add24", "click", function(e){
		if($.isNumeric($("[name='duration']").val())){

			var curDur = parseInt($("[name='duration']").val(), 10);
			var newDur = curDur + 24;

		}else{
			var newDur = 24;
		}

		$("[name='duration']").val(newDur);
		setReadableDuration();
		e.preventDefault();
	});

	$("#trip-form").delegate("#remove24", "click", function(e){
		if($.isNumeric($("[name='duration']").val())){

			var curDur = parseInt($("[name='duration']").val(), 10);
			var newDur = curDur - 24;

			if(newDur < 0){
				var newDur = 0;
			}

		}else{
			var newDur = 0;
		}

		$("[name='duration']").val(newDur);
		setReadableDuration();
		e.preventDefault();
	});

	$( "[name='duration']" ).bind("propertychange keyup input paste", function(){
		setReadableDuration();
	});


	//set the trip types with the hadlebars template
	var typeSource = $("#trip-type").html();
	var typeTemplate = Handlebars.compile(typeSource);

	$.ajax({
		url: "/company/triptypes",
		type: "GET",
		dataType: "json",
		success: function(data){
			$.each(data, function(){
				$("#trip-types").append(typeTemplate(this));
			});
		}
	});

	//add new tickets

});

function setReadableDuration(){

	var readString;
	var totalHours = parseInt($("[name='duration']").val(), 10);

	if($.isNumeric(totalHours)){
		var hours = totalHours % 24;
		var days = Math.floor(totalHours / 24);
		readString = days + " days and " + hours + " hours.";
	}else{
		readString = "No a valid number..";
	}



	$("#duration-readable").html(readString);
}
