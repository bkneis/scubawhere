$(function(){
	
	//form appended to the dropped room type to get capacity in boat
	var typeCapForm = 	'<form>' +
							'<input name="typeCap" type="text" placeholder="Room Type Capacity" />' +
							'<input type="submit" value="Save" id="save-cap" />' +
						'</form>';
	
	setPage(typeCapForm);
	
	//on oad set the draggable and droppable items
    setDroppables(typeCapForm);
    setDraggables();
    
    //clicked save new room type
    $("#save-type").click(function(e){
    	
    	//get type data
    	var typeName = $('#new-type-name').val();
    	var typeDes = $('#new-type-description').val();
    	
    	//set borders back to default
    	$('#new-type-name').css("border-color", "#c8c8c8");
    	$('#new-type-description').css("border-color", "#c8c8c8");
    	
    	/* ~~~~~~~~~~~~~~~~~~~~~~~~VALIDATE HERE */
    	if(!typeName || !typeDes){
	    	//not complete
	    	if(!typeName){$('#new-type-name').css("border-color", "#FF7163");}
	    	
	    	if(!typeDes){$('#new-type-description').css("border-color", "#FF7163");}
	    	
    	}else{
	    	//append new room type
	    	appendType(typeName, typeDes);
	    	
	    	//set new draggable elemants
	    	setDraggables();
    	}
    	
    	
    	
    	//prevent form submission
    	e.preventDefault();
    });
    
    //clicked save new boat type
    $("#save-boat").click(function(e){
    	
    	//get the input data
    	var boatName = $('#new-boat-name').val();
		var boatCap =  $('#new-boat-cap').val();
		var boatDes =  $('#new-boat-description').val();
		
		//set borders back to default
    	$('#new-boat-name').css("border-color", "#c8c8c8");
    	$('#new-boat-cap').css("border-color", "#c8c8c8");
    	$('#new-boat-description').css("border-color", "#c8c8c8");
		
		/* ~~~~~~~~~~~~~~~~~~~~~~~~VALIDATE HERE */
    	if(!boatName || !boatCap || !boatDes || !$.isNumeric(boatCap)){
	    	//not complete
	    	if(!boatName){$('#new-boat-name').css("border-color", "#FF7163");}
	    	if(!boatCap || !$.isNumeric(boatCap)){$('#new-boat-cap').css("border-color", "#FF7163");}
	    	if(!boatDes){$('#new-boat-description').css("border-color", "#FF7163");}
	    	
    	}else{
	    	//call append boat with the values from form
	    	appendBoat(boatName,boatCap,boatDes);
	    	
	    	//set droppables after new boat is appended
		    setDroppables(typeCapForm);
    	}
    	
    	
	    
	    //restrict form submit
    	e.preventDefault();
    });
    
    //delegate click to save the boats roomtype capacity
    $("body").delegate("#save-cap", "click", function(e){
	    
	    //get form data
		var typeCap = $('[name="typeCap"]').val();
		//get the wrap to append in
    	var roomTypeWrap = $(this).parent().parent();
    	
    	//append new room type capacity
    	appendCap(typeCap, roomTypeWrap);
		
		//prevent form submission
    	e.s();
    });
});



/*
******************************************
								FUNCTIONS
******************************************
*/

function setPage(typeCapForm){
	$.ajax({
		url: "/company/boats",
		type: "GET",
		dataType: "json",
		success: function(data){
			console.log(data);
			$.each(data.accomodations, function(data){
					
				var toAppend = 	'<li class="draggable" id="' + this.id + '">' +
									'<span class="types-name" data-room="' + this.name + '">' + this.name + '</span>' +
									'<span class="types-des">' + this.description + '</span>' +
								'</li>' + 
								'<input type="hidden" value="' + this.name + '" name="accomodations['+this.id+'][name]" >' +
								'<input type="hidden" value="' + this.name + '" name="accomodations['+this.id+'][description]" value="' + this.description + '" >';
					
				$("#types-list").append(toAppend);
			});
			
			$.each(data.boats, function(data){
					
				var toAppend = 	'<li class="droppable" id="' + this.id + '">' +
									'<span class="boat-name">' + this.name + '</span>'+
									'<span class="boat-cap">Capacity: ' + this.capacity + '</span>'+
									'<div class="truncate">' + this.description + '</div>' +
									'<input type="hidden" value="' + this.name + '" name="boats[' + this.id + '][name]" />' +
									'<input type="hidden" value="' + this.description + ' " name="boats[' + this.id + '][description]" />' +
									'<input type="hidden" value="' + this.capacity + '" name="boats[' + this.id + '][capacity]" />' +
									'<ul></ul><!-- list of dropped BOATS -->' +
								'</li>';
					
				$("#boats-list").append(toAppend);
			});
			
			setDroppables(typeCapForm);
			setDraggables();
			
		}
	});
}

