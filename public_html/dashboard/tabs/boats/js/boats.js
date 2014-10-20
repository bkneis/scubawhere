//array of room types
var roomTypes = [];

//initialise the templates as globals
var boatTemplate;
var accomTemplate;

$(function(){

	setPage();

	/* Save Room Type */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

	$("body").delegate("#saveRoom", "click", function(e){
		//returns true if good, or alerts the user if not.
		//create temp id for the room
		var rand = randomString();
		var newRoom = {name: $("[name=newRoomName]").val(), description: $("[name=newRoomDescription]").val(), id: rand};

		//append new table row
		$('#accom-body').append(accomTemplate(newRoom));

		//add this to the select list
		$(".newRoomTypeSelect").append("<option value='" + rand + "'>" + $("[name=newRoomName]").val() + "</option>");

		//add this new room to the array
		roomTypes.push(roomType(rand, $("[name=newRoomName]").val()));

		// Trigger saveAll
		$('#saveAll').click();

		e.preventDefault();
	});


	/* Save Boat Room Type */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

	$("body").delegate("#saveBoatRoom", "click", function(e){
		e.preventDefault();

		var newBoatRoomID = $(this).closest(".newBoatRoom").find("[name='newBoatRoomName']").val();
		var newBoatRoomCapacity = $(this).closest(".newBoatRoom").find("[name='newBoatRoomCapacity']").val();


		/* check that the new boat room type has a type and capacity */
		if(newBoatRoomID && newBoatRoomCapacity){
			var newBoatRoomName = $(this).closest(".newBoatRoom").find("[name='newBoatRoomName']").find(":selected").text();

			var thisBoatID = $(this).attr("data-boat-id");

			/* curent total room type set in boat*/
			var curTotalCap = 0;
			/* Boat capacity */
			var thisBoatCap = $("[name='boats[" + thisBoatID + "][capacity]']").val();

			/* loop through each capacity and calc total */
			$.each( $(this).closest('ul').find("[type='hidden']"), function() {
				curTotalCap += parseInt( this.value, 10);
			} );

			var newTotalCap = curTotalCap + parseInt(newBoatRoomCapacity, 10);


			//check that the boat isnt overfull with new capacity
			if(newTotalCap <= thisBoatCap){
				$(this).parents(".newBoatRoom")
				.parent("li").before(
					"<li>" +
						"<span><strong>" + newBoatRoomName + "</strong></span>" +
						"<span>Capacity: " + newBoatRoomCapacity + "</span>" +
						"<span class='del-boat-room redf link' data-sure='Are you sure you want to delet this boat room?'>Delete</span>" +
						"<input type='hidden'" +
								"name='boats[" + thisBoatID + "][accommodations][" + newBoatRoomID + "]'" +
								"value='" + newBoatRoomCapacity + "'>" +
					"</li>"
				);

				//set options back to default (value = 0)
				$(this).siblings("[type='text']").val("");
				$(this).parent().siblings('select option[value]').attr("selected", true);

				// Trigger saveAll
				$('#saveAll').click();
			}
			else {
				//not enough room on the boat
				pageMssg("Not enough room on the boat.", false);
				//set capacity field back to blank
				$(this).siblings("[type='text']").val("");
			}


		}
		else {
			//ask for them
			pageMssg("Please enter values for both room type and capacity.", false);
		}
	});

	/* Save Boat */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

	$("body").delegate("#saveBoat", "click", function(e){

		e.preventDefault();

		var rand = randomString();

		//returns true if good, or alerts the user if not.
		var validThree = $("[name=newBoatCapacity]").validateNumericField();

		//if true true true
		if(validThree){

			var newRoom = { newBoat: "New Boat - ",
							name: $("[name=newBoatName]").val(),
							description: $("[name=newBoatDescription]").val(),
							capacity: $("[name=newBoatCapacity]").val(),
							id: rand };

			$('#boats-wrap').append(boatTemplate(newRoom));

			//find the select field in the new boat
			$boatRoomSelect = $("[data-boat-id='" + rand + "']").find("select");

			//now it need the room types
			$.each(roomTypes, function(){
				// append these as select options for room types
				$boatRoomSelect.append("<option value='" + this.id + "'>" + this.name + "</option>");
			});

			// Trigger saveAll
			$('#saveAll').click();

		}else{
			//there was an error
		}

	});

																		/* REMOVE Functions */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	/* Remove Boat */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	//dealt with in UI.js using del-box

	/* Remove BoatRoom (Room attached to boat) */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	$("body").delegate(".del-boat-room", "click", function(){
		if($(this).isSure()){
			$(this).parent().remove();

			// Trigger saveAll
			$('#saveAll').click();
		}
	});

	/* Remove Room Type */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	$("body").delegate(".del-room", "click", function(){

		//get id of boat to be deleted
		var delRoomID = $(this).attr("id").replace("del-", "");

		//set to true if this room is being used
		var found = false;

		//check that this room type is not contained in any of the boats
		//loop through each boat wrapper
		$(".boat-wrap").each(function(){

			var dataBoatID = $(this).attr("data-boat-id");

			//find hidden input with boats[data-boat-id]accommodations[delBoatID]
			var elmFound = $(this).find("[name='boats[" + dataBoatID + "][accommodations][" + delRoomID + "]']");
			//if found then set found to true

			if(elmFound.length > 0){
				found = true;
			}
		});

		//if it hasnt been found (not already in use)
		if(!found){
			//check they are sure they want to delet this
			if($(this).isSure()){
				//remove roomtype table row
				$("#room-" + delRoomID).remove();

				//remove from array
				$.each(roomTypes, function(i){
				    if(roomTypes[i].id == delRoomID) {
				        roomTypes.splice(i,1);
				        return false;
				    }
				});

				console.log(roomTypes);

				//remove from the select lists
				$("option[value='" + delRoomID + "']").remove();

				// Trigger saveAll
				$('#saveAll').click();
			}
		}else{
			alert("This room type belongs to one or more of your boats. Please remove this room type from all boats before deleting it.");
		}

	});



	/* Save Entire Form */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

	$("body").delegate("#saveAll", "click", function(e){

		e.preventDefault();

		$.ajax({
			url: "/company/boats",
			type: "POST",
			dataType: "json",
			data: $('form#saveBoatsAndRooms').serialize(),
			async: false,
			success: function(data){
				//successfull so reload this content and show success message
				pageMssg("Save successful.", true);
				/*

				Do NOT reload the page. The advantage of using AJAX is, that the content on the page should be the same as on the server, without the need for reloading the page.

				########### OLD ###########
				//load in new content
				//force it by unsetting and setting hash
				window.location.hash = "";
				$("#wrapper").html(LOADER);

				window.location.hash = "boats";
				*/
			}
		});
	});
});

