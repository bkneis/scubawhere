<?php

	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/tickets.php");


    $company_id = $_SESSION['id'];
	$savedTickets = get_tickets_by_company($company_id);
	//get total tickets for company
	$numTickets = count_tickets_by_company($company_id);

?>

<script>
	$(function(){
		//click function for any element that links away from this form
		$('.click-away').click(function(){
			//for each form element in this form
			$('#trip-add-tickets').find(':input').each(function(){
				var inName = $(this).attr('name');
				var inVal = $(this).val();

				$('#hidden-form').find("[name='" + inName + "']").val(inVal);

			});

			var loadForm = $(this).attr('data-load-form');
			$("#add-trip").load(loadForm);
		});
	});
</script>

<!-- drop down descriptions on saved tickets-->
<script>
	//function to add selected tickets to hidden fields
	function addTicket(ticketID){

		$("#hidden-form").append("<input type='hidden' name='ticket[]' value='" + ticketID + "'>");

		//increment ticket count
		var tempTicketCount = parseInt($("#ticketCount").val(), 10) + 1;
		$("#ticketCount").val(tempTicketCount);
	}

	//function to remove selected tickets to hidden fields
	function removeTicket(ticketID){

		$("[value='" + ticketID + "'][name='ticket[]']").remove();

		//decrement ticket count
		var tempTicketCount = parseInt($("#ticketCount").val(), 10) - 1;
		$("#ticketCount").val(tempTicketCount);
	}

	  $(function(){
	  		$('.drop-des').each(function(){
		  		$(this).click(function(){

			  		//accordion
			  		if ( $(this).parent().find(".ticket-item-des" ).is( ":hidden" ) ) {
			  			$( $(this).parent().find(".ticket-item-des" ) ).slideDown( "fast" );
			  		} else {
				    	$( $(this).parent().find(".ticket-item-des" ) ).slideUp("fast");
					}
			  	});
	  		});
		});

		$(function(){
			$('.select-toggle').each(function(){
		    	$(this).change(function(){
		    		var ticketChecked = $(this).attr('id');
		    		ticketChecked = ticketChecked.split("-");
		    		ticketChecked = ticketChecked[2];
		    		var checkLabel = $(this).attr('id');
			        if(this.checked){
			            $("[for='" + checkLabel + "']").addClass("selected");
			            addTicket(ticketChecked);
			            /* $('#is-selected').removeClass("not-selected"); */
			        }else{
			            $("[for='" + checkLabel + "']").removeClass("selected");
			            removeTicket(ticketChecked);
			        }

				});
		    });
		});


</script>

<script>
	//on page load check all previously ticked boxes
	$(function(){
		$('[name="ticket[]"]').each(function(){

			var thisTicketID = $(this).val();
			$( "#ticket-checkbox-" + thisTicketID ).attr("checked",true);
			$( "[for='ticket-checkbox-" + thisTicketID + "']" ).addClass('selected');
		})
	});
</script>

<script>
$(function(){
	$('.trip-progress-stage').removeClass('stage-active');
    $('#third-stage').addClass('stage-active');


});
</script>

<script>
$(function(){
	if($('#hidden-submit').hasClass('submit-hidden')){
	$('#hidden-submit').removeClass('submit-hidden');
	}
});
</script>

<form id="trip-add-tickets">
	<span class="form-item trip-ticket">
		<label class="add-trip-label item-head">Tickets</label>

		<div id="saved-tickets-wrap">
			<div id="saved-tickets">
				<?php if($savedTickets < 1){ ?>
					<p id="no-tickets">You have no previous tickets yet.</p>
				<?php }else{ ?>

					<?php $counter = 1; ?>

					<div class="ticket-col-left">

					<?php foreach($savedTickets as $ticket){ ?>

						<?php if($counter % 2 != 0){ ?>

								<div class="ticket-item floating">
									<div class="ticket-item-name">
										<?php echo $ticket['name']; ?>
									</div>

									<div class="ticket-item-des" id="des-<?php echo $counter; ?>">
										<div class="ticket-item-des-price">
											Price: £<?php echo $ticket['price']; ?>
										</div>
										<div class="ticket-item-des-des">
											<?php echo $ticket['description']; ?>
										</div>
									</div>


									<a class="drop-des">
										<div class="ticket-item-des-tab">
											More Details
										</div>
									</a>


									<input type="checkbox" name="previous-ticket[]" id="ticket-checkbox-<?php echo $ticket['id']; ?>" class="select-toggle">
									<label for="ticket-checkbox-<?php echo $ticket['id']; ?>" class="not-selected">Select</label>
								</div>

						<?php } ?>

						<?php $counter++; ?>

					<?php } ?>

					</div><!-- ticket-col-left -->

					<?php $counter = 1; ?>

					<div class="ticket-col-right">

					<?php foreach($savedTickets as $ticket){ ?>

						<?php if($counter % 2 == 0){ ?>


								<div class="ticket-item floating">
									<div class="ticket-item-name">
										<?php echo $ticket['name']; ?>
									</div>

									<div class="ticket-item-des" id="des-<?php echo $counter; ?>">
										<div class="ticket-item-des-price">
											Price: £<?php echo $ticket['price']; ?>
										</div>
										<div class="ticket-item-des-des">
											<?php echo $ticket['description']; ?>
										</div>
									</div>


									<a class="drop-des">
										<div class="ticket-item-des-tab">
											More Details
										</div>
									</a>


									<input type="checkbox" name="previous-ticket[]" id="ticket-checkbox-<?php echo $ticket['id']; ?>" class="select-toggle">
									<label for="ticket-checkbox-<?php echo $ticket['id']; ?>" class="not-selected">Select</label>
								</div>

						<?php } ?>

						<?php $counter++; ?>

					<?php } ?>

					</div><!-- ticket-col-right -->

				<?php } ?>
			</div><!-- saved-tickets -->
		</div><!-- saved-tickets-wrap -->
		<div class="form-foot">
			<a data-load-form='tabs/add-trip-forms/location.php' class="click-away form-button trip-bttn-left">Set Locations</a>
		</div>
</form>