//sets all dragable elements on the page
function setDraggables(){
	$(".draggable").each(function(){
		$(this).draggable({
			helper: "clone"
		});
	});
}

//sets all droppable elements on the page
function setDroppables(typeCapForm){
	$(".droppable").each(function(){
		$(this).droppable({
			hoverClass: "ui-state-hover",
			drop:function(event, ui) {
				//get the id of this boat to add in name of new appended
				var thisID = $(this).attr('id');
				//append the form for type capacity within that boat
		        $(this).children("ul").append($(ui.draggable).clone().find(".types-des").remove().append(typeCapForm)
		        								);	
		        
		        	      
		    }
		});
	});
}

//append capacity to dragged in room type
function appendCap(cap, thisRoomWrap){
	//append visible boat capacity
	var toAppend = 	"<span>" + cap + "</span>";
	$(thisRoomWrap).append(toAppend);
	$(thisRoomWrap).children("form").remove();
	
	//append hidden inputs with capacity, boat and room type
	var roomID = $(thisRoomWrap).attr("id");
	var boatID = $(thisRoomWrap).parent().parent().attr("id");
	$(thisRoomWrap)
	.append('<input type="hidden" name="[boats][' + boatID + '][accomodations]['+roomID+']" value="'+cap+'">');
}

//append new room type
function appendType(name, des){
	var rand = randomString();
	
	
	var toAppend = 	'<li class="draggable" id="' + rand + '">' +
						'<span class="types-name" data-room="' + name + '">' + name + '</span>' +
					'</li>' + 
					'<input type="hidden" value="' + name + '" name="accomodations['+rand+'][name]" >' +
					'<input type="hidden" value="' + name + '" name="accomodations['+rand+'][description]" value="' + des + '" >';
	
	$("#types-list").append(toAppend);
	
	//reset fields
	$('#new-type-name').val("");
    $('#new-type-description').val("");
}

//append new boat
function appendBoat(name, cap, des){
	var rand = randomString();
	var toAppend = 	'<li class="droppable" id="' + rand + '">' +
						'<span class="boat-name">' + name + '</span>'+
						'<span class="boat-cap">Capacity: ' + cap + '</span>'+
						'<div class="truncate">' + des + '</div>' +
						'<input type="hidden" value="' + name + '" name="boats[' + rand + '][name]" />' +
						'<input type="hidden" value="' + des + ' " name="boats[' + rand + '][description]" />' +
						'<input type="hidden" value="' + cap + '" name="boats[' + rand + '][capacity]" />' +
						'<ul></ul><!-- list of dropped BOATS -->' +
					'</li>';
	
	$("#boats-list").append(toAppend);
}

function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 15;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
}


/*
<input name="accomodations[3][name]" value="Single Bedroom">
<textfield name="accomodations[3][description]"></textfield>
(<input name="accomodations[3][photo]" value="single_bedroom.jpg">)

<input name="accomodations[acdc][name]" value="Double Bedroom">
<textfield name="accomodations[acdc][description]"></textfield>


The boats then:

<input name="boats[1][name]" value="Sea Shepard">
<input name="boats[1][capacity]" value="30">
<textfield name="boats[1][description]"></textfield>
<input name="boats[1][photo]" value="sea_shepard.gif">
    <input name="boats[1][accomodations][3]" value="8">
    <input name="boats[1][accomodations][acdc]" value="22">
*/

