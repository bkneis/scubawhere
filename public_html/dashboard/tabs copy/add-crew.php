<?php 
	$root =  $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/init.php");
	require_once($root."/engine/core/db/interface/agencies.php");
	
	$quals = get_all_CERTIFICATE_CONSTANTS();
?>
<!-- JQUERY FORM SUBMIT FUNCTION -->
<script>
$(document).ready(function(){
  $("#crew-add").submit(function(){
	  /* success */
  });
});
</script>

<script>
  	$(function() {
	    // Enable are you sure script
	    $('form').areYouSure();
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

<div id="wrapper" class="floating">
	<label class="item-head" id="add-crew-head">Add New Crew Member</label>
	<form action="test.php" id="crew-add" method="post" accept-charset="utf-8">
	
		<span class="form-item">
			<label class="crew-label" for="">First Name</label>
			<input class="form-text form-name" type="text" name="firstName" data-validation="length" data-validation-length="min2" data-validation-error-msg="Minimum 2 characters.." >
		</span>
		
		<span class="form-item">
			<label class="crew-label" for="">Last Name</label>
			<input class="form-text form-name" type="text" name="lastName" data-validation="length" data-validation-length="min2" data-validation-error-msg="Minimum 2 characters..">
		</span>
		
		<span class="form-item">
			<label class="crew-label" for="">DOB</label>
			<input type="text" name="dob" class="form-text" placeholder="DD/MM/YYYY" data-validation="date" data-validation-format="dd/mm/yyyy"/>
		</span>

		
		<span class="form-item">
			<label class="crew-label" for="">Qualifications</label>
			
			<hr />
			
			<?php $count = 1; ?>
            <?php $aTot = count($quals); ?>
			
			<?php foreach($quals as $qual){ ?>
			
							<?php if($count == 1){ ?>
                        		<div class="trip-type-row">
                        	<?php } ?>
                        	
                            	<div class="trip-type-item"><input type="checkbox" class="crew-check" name="qual[]" value="<?php echo $qual['name']; ?>" > <label class="crew-check-label" for=""> - <?php echo $qual['name']; ?></label></div>
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
				
			<?php } ?>
			
			
		</span>
		
		<hr />
		
		<span class="form-item">
			<label class="crew-label" for="">Comments</label>
			<textarea class="form-area crew-area" name="rdes"></textarea>
		</span>
		
		<input type="submit" class="form-button res-button" value="Add Crew">
	</form>	
</div>

