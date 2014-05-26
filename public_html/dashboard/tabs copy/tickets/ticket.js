/* ************************************ */
/* SWITCHES */
/* ************************************ */
$(function(){
	/*	tab switches */
	$("#ticket-area").html("").load("tabs/tickets/add-new.php");
	
	$(".switch-button").click(function(){
		$(".switch-button").removeClass("active-switch");
		$(this).addClass("active-switch");
		
		
		
		if($(this).attr("id") == "add"){
			$("#ticket-area").html("").load("tabs/tickets/add-new.php");
		}
		
		if($(this).attr("id") == "edit"){
			$("#ticket-area").html("").load("tabs/tickets/edit.php");
		}

	});
});

/* ************************************ */
/* ADD TICKET */
/* ************************************ */
$(function(){
	$("#outer").delegate("#add-another", "click", function(e){
		$("#single-tickets-wrap").append(
			'<div class="single-ticket floating">' +
				'<label class="item-head">Single Ticket <span class="remove-ticket">&#10005;</span></label>' +
				'<div class="ticket-body">' +
					'<label class="ticket-label">Ticket Name</label>' +
					'<input type="text" placeholder="Name" name="name[]" class="form-text ticket-input"/>' +
					
					'<label class="ticket-label">Description</label>' +
					'<textarea placeholder="Description" name="description[]" class="form-area ticket-area"/>' +
					
					'<div class="price-dur-wrap">' +
					'<span>' +
						'<label class="ticket-label">Duration</label>' +
						'<input type="text" placeholder="Duration" name="duration[]" class="form-text ticket-input"/>' +
					'</span>' +
					'<span>' +
						'<label class="ticket-label">Price</label>' +
						'<input type="text" placeholder="Price" name="price[]" class="form-text ticket-input"/>' +
					'</span>' +
					'</div>' +
				'</div>' +
			'</div>');
			
			updateTicketTotal(true);
			e.preventDefault();
	});
	
	$("#outer").delegate(".remove-ticket", "click", function(e){
		$(this).parent().parent().remove();
		updateTicketTotal(false);
		e.preventDefault();
	});
	
	
});

function updateTicketTotal(bool){
	var curTotal = parseInt($('[name="ticket-count"]').val(), 10);
	
	if(bool == true){
		//add to it
		curTotal++;
	}
	if(bool == false){
		//decrement it
		curTotal--;
	}
	$('[name="ticket-count"]').val(curTotal)	
}

/* ************************************ */
/* EDIT TICKETS */
/* ************************************ */
$(function(){
	$("#outer").delegate("#ticket-table tbody tr", "click", function(e){
		var ticketID = $(this).attr("id");
		var ticketName = $(this).children(".ticket-col-one").html();
		var ticketDescription = $(this).children(".ticket-col-two").html();
		var ticketPrice = $(this).children(".ticket-col-three").html();
		var ticketDuration = $(this).children(".ticket-col-four").html();
		
		$("#edit-ticket-wrap").html(
				'<div class="ticket-body">' +
					'<label class="ticket-label">Ticket Name</label>' +
					'<input type="text" value="'+ ticketName +'" placeholder="Name" name="name" class="form-text ticket-input"/>' +
					
					'<label class="ticket-label">Description</label>' +
					'<textarea placeholder="Description" name="description" class="form-area ticket-area">'+ ticketDescription +'</textarea>' +
					
					'<div class="price-dur-wrap">' +
					'<span>' +
						'<label class="ticket-label">Duration</label>' +
						'<input type="text" value="'+ ticketDuration +'" placeholder="Duration" name="duration" class="form-text ticket-input"/>' +
					'</span>' +
					'<span>' +
						'<label class="ticket-label">Price</label>' +
						'<input type="text" value="'+ ticketPrice +'" placeholder="Price" name="price" class="form-text ticket-input"/>' +
					'</span>' +
					'</div>' +
					'<input type="hidden" name="ticketID" value="' + ticketID + '">' +
					'<input type="submit" class="form-button" value="Save">' +
				'</div>');
		
		
	});
});