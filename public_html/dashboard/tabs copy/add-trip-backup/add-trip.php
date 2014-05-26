<?php

	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/places.php");
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/tickets.php");
    require_once($root."/engine/core/db/interface/trip_types.php");
	
    $company_id = $_SESSION['id'];
	$savedTickets = get_tickets_by_company($company_id);
	//get total tickets for company
	$numTickets = count_tickets_by_company($company_id);
	
?>

<!-- Ajax form submit, if success then custom alert appears -->
<script> 
    $(document).ready(function() { 
    	//check a few things first
    	//*has the user selected a trip type???
    	//*has the user set at least one ticket???
    	$('#add-trip-button').click(function(){
	    	var atLeastOneIsChecked = $('#trip-type-selection input:checkbox').is(':checked');
	    	if(!atLeastOneIsChecked){
		    	alert("Please select a trip type..");
	    	}
	    	
	    	var atLeastOneTicketIsAdded = $('#saved-tickets input:checkbox').is(':checked');
	    	//or
	    	//check if user has added manual tickets, then leave the rest of the validation to the validate script
	    	if ( $('#another-input').children().length > 0 ) {
	    		//has tickets
	    		var atLeastOneTicketIsEntered = true;
				
	 		}else{
		 		//doesn't have tickets
				var atLeastOneTicketIsEntered = false;
				
	 		}
	    	
	    	
	    	if((atLeastOneTicketIsAdded == false) && (atLeastOneTicketIsEntered == false)){
		    	alert("Please select/enter at least one ticket..");
	    	}
    	});
    	
    	
		/* AJAX FORM SUBMIT */
        $('#add-trip').ajaxForm(function(){
            //success modal appears!
            $( "#modal-overlay:hidden:first" ).fadeIn( "slow" );
            $( "#modal-box:hidden:first" ).fadeIn( "slow" );
        }); 
    }); 
</script>

<script>
  	$(function() {
	    // Enable are you sure script
	    $('#trip-add').areYouSure();
    });
</script>

<!-- Live on page form validation -->
<script>
	$.validate({
    modules : 'location, date, security, file',
    onModulesLoaded : function() {
      
    }
  });
</script>




