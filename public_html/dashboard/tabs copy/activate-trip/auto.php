<?php
        
    $tripID = $_GET['id'];

	//dates
	function day(){
		return date('d');
	}
	
	function month(){
		return date('m');
	}
	
	function year(){
		return date('Y');
	}
	

?>

<!-- DATE SELECTOR -->
<script>
        var dateCount = 1;
        var repeatDateCount = 1;
    
	$(document).ready(function(){
		
		//by defult make submit button unclickable
		$('input[type="submit"]').attr('disabled','disabled');
		
		//adds x days to a date
		function addDays(plusDays){
			//get current date
			
			var date = moment().add('days', plusDays).format("MMMM Do YYYY");
			
			
			return date;
		}
		
		
		//function sets all selector dates
		function setSelectorDates(startDay, startMonth, startYear){
			
				var momentDate = startYear+'-'+startMonth+'-'+startDay;
			
			/* PUT days into divs starting from today */
			for(var i = 0; i < 28; i++){
				
				
				
				
				var theDay = moment(momentDate).add('days', i).format("dddd");
				var theRest = "<div class='sel-date'>" + moment(momentDate).add('days', i).format("Do MMM") + "</div>";
				var theDate = moment(momentDate).add('days', i).format("dddd, MMMM Do YYYY");
				var theProperDate = moment(momentDate).add('days', i).format("YYYY-MM-DD");
				
					$('#day-' + i).html(theDay + theRest);
					
					$('#day-id-' + i).val(theDate);
					
					$('#day-item-' + i).val(theProperDate);
				
			}
		}
	
		
		//date selection script
		$( "#start-date-day, #start-date-month, #start-date-year"  )
			.change(function() {
				var day = $( "#start-date-day option:selected" ).val(); 
				var month = $( "#start-date-month option:selected" ).val();
				var year = parseInt($( "#start-date-year option:selected" ).val(), 10);
				var curYearInt = parseInt(moment().format("YYYY"), 10);
				
				
				
				var dayInt = parseInt(day, 10);
				var monthInt = parseInt(month, 10);
				
				var curDayInt = parseInt(moment().format("D"), 10);
				var curMonthInt = parseInt(moment().format("M"), 10);
				
				
				
				var error = 0;
				
				if(year == curYearInt){
					if(monthInt < curMonthInt){
						alert("Invalid date, sorry..");	
						error = 1;
					}
					if(monthInt == curMonthInt){
						if(dayInt < curDayInt){
							alert("Invalid date, sorry..");
							error = 1;	
						}
					}
				}
				
				if(error == 0){
					
					//set new selector dates
					setSelectorDates(day, month, year);
					
					//remove all dates from bottom as new ones will be set
					$('.can-remove').remove();
					
					//also delselect all selected dates
					$(".can-remove-class").removeClass("day-selected");
				}
				
			})
		.trigger( "change" );
		
		
		
		$( "#start-date-day, #start-date-month, #start-date-year"  )
			.change(function() {
				
				var selectedDay = $("#start-date-day").val();
				var selectedMonth = $("#start-date-month").val();
				var selectedYear = $("#start-date-year").val();
				
				var daysInMonth = moment(selectedYear+"-"+selectedMonth, "YYYY-MM").daysInMonth();
				
				if(selectedDay > daysInMonth){
					alert("Sorry, there aren't that many days in that month..");
				}
				
			})
		.trigger( "change" );
		
		
		
		
		$('.day-check:checkbox').change(function () {
			
			//removes white spaces and commas to be put in ID attr
			var dateNoWhite = $( this ).val().replace(/\s+/g, '').replace(/\,/g, '');
			
	        if(this.checked){

	        	$( this ).parent().addClass("day-selected");
	        	toAppend = "<div class='can-remove' id='" + dateNoWhite + "'><input class='form-text text-wider' type='text' value='" + $( this ).val() + "' name='date"+dateCount+"' disabled><input type='hidden' value='" + $( this ).val() + "' name='date"+dateCount+"'><a class='date-sel-wrap'>Delete</a></div>";
	        	
	                $("#append-to-this").append(toAppend);
                    dateCount++;
                    $('#ammnt').val(dateCount);
                    
	        }else{
	        	
	            $( this ).parent().removeClass("day-selected");
				
				
				$("#" + dateNoWhite).remove();
	        }
	        
		 });

		
		//delete individual dates
		$("#append-to-this").on('click', '.date-sel-wrap', function () {
			var dayID = $( this ).attr( "id" );
			$('#day-item-'+dayID).removeClass("day-selected");
		    $(this).parent().remove(); 
		    
		});
		
		//delete individual dates
		$("#reset-repeat").on('click', '.date-sel-wrap', function () {
		    $(this).parent().remove(); 
		});
		
		
		//date repetition
		$(document).ready(function(){
			$("#apply-dates").click(function(){
				$('#reset-repeat').html('<div id="append-repeated-to-this"></div>');
				
				var repAmmnt = $('#repeat-amount').val();
				
				if(repAmmnt){
				
					if($('.day-selected').length){
						//user has set some start dates from the date selector
						//********* CALCULATE NEW DATES HERE *************
						
						var showOnce = 0;
						
						$('input[type="submit"]').removeAttr('disabled');
						
						//count amount of elements selected in selector multiplied by ammount of months wanted
						var dateAmmnt = $('.day-selected').length;
						
						
						alert($('.day-selected').length + " x (" + repAmmnt + " - 1)" );
						
						//for loop through amount of selected elements
						for(var i = 1; i <= dateAmmnt; i++){
							//append dates in inputs
							
							//for each of the selected dates
							jQuery('.day-selected').each(function() {
								var j = i * 4;
								
								//get the date selected in question
							    var currentSelected = $(this).val();
							    
							    var newDate = moment(currentSelected).add('weeks', j).format("dddd, MMMM Do YYYY");
							    
							    //use this for checking not too far inadvance
							    var newTmpDate = moment(currentSelected).add('weeks', j).format("YYYY-M-D");
							    
							    //limiting the how far a date can be into the future by x months
							    var subDt = newTmpDate.split('-');
								var tmpYear = subDt[0];
								var tmpMonth = subDt[1]; 
								var tmpDay = subDt[3];
							    
							    //date a is this current date, date b is the calculated new date
							    var a = moment([<?php echo year(); ?>, <?php echo month(); ?>, <?php echo day(); ?>]);
							    var b = moment([tmpYear, tmpMonth, tmpDay]);
								
								var diff = b.diff(a, 'years', true); 
							    
							    
							    //check if too far in advance
							    if(diff > 1){
							    	if(showOnce == 0){
							    		alert("Sorry trips cannot be planned more than 1 year in advance.");
							    		showOnce = 1;
							    	}else{
								    	//breaks each loop
										return false;
							    	}
							    
							    //if all good append the new date	
							    }else{

									$("#append-repeated-to-this").append("<div class='can-remove' id=''><input class='form-text text-wider' type='text' value='" + newDate + "' name='repeatedDate"+repeatDateCount+"'><input type='hidden' value='" + newDate + "' name='repeatedDate"+repeatDateCount+"'><a class='date-sel-wrap'>Delete</a></div>");
								    repeatDateCount++;
	                                
	                                $('#rptammnt').val(repeatDateCount);
                                }
                                                            
							});
							 
						}
		
					}else{
						alert("Please select a date or more to start the trip from using the date selector.");
					}
				}else{
					alert('Please enter a repeat ammount.');
				}
				
			});
		});
		
		
		
	});