//room types obj
function roomType(id, name){ return {id: id, name: name} }

function setPage() {

	/* Compile handlebars templates */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	var boatSource = $("#boat").html();
	boatTemplate = Handlebars.compile(boatSource);
	var accomSource = $("#rooms").html();
	accomTemplate = Handlebars.compile(accomSource);

	/* Set API token in hidden input */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	_token = $.ajax({
		url: "/token",
		type: "GET",
		dataType: "html",
		async: false,
		success: function(data){
			$("[name='_token']").val(data)
		}
	});


	/* Set Boats And Room Types */
	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	$.ajax({
			url: "/company/boats",
			type: "GET",
			dataType: "json",
			success: function(data){

				// Empty the list to refill it again
				// $('.padder').empty(); // I think this would not work. To much deletion

				$.each(data.boats, function(){
					$('#boats-wrap > .padder').append(boatTemplate(this));
					$('#boat-rooms').append(accomTemplate(this));

				});

				$.each(data.accommodations, function(){
					$('#accom-body').append(accomTemplate(this));
					$(".newRoomTypeSelect").append("<option value='" + this.id + "'>" + this.name + "</option>");
					roomTypes.push( {id: this.id, name: this.name} );

				});
			},
			error: function(data){
				pageMssg("Error loading data", false);
			}
	});
}

var randomStrings = [];
function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 15;
	var result = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		result += chars.substring(rnum,rnum+1);
	}

	if(_.indexOf(randomStrings, result) >= 0)
	{
		// If the random string is not unique (unlikely, but possible) the function recursively calls itself again
		return randomString();
	}
	else
	{
		// When the random string has been approved as unique, it is added to the list of generated strings and then returned
		randomStrings.push(result);
		return result;
	}
}