<!-- Appends new ticket form segment -->
<script>
$(document).ready(function(){
    var count = 1;
	$("#another-ticket").click(function(){
		$("#another-input").append('<div class="ticket-wrap floating" id="this-'+count+'>
		<div class="ticket-label"><label class="ticket-num">New Ticket <a class="remove-this" id="'+count+'">(Remove)</a></label>
		<div class="ticket-form-fields"><label class="ticket-label">Ticket Name</label>
		<input type="text" placeholder="" id="ticket-input" class="form-text ticket-input" name="ticketName'+count+'" data-validation="length" data-validation-length="min2" data-validation-error-msg="Please enter a valid ticket name."/><label class="ticket-label">Ticket Description</label><textarea class="form-area ticket-area" name="ticketDescription'+count+'" data-validation="length" data-validation-length="min10" data-validation-error-msg="Please enter a valid ticket description."></textarea><label class="ticket-label">Ticket Capacity</label><input type="text" id="ticket-cap'+count+'" value="0" class="form-text ticket-input" name="ticketCap'+count+'" data-validation="number" data-validation-allowing="float"/><label class="ticket-label">Ticket Price</label><input type="text" placeholder="&pound;" class="form-text" name="ticketPrice'+count+'" data-validation="number" data-validation-allowing="float" /></div></div>');
		
		$( "#ammnt" ).val(count);
		
		count++;
		
		
	});	
});
</script>

<script>
$("#items-revmovable").on('click', '.remove-this', function () {
	
    $(this).parent().parent().remove(); 
});
</script>

<script>
$("#items-revmovable").on('click', '.remove-this', function () {
	
    $(this).parent().parent().remove(); 
    
});
</script>


<?php for($i = 1; $i <= $numTickets; $i++){ ?>
	<!-- drop down descriptions on saved tickets-->
	<script>
		  $(document).ready(function(){
				  	$("#drop-des-<?php echo $i; ?>").click(function(){
				  		//accordion
				  		if ( $( "#des-<?php echo $i; ?>:first" ).is( ":hidden" ) ) {
				  			$( "#des-<?php echo $i; ?>" ).slideDown( "fast" );
				  		} else {
					    	$( "#des-<?php echo $i; ?>" ).slideUp("fast");
						}
						
				  	});
			  	
			});
	</script>
	<script>
	$(document).ready(function(){
		    $('#ticket-checkbox-<?php echo $i; ?>').change(function(){
		        if(this.checked)
		            $('#is-selected-<?php echo $i; ?>').addClass("selected");
		            /* $('#is-selected').removeClass("not-selected"); */
		        else
		            $('#is-selected-<?php echo $i; ?>').removeClass("selected");
		            
		
		    });
		});
	</script>
<?php } ?>


  <script src="<?php echo "http://" .$_SERVER['HTTP_HOST']. "/common/ckeditor/ckeditor.js"; ?>"></script>
  
  <script src="<?php echo "http://" .$_SERVER['HTTP_HOST']. "/common/ckeditor/adapters/jquery.js"; ?>"></script>
  
  <script>

		CKEDITOR.disableAutoInline = true;

		$( document ).ready( function() {
			$( '#ckeditor' ).ckeditor(); // Use CKEDITOR.replace() if element is <textarea>.
			$( '#editable' ).ckeditor(); // Use CKEDITOR.inline().
		} );

		function setValue() {
			$( '#ckeditor' ).val( $( 'input#val' ).val() );
		}

</script>

<div id="add-trip" class="floating">
	<form action="/engine/trips/add_trip.php" id="trip-add" method="post" accept-charset="utf-8">
	
		<span class="form-item">
		
			<label class="add-trip-label item-head" for="name">Trip Title</label>
			
			<div class="form-fields">
				<input class="form-text form-name" id="tessting" type="text" name="name"  data-validation="length" data-validation-length="min2" data-validation-error-msg="Please enter a valid trip name." />
			</div>
			
		</span>
		
		<span class="form-item">
			<label class="add-trip-label item-head" for="description">Description</label>
			<div class="form-fields">
				<textarea class="form-area des-area" id="ckeditor" name="description" data-validation="length" data-validation-length="min10" data-validation-error-msg="Please enter a valid trip description."></textarea>
			</div>
		</span>
                
        <span class="form-item" id="trip-type-selection">
			<label class="add-trip-label item-head" for="trip_type">Trip Type</label>
			<div class="form-fields">
                        
                        <?php $count = 1; ?>
                        <?php $aTot = count(get_all_TRIP_TYPE_CONSTANTS()); ?>                       
                        <?php foreach (get_all_TRIP_TYPE_CONSTANTS() as $TRIP_TYPE){ ?>
                        	<?php if($count == 1){ ?>
                        		<div class="trip-type-row">
                        	<?php } ?>
                        	
                            	<div class="trip-type-item"><input type="checkbox" id="<?php echo $TRIP_TYPE['id']?>" name="tripType<?php echo $TRIP_TYPE['id']?>"/><label for="<?php echo $TRIP_TYPE['id']?>"><?php echo $TRIP_TYPE['name']?></label></div>
                            <?php if($count == $aTot){ ?>
                        		</div>
                        		<?php break; ?>
                        	<?php } ?>
                            <?php if($count % 3 == 0){ ?>
                        		</div>
                        	<?php } ?>
                            
                        	<?php if($count % 3 == 0){ ?>
                        		<div class="trip-type-row">
                        	<?php } ?>
                            <?php $count++; ?>
                        <?php  } ?>
                    
			</div>
		</span>
        
        <span class="form-item">
			<label class="add-trip-label item-head" for="duration">Duration (total nights)</label>
			<div class="form-fields">
				<input type="text" name="duration" class="form-text" data-validation="number" data-validation-allowing="float" /> <span id="nights-placeholder">Nights</span>
				<div class="field-help">If this is a day trip then simply put '0'.</div>
			</div>
		</span>
            
        <span class="form-item">
			<label class="add-trip-label item-head" for="location">Location</label>
			<div class="form-fields">
                   
                        <?php include($root."/engine/core/maps/dive_locator.php"); ?>
                        
                            
                            
			</div>
		</span>
            
        <span class="form-item">
			<label class="add-trip-label item-head" for="photo">Photo</label>
			<div class="form-fields">
				UPLOAD PHOTO HERE
			</div>
		</span>
            
        <span class="form-item">
			<label class="add-trip-label item-head" for="video">Video</label>
			<div class="form-fields">
				LINK VIDEO HERE
			</div>
		</span>
		
		
		<span class="form-item trip-ticket">
			<label class="add-trip-label item-head">Tickets</label>
			
			<div id="saved-tickets-wrap">
				<label id="label-saved-tickets">Previous Tickets</label>
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
										
										
										<a id="drop-des-<?php echo $counter; ?>">
											<div class="ticket-item-des-tab">
												More Details
											</div>
										</a>
										
										
										<input type="checkbox" name="previous-ticket-<?php echo $ticket['id']; ?>" id="ticket-checkbox-<?php echo $counter; ?>" class="select-toggle">
										<label for="ticket-checkbox-<?php echo $counter; ?>" id="is-selected-<?php echo $counter; ?>" class="not-selected">Select</label>
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
										
										
										<a id="drop-des-<?php echo $counter; ?>">
											<div class="ticket-item-des-tab">
												More Details
											</div>
										</a>
										
										<input type="checkbox" name="previous-ticket-<?php echo $ticket['id']; ?>" id="ticket-checkbox-<?php echo $counter; ?>" class="select-toggle">
										<label for="ticket-checkbox-<?php echo $counter; ?>" id="is-selected-<?php echo $counter; ?>" class="not-selected">Select</label>
									</div>
								
							<?php } ?>	
							
							<?php $counter++; ?>
							
						<?php } ?>
						
						</div><!-- ticket-col-right -->
						
					<?php } ?>
				</div><!-- saved-tickets -->
			</div><!-- saved-tickets-wrap -->
						
			
			<hr class="ticket-hr" />
			<span id="items-revmovable">
			<span id="another-input"></span>
			</span>
			<a  id="another-ticket" class="plus">+ <span class="smaller">Add another ticket...</span></a>
			
		</span>
		
		<!-- hidden input containing amount of new tickets added -->
		<input type="hidden" id="ammnt" name="totalNewTickets" value="0">
		
		<hr class="ticket-hr" />
		<input type="submit" class="form-button" id="add-trip-button" value="Save Trip">
	</form>	
</div>


