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

<script>
	CKEDITOR.disableAutoInline = true;

	$( document ).ready( function() {
		$( '#ckeditor' ).ckeditor(); // Use CKEDITOR.replace() if element is <textarea>.
	} );

	
</script>


<!-- Live on page form validation -->
<script>
	$.validate({
    modules : 'location, date, security, file',
    onModulesLoaded : function() {
      
    }
  });
</script>



<script>
  	$(function() {
	    // Enable are you sure script
	    $('#trip-add').areYouSure();
    });
    
    /* AJAX FORM SUBMIT */
    $('#trip-add-details').ajaxForm(function(){
        //success modal appears!
       /*  $( "#modal-overlay:hidden:first" ).fadeIn( "slow" ); */
        /* $( "#modal-box:hidden:first" ).fadeIn( "slow" ); */
        //head to the next form
        $("#add-trip").load('tabs/add-trip-forms/location.php');
    });
</script>

<script>
$(function(){
	if(!$('#hidden-submit').hasClass('submit-hidden')){
	$('#hidden-submit').addClass('submit-hidden');
	}
});
</script>

<script>
	$(function(){
		if($("[name='name'][type='hidden']").val()){
			$("[name='name'][type='text']").val($("[name='name'][type='hidden']").val());
		}
		
		if($("[name='description'][type='hidden']").val()){
			$("[name='description']textarea").val($("[name='description'][type='hidden']").val());
		}
		
		if($("[name='duration'][type='hidden']").val()){
			$("[name='duration'][type='text']").val($("[name='duration'][type='hidden']").val());
		}
		
		if($("[name='duration'][type='hidden']").val()){
			$("[name='duration'][type='text']").val($("[name='duration'][type='hidden']").val());
		}
		
	});
</script>

<script>
	//loop though all type fields
	$(function(){
		$('.trip-type-checkbox').change(function(){
			var thisID = $(this).attr('id');
			if(this.checked){
				//append this type to hidden fields
				$('#hidden-form').append("<input id='type"+ thisID +"' type='hidden' name='type[]' value='" + thisID + "'>");
			}else{
				//remove this hidden field
				$('#type' + thisID).remove();
			}
		})
	});
	
	//on page load check all previously ticked boxes
	$(function(){
		$('[name="type[]"]').each(function(){
			
			var typeID = $(this).val();
			$( "#" + typeID ).attr("checked",true);
		})
	});
	
</script>



<script>
	$(function(){
		//click function for any element that links away from this form
		$('.click-away').click(function(){
			//for each form element in this form
			$('#trip-add-details').find(':input').each(function(){
				var inName = $(this).attr('name');
				var inVal = $(this).val();
				
				$('#hidden-form').find("[name='" + inName + "']").val(inVal);
				
			});
			
			var loadForm = $(this).attr('data-load-form');
			$("#add-trip").load(loadForm);
		});
	});
</script>

<script>
$(function(){
	$('.trip-progress-stage').removeClass('stage-active');
    $('#first-stage').addClass('stage-active');
});
</script>

<form action="" id="trip-add-details" method="post" accept-charset="utf-8">
	
	<span class="form-item">
	
		<label class="add-trip-label item-head" for="name">Trip Title</label>
		
		<div class="form-fields">
			<input class="form-text form-name" id="tessting" type="text" name="name"  data-validation="length" data-validation-length="min2" data-validation-error-msg="Please enter a valid trip name." />
		</div>
		
	</span>
	
	<span class="form-item">
		<label class="add-trip-label item-head" for="description">Description</label>
		<div class="form-fields">
			<textarea class="form-area des-area" id="ckeditor" name="description"></textarea>
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
                    	
                        	<div class="trip-type-item"><input class="trip-type-checkbox" type="checkbox" id="<?php echo $TRIP_TYPE['id']?>" name="tripType<?php echo $TRIP_TYPE['id']?>"/><label for="<?php echo $TRIP_TYPE['id']?>"><?php echo $TRIP_TYPE['name']?></label></div>
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
        
    
	
	<div class="form-foot">
		<a data-load-form='tabs/add-trip-forms/location.php' class="click-away form-button trip-bttn-right">Set Locations</a>
	</div>
</form>	