</script>

<script>
  	$(function() {
	    // Enable are you sure script
	    $('form').areYouSure();
    });
</script>


<form method="post" action="/engine/trips/activate_trip_automatic.php">
		
		<span class="form-item floating" id="date-form-item">
					<label class="inact-label item-head" for="description">Start Date & Times</label>
					<div class="form-fields">
						<label class="date-label">First Start Date</label>
						
						<select name="start-date-year" id="start-date-year" class="form-select">
							<option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
							<option value="<?php echo date('Y', strtotime('+1 year')); ?>"><?php echo date('Y', strtotime('+1 year')) ?></option>
						</select>
						
						<select name="start-date-day" id="start-date-day" class="form-select">
						<?php for($i = 1; $i < 32; $i ++){ ?>
						
							<?php if($i > 9){ $j = $i; }else{ $j = "0".$i; } ?>
							
							<option value="<?php echo $j; ?>"  
									class="<?php if($i == 31){ ?>removable<?php } ?><?php if(($i > 28) && ($i < 31)){ ?>remove-for-feb<?php } ?>"
								    <?php if($j == day()){?>selected<?php } ?>>
								    
								    <?php echo $j; ?>
							</option>
						<?php } ?>
						</select>
						
						<select name="start-date-month" id="start-date-month" class="form-select">
							<option value="01" <?php if("01" == month()){?>selected<?php } ?>>January</option>
							<option value="02" <?php if("02" == month()){?>selected<?php } ?>>February</option>
							<option value="03" <?php if("03" == month()){?>selected<?php } ?>>March</option>
							<option value="04" <?php if("04" == month()){?>selected<?php } ?>>April</option>
							<option value="05" <?php if("05" == month()){?>selected<?php } ?>>May</option>
							<option value="06" <?php if("06" == month()){?>selected<?php } ?>>June</option>
							<option value="07" <?php if("07" == month()){?>selected<?php } ?>>July</option>
							<option value="08" <?php if("08" == month()){?>selected<?php } ?>>August</option>
							<option value="09" <?php if("09" == month()){?>selected<?php } ?>>September</option>
							<option value="10" <?php if("10" == month()){?>selected<?php } ?>>October</option>
							<option value="11" <?php if("11" == month()){?>selected<?php } ?>>November</option>
							<option value="12" <?php if("12" == month()){?>selected<?php } ?>>December</option>	
						</select>
						
						<label class="date-label" id="times">Trip Start & End Times</label>
						<span class="from-to">Start</span>
						<select name="start-hour" class="form-select">
						  <option value="00">00</option>
						  <option value="01">01</option>
						  <option value="02">02</option>
						  <option value="03">03</option>
						  <option value="04">04</option>
						  <option value="05">05</option>
						  <option value="06">06</option>
						  <option value="07">07</option>
						  <option value="08">08</option>
						  <option value="09">09</option>
						  <option value="10">10</option>
						  <option value="11">11</option>
						  <option value="12">12</option>
						  <option value="13">13</option>
						  <option value="14">14</option>
						  <option value="15">15</option>
						  <option value="16">16</option>
						  <option value="17">17</option>
						  <option value="18">18</option>
						  <option value="19">19</option>
						  <option value="20">20</option>
						  <option value="21">21</option>
						  <option value="22">22</option>
						  <option value="23">23</option>
						  <option value="24">24</option>
						</select>
						:
						<select name="start-min" class="form-select">
						  <option value="00">00</option>
						  <option value="05">05</option>
						  <option value="10">10</option>
						  <option value="15">15</option>
						  <option value="20">20</option>
						  <option value="25">25</option>
						  <option value="30">30</option>
						  <option value="35">35</option>
						  <option value="40">40</option>
						  <option value="45">45</option>
						  <option value="50">50</option>
						  <option value="55">55</option>
						</select>
						
						<br />
						<span class="from-to">End</span>
							
						<select name="end-hour" class="form-select">
						  <option value="00">00</option>
						  <option value="01">01</option>
						  <option value="02">02</option>
						  <option value="03">03</option>
						  <option value="04">04</option>
						  <option value="05">05</option>
						  <option value="06">06</option>
						  <option value="07">07</option>
						  <option value="08">08</option>
						  <option value="09">09</option>
						  <option value="10">10</option>
						  <option value="11">11</option>
						  <option value="12">12</option>
						  <option value="13">13</option>
						  <option value="14">14</option>
						  <option value="15">15</option>
						  <option value="16">16</option>
						  <option value="17">17</option>
						  <option value="18">18</option>
						  <option value="19">19</option>
						  <option value="20">20</option>
						  <option value="21">21</option>
						  <option value="22">22</option>
						  <option value="23">23</option>
						  <option value="24">24</option>
						</select>
						:
						<select name="end-min" class="form-select">
						  <option value="00">00</option>
						  <option value="05">05</option>
						  <option value="10">10</option>
						  <option value="15">15</option>
						  <option value="20">20</option>
						  <option value="25">25</option>
						  <option value="30">30</option>
						  <option value="35">35</option>
						  <option value="40">40</option>
						  <option value="45">45</option>
						  <option value="50">50</option>
						  <option value="55">55</option>
						</select>
						
					</div>
				</span>
		
		<span class="form-item floating" id="repeat-form-item">
			<label class="inact-label item-head" for="description">Repeat Trip</label>
			<div class="form-fields">
				<p>Please select a day to start the trip (days are relative to today), and select the frequency that you would like to repeat the trip.</p>
				
				<div id="date-sel-wrap">
					<?php for($i = 0; $i < 28; $i++){ ?>
						
						<?php if($i == 27){ ?>
							<div class="can-remove-class date-day-end" value="" id="day-item-<?php echo $i; ?>">
						<?php }else{ ?>
							<div class="date-day can-remove-class" value="" id="day-item-<?php echo $i; ?>">	
						<?php } ?>
						
								<label class="day-label"  for="day-id-<?php echo $i; ?>" id="day-<?php echo $i; ?>"></label>
								
								<input name="daySelect[]" title="" id="day-id-<?php echo $i; ?>" value="" class="day-check" type="checkbox">
								
								
							</div>
						
					<?php } ?>
				</div>
			</div>
			
			<hr class="hr" />
			<div class="form-fields">
				<label>Repeat This Trip</label>
				<p>This feature allows you to repeat the starting dates above for x amount of units in time. Make your selection and view the new dates below.</p>
				
				Repeat for <input type="text" class="form-text" name="repeat-amount" id="repeat-amount">
				<input type="text" id="repeat-unit" class="form-text" value="Months (28 days)" disabled>
					
				<br/>
				<br/>
				<hr class="hr" />
				<a id="apply-dates"><span class="form-button apply-dates">Apply Repeat Dates</span></a>
			</div>
		</span>
		
		<span class="form-item floating" id="dates-form-item">
			<label class="inact-label item-head" for="description">Dates</label>
			<div class="form-fields" id="has-elements">
				<div id="append-to-this"></div>
			</div>
			
			<label class="inact-label item-head" for="description">Repeated Dates</label>
			<div class="form-fields" id="reset-repeat">
				<div id="append-repeated-to-this"></div>
			</div>
		</span>

		<input type="hidden" id="ammnt" name="totalDates" value="1">
                <input type="hidden" id="rptammnt" name="totalRepeatedDates" value="1">
                <input type="hidden" name="tripID" value="<?php echo $tripID; ?>">
                
		<input type="submit" class="form-button activate-bttn" value="Activate Trips">
	</form>
