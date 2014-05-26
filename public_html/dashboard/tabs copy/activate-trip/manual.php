<?php

        $tripID = $_GET['id'];

	//dates
	function day(){
		return date('d');
	}
	
	function month(){
		return date('n');
	}
	
	function year(){
		return date('Y');
	}        
?>

<script>
	$(document).ready(function(){	
		var count = 1;
		
		$("#add-date").click(function(){
		count++;	
	        
	    $("#append").append('<div class="individual-date"><select name="start-date-day-'+count+'" id="start-date-day-'+count+'" class="form-select start-date-day"><?php for($i = 1; $i < 32; $i ++){ ?><?php if($i > 9){ $j = $i; }else{ $j = "0".$i; } ?><option value="<?php echo $j; ?>" <?php if($j == day()){?>selected<?php } ?>><?php echo $j; ?></option><?php } ?></select> <select name="start-date-month-'+count+'" id="start-date-month-'+count+'" class="form-select start-date-month"><option value="01" <?php if("01" == month()){?>selected<?php } ?>>January</option><option value="02" <?php if("02" == month()){?>selected<?php } ?>>February</option><option value="03" <?php if("03" == month()){?>selected<?php } ?>>March</option><option value="04" <?php if("04" == month()){?>selected<?php } ?>>April</option><option value="05" <?php if("05" == month()){?>selected<?php } ?>>May</option><option value="06" <?php if("06" == month()){?>selected<?php } ?>>June</option><option value="07" <?php if("07" == month()){?>selected<?php } ?>>July</option><option value="08" <?php if("08" == month()){?>selected<?php } ?>>August</option><option value="09" <?php if("09" == month()){?>selected<?php } ?>>September</option><option value="10" <?php if("10" == month()){?>selected<?php } ?>>October</option><option value="11" <?php if("11" == month()){?>selected<?php } ?>>November</option><option value="12" <?php if("12" == month()){?>selected<?php } ?>>December</option>	</select> <select name="start-date-year-'+count+'" id="start-date-year-'+count+'" class="form-select start-date-year"><option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option><option value="<?php echo date('Y', strtotime('+1 year')); ?>"><?php echo date('Y', strtotime('+1 year')) ?></option></select><a class="delete-this" id="'+count+'">Delete</a></div>');
	
			$('#ammnt').val(count);
	
		});
			
		$("#append").on('click', '.delete-this', function () {
			
		    $(this).parent().remove(); 
		    
		});
		
		
			
	});
</script>
<script>
$(document).ready(function(){	
	//date verification scripts
		$(document).on('change',".start-date-day, .start-date-month, .start-date-year", function(){
				var tmpID = $(this).attr('id');
				var lastChar = tmpID.substr(tmpID.length - 1);
		    	var day = $( "#start-date-day-" + lastChar + " option:selected" ).val(); 
				var month = $( "#start-date-month-" + lastChar + " option:selected" ).val();
				var year = parseInt($( "#start-date-year-" + lastChar + " option:selected" ).val(), 10);
				var curYearInt = parseInt(moment().format("YYYY"), 10);
				
				if(day.charAt(0) == "0"){
					day = day.substring(1, day.length);
				}
				
				if(month.charAt(0) == "0"){
					month = month.substring(1, month.length);
				}
				
				
				var dayInt = parseInt(day, 10);
				var monthInt = parseInt(month, 10);
				
				var curDayInt = parseInt(moment().format("D"), 10);
				var curMonthInt = parseInt(moment().format("M"), 10);
				
				var error = 0;
				
				//is the date in the past
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
				
				//is it more than a year in advance
				var a = moment([<?php echo year(); ?>, <?php echo month(); ?>, <?php echo day(); ?>]);
				var b = moment([year, monthInt, dayInt]);
								
				var diff = b.diff(a, 'years', true); 
							    
							    
							    
				if(diff > 1){
		    		alert("Sorry trips cannot be planned more than 1 year in advance.");
		    		error = 1;
		    	}
		    	
		    	if(error == 1){
			    	$('input[type="submit"]').attr('disabled','disabled');
		    	}else{
			    	$('input[type="submit"]').removeAttr('disabled');
		    	}
				
			
		});
				
		
		
		
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
		
		
});
</script>
<script>
  	$(function() {
	    // Enable are you sure script
	    $('form').areYouSure();
    });
</script>

<form action="/engine/trips/activate_trip_manual.php" method="post">
<span class="form-item floating" id="dates-form-item">
<label class="inact-label item-head">Trip Start & End Times</label>
	<div class="form-fields">
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
	
	<label class="inact-label item-head">Dates</label>
	
	<div class="form-fields" >
		<select name="start-date-day-1" id="start-date-day-1" class="form-select start-date-day">
		<?php for($i = 1; $i < 32; $i ++){ ?>
		
			<?php if($i > 9){ $j = $i; }else{ $j = "0".$i; } ?>
			
			<option value="<?php echo $j; ?>" <?php if($j == day()){?>selected<?php } ?>><?php echo $j; ?></option>
		<?php } ?>
		</select>
		
		<select name="start-date-month-1" id="start-date-month-1" class="form-select start-date-month">
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
		
		<select name="start-date-year-1" id="start-date-year-1" class="form-select start-date-year">
			<option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
			<option value="<?php echo date('Y', strtotime('+1 year')); ?>"><?php echo date('Y', strtotime('+1 year')) ?></option>
		</select>
		
		<div id="append"></div>
		
		<div id="add-date-wrap"><a id="add-date" class="form-button">Add Another Date</a></div>
		
		<br />
		
	</div>
	
	
</span>

<input type="hidden" id="ammnt" name="totalDates" value="1">

<input type="hidden" name="tripID" value="<?php echo $tripID; ?>">

<input type="submit" class="form-button activate-bttn" value="Activate Trips">
	
</form>