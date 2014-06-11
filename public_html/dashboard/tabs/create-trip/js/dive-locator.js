var token;

$(function(){
	$.ajax({
		url: "/token",
		type: "GET",
		dataType: "html",
		async: false,
		success: function(data){
			token = data;
			$("[name='_token']").val(token);
		}
	});

	$("body").delegate("#create-trip", "click", function(e){
		e.preventDefault();

		if(!validateTrip()){
			console.log("error");
		}

		$.ajax({
			url: "/company/add-trip",
			type: "POST",
			dataType: "json",
			data: $('form#trip-form').serialize(),
			async: false,
			success: function(data){
				//successfull so reload this content and show success message
				pageMssg("Trip created. You can now activate it in 'Activate Trip'.", true);

				//load in new content
				//force it by unsetting and setting hash
				window.location.hash = "";
				$("#wrapper").html(LOADER);

				window.location.hash = "create-trip";

			}
		});
	});

	var spotSource = $("#selected-spot").html();
	var spotTemplate = Handlebars.compile(spotSource);

	$("body").delegate(".add-location", "click", function(){
		//append this to list of selected locations

		var thisLocation	 = $(this).attr("data-location").split(",");
		var thisLocationName = thisLocation[0];
		var thisLocationID 	 = thisLocation[1];
		var thisLocationLat  = thisLocation[2];
		var thisLocationLng  = thisLocation[3];
		var spotData = {name: thisLocationName, id: thisLocationID, longitude: thisLocationLng, latitude: thisLocationLat};

		//if it isnt already selected

		$("#selected-spots").append(spotTemplate(spotData));

	});

	$("body").delegate(".remove-location", "click", function(){

	});

	$("#locations-list").html("");//refresh list

	var locSource = $("#locations").html();
	var locTemplate = Handlebars.compile(locSource);

	$.ajax({
		url: "/company/locations",
		type: "GET",
		dataType: "json",
		data: {latitude:0, longitude:0, limit:1000, _token: token},
		async: false,
		success: function(data){
			$.each(data, function(){

				$("#locations-list").append(locTemplate(this));

				$("#hidden-spots").append("<input type='hidden' name='locations[]' value='" + this.id + "'>");

			});

		}
	});

});

function validateTrip(){
	var bool = true;

	for ( instance in CKEDITOR.instances ){
    CKEDITOR.instances[instance].updateElement();
	}

	$("#cke_description").css("border-color", "");//reset the ckeditors border colo

	var validName 			= $("[name=name]").validateField(5, 100);
	var validDuration 		= $("[name=duration]").validateNumericField(0, 999999);//no maximum
	var validDescription 	= $("[name=description]").validateField(5, 3000);
	if(!validDescription){$("#cke_description").css("border-color", "red");}//oesnt work with present validate script

	function validTripType(){
		var validTT = true;
		$("#type-error").html("");
		if(!jQuery('[name="triptypes[]"]:checked').length){
			validPU = false;
			$("#type-error").html("<div class='red-helper'>Please select at least one .</div>");
		}

		return validTT;
	}
	validTripType();//complete actions
	if(validName && validDuration && validDescription && validTripType()){
		bool = false;
	}else{
		pageMssg("There are errors.", false);
	}

	return bool;
}
