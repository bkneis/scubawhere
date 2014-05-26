<script src="<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/common/js/jquery.quicksearch.js" ?>"></script>

<script type="text/javascript">
	$(function () {
		
		$('input#id-search').quicksearch('table tbody tr');
				
	});
</script>

<script>
	$(document).ready(function(){
		
		
		//defaults
		var thisMonth = moment().format('MMMM YYYY'); 
		$('#switch-disp').html(thisMonth);
		$('tbody').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/do.php?order=1&month=0'); 
		
		var alphabet = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
		
		var sort = 1;
			
		var clickCount = 0;
		var newMonth;
		
		$("#switch-prev").click(function(){
				$('input#id-search').quicksearch('table tbody tr');
				
				
				if(sort == 1){
					clickCount--;
					
					var newMonth = moment().add('M', clickCount).format('MMMM YYYY'); 
					/* newMonth = moment(newMoment).('MMMM YYYY'); */
					
					//set new switcher date
					$('#switch-disp').html(newMonth);
					
					//set new table rows
					$('tbody').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/do.php?order=' + sort + '&month=' + clickCount);
				}
				
				if(sort == 2){
					
					//take back to 25 if at 0
					if(clickCount == 0){
						clickCount = 25;
					}else{
						clickCount--;
					}
					
				
					var newAlpha = alphabet[clickCount];
					
					//set new switcher date
					$('#switch-disp').html(newAlpha);
					
					//set new table rows
					$('tbody').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/do.php?order=' + sort + '&letter=' + newAlpha);

				}
		});
		
		$("#switch-next").click(function(){
				$('input#id-search').quicksearch('table tbody tr');
				
				
				if(sort == 1){
					
					clickCount++;
				
					
					var newMonth = moment().add('M', clickCount).format('MMMM YYYY'); 
					/* newMonth = moment(newMoment).('MMMM YYYY'); */
					
					//set new switcher date
					$('#switch-disp').html(newMonth);
					
					//set new table rows
					$('tbody').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/do.php?order=' + sort + '&month=' + clickCount);
				}
				
				
				if(sort == 2){
					
					//26 letter in alphabet.. Must reset back round to zero if..
					if(clickCount == 25){
						clickCount = 0;
					}else{
						clickCount++;
					}
					
					var newAlpha = alphabet[clickCount];
					
					//set new switcher date
					$('#switch-disp').html(newAlpha);
					
					//set new table rows
					$('tbody').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/do.php?order=' + sort + '&letter=' + newAlpha); 
				}
				
		});
		
		$(".booking-order").click(function(){
			sort = $(this).attr('title');
			$(".booking-order").removeClass('order-active');
			
			$(this).addClass('order-active');
			
			if($(this).attr('id') == "booking-order"){
				$('tbody').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/do.php?order=' + sort + '&month=' + clickCount);
				$("#switch-disp").html(thisMonth);
				clickCount = 0;
			}else{
				$('tbody').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/do.php?order=' + sort + '&letter=' + clickCount);
				$("#switch-disp").html("A");
				clickCount = 0;
			}
		});
		

	});
</script>


	
<div id="wrapper">			
	<form action="#">
		
			<input type="text" class="form-text" name="search" value="" id="id-search" placeholder="Search" autofocus />
		
	</form>
	
	<div id="filter-bar" class="floating">
		<div id="group-switch">
			<div id="switch-prev">Previous</div>
			<div id="switch-disp"></div>
			<div id="switch-next">Next</div>
		</div>
		
		<div id="bookings-order">
			<div id="sort-by">Sort by:</div>
			<div id="booking-order" class="booking-order order-active" title="1">Booking date</div>
			<div id="sur-order" class="booking-order" title="2">Surname</div>
		</div>
	</div>
		<table class="table floating">
		<thead class="thead">
			<tr class="tr">
				<th class="th" width="15%">Booking Ref.</th>
				<th class="th" width="20%">Name</th>
				<th class="th" width="25%">Email</th>
				<th class="th" width="30%">Date Booked</th>
				<th class="th" width="10%">Manage</th>
			</tr>
		</thead>
		<tbody class="tbody">
			
		</tbody>
	</table>
</div>

				