<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/tickets.php");
	
	
	$tripID = $_GET[id];
	
	
	//get all companies trips in array
	$allTickets = get_tickets_by_trip($tripID);
?>

<script>
	  $(document).ready(function(){
			  	$("#left-col").on('click', '.more-details', function () {
			  		//accordion
			  		var select = $($(this).parent()).find(".ticket-item-des");
			  		if ( $(select ).is( ":hidden" ) ) {
			  			$(select ).slideDown( "fast" );
			  		} else {
				    	$( select ).slideUp("fast");
					}
					
			  	});
			  	
			  	$("#right-col").on('click', '.more-details', function () {
			  		//accordion
			  		var select = $($(this).parent()).find(".ticket-item-des");
			  		if ( $(select ).is( ":hidden" ) ) {
			  			$(select ).slideDown( "fast" );
			  		} else {
				    	$( select ).slideUp("fast");
					}
					
			  	});
		});
</script>


<?php

	
	if($allTickets == 0){
		echo "This trip has no tickets, sorry.";
	}else{
		 $counter = 1; ?>
						
						<div class="ticket-col-left" id="left-col">
						
						<?php foreach($allTickets as $ticket){ ?>
						
							<?php if($counter % 2 != 0){ ?>
								
									<div class="ticket-item floating">
										<div class="ticket-item-name">
											<?php echo $ticket['name']." - ?? available"; ?>
										</div>
										
										<div class="ticket-item-des" id="des-<?php echo $counter; ?>">
											<div class="ticket-item-des-price">
												Price: £<?php echo $ticket['price']; ?>
											</div>
											<div class="ticket-item-des-des">
												<?php echo $ticket['description']; ?>
											</div>
										</div>
										
										
										<a class="more-details">
											<div class="ticket-item-des-tab">
												More Details
											</div>
										</a>
										
										
										<input type="radio" name="ticket" value="<?php echo $ticket['id']; ?>" id="ticket-radio-<?php echo $counter; ?>" class="ticket-radio">
										<label for="ticket-radio-<?php echo $counter; ?>" id="is-selected-<?php echo $counter; ?>" class="not-selected">Select</label>
									</div>
								
							<?php } ?>
								
							<?php $counter++; ?>
							
						<?php } ?>
						
						</div><!-- ticket-col-left -->
						
						<?php $counter = 1; ?>
						
						<div class="ticket-col-right" id="right-col">
							
						<?php foreach($allTickets as $ticket){ ?>
						
							<?php if($counter % 2 == 0){ ?>
								
							
									<div class="ticket-item floating">
										<div class="ticket-item-name">
											<?php echo $ticket['name']." - ?? available"; ?>
										</div>
										
										<div class="ticket-item-des" id="des-<?php echo $counter; ?>">
											<div class="ticket-item-des-price">
												Price: £<?php echo $ticket['price']; ?>
											</div>
											<div class="ticket-item-des-des">
												<?php echo $ticket['description']; ?>
											</div>
										</div>
										
										
										<a class="more-details">
											<div class="ticket-item-des-tab">
												More Details
											</div>
										</a>
										
										<input type="radio" name="ticket" value="<?php echo $ticket['id']; ?>" id="ticket-radio-<?php echo $counter; ?>" class="ticket-radio">
										<label for="ticket-radio-<?php echo $counter; ?>" id="is-selected-<?php echo $counter; ?>" class="not-selected">Select</label>
									</div>
								
							<?php } ?>	
							
							<?php $counter++; ?>
							
						<?php } ?>
						
						</div><!-- ticket-col-right -->
						<?php
	}
	
?>