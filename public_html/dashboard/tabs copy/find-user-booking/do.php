<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	include_once($root."/engine/core/db/interface/bookings.php");
	include_once($root."/engine/core/db/interface/users.php");
	include_once($root."/engine/core/db/interface/tickets.php");
	include_once($root."/engine/core/db/interface/trips.php");
	
	$bookings = get_bookings_by_company($_SESSION['id']);
	
	function bookingDateArray($bookings){
		//get the month that is selected/work it out
		$getMonth = $_GET['month'];
		
		$month = date("n", strtotime("+" . $getMonth . " months"));
		$year = date("Y", strtotime("+" . $getMonth . " months"));
		
		
		//put all appropriate data into an array for sorting
		foreach($bookings as $booking){ 
			//check if date is appropriate
			$tempMonth = date("n", strtotime($booking['created']));
			$tempYear = date("Y", strtotime($booking['created']));
			
			if(($month == $tempMonth) && ($year == $tempYear)){
			
				//find user and trip details
				$user = get_user_by_id($booking['userID']);
	
				$fullBookingsArray[] = array(
		                    'id'  => $booking['id'], 
		                    'user_surname' => $user['lastName'],
		                    'user_first_name'  => $user['firstName'],
		                    'email' => $user['email'],
		                    'session_ticket_id'  => $booking['sessionTicketID'],
		                    'date_booked' => $booking['created']
		        );
	        }
		}
		return $fullBookingsArray;
	}
	
	function surnameArray($bookings){
		//get the month that is selected/work it out
		$getLetter = $_GET['letter'];
		
		
		//put all appropriate data into an array for sorting
		foreach($bookings as $booking){ 
			
			//find user and trip details
			$user = get_user_by_id($booking['userID']);
			
			//get first letter of surname
			$userSurname = $user['lastName'];
			$surnameLetter = strtoupper($userSurname[0]);
			
			if($surnameLetter == $getLetter){

				$fullBookingsArray[] = array(
		                    'id'  => $booking['id'], 
		                    'user_surname' => $user['lastName'],
		                    'user_first_name'  => $user['firstName'],
		                    'user_id'  => $user['id'],
		                    'email' => $user['email'],
		                    'session_ticket_id'  => $booking['sessionTicketID'],
		                    'date_booked' => $booking['created']
		        );
	        }
		}
		return $fullBookingsArray;
	}
	
	//sort the array appropriatly
	function aasort (&$array, $key) {
	    $sorter=array();
	    $ret=array();
	    reset($array);
	    foreach ($array as $ii => $va) {
	        $sorter[$ii]=$va[$key];
	    }
	    asort($sorter);
	    foreach ($sorter as $ii => $va) {
	        $ret[$ii]=$array[$ii];
	    }
	    $array=$ret;
	}
	
	if($_GET['order'] == 1){ 
		$fullBookingsArray = bookingDateArray($bookings);
		
	 }
	
	 if($_GET['order'] == 2){
		$fullBookingsArray = surnameArray($bookings);
		 
	}
      
?>
<?php if( ! empty($fullBookingsArray)) { ?>

	<!-- SORT ARRAY -->
	<?php 
		if($_GET['order'] == 1){ 
			
			aasort($fullBookingsArray, "date_booked");
		 }
		
		 if($_GET['order'] == 2){
			
			aasort($fullBookingsArray, "user_surname"); 
		} 
	?>
	
	
	<?php foreach($fullBookingsArray as $booking){ ?>
	
		<tr class="tr" id="active-tr-<?php echo $booking['id']; ?>">
			
				<td class="td"><p><?php echo $booking['id']; ?></p></td>
				<td class="td"><p><?php echo $booking['user_surname']; ?>, <?php echo $booking['user_first_name']; ?></p></td>
				<td class="td"><p><?php echo $booking['email']; ?></p></td>
				<td class="td"><p><?php echo date("l, d M Y H:i:s",strtotime($booking['date_booked'])); ?></p></td>
				<td class="td">
					<p>
						<a class="manage-booking" data-user-id="<?php echo $booking['user_id']; ?>" data-session-ticket-id="<?php echo $booking['user_id']; ?>" data-booking-id="<?php echo $booking['id']; ?>">Manage</a>
					</p>
				</td>
			
		</tr>
		
		<tr class="tr">
			
				<td colspan="5"><div class="tr-manage" id="tr-<?php echo $booking['id']; ?>">Manage this booking</div></td>
			
		</tr>
		
	<?php } ?>
<?php }else{ ?>

<?php if($_GET['order'] == 1){ ?>
	<tr class="tr">
			<td class="td" colspan="5"><p>Sorry, there seems to be no bookings made this month.</p></td>			
	</tr>
<?php } ?>
	
<?php if($_GET['order'] == 2){ ?>
	<tr class="tr">
			<td class="td" colspan="5"><p>Sorry, there seems to be no bookings for surnames beginning with <?php echo $_GET['letter']; ?>.</p></td>			
	</tr>
<?php } ?>
	
	
	
<? } ?>

<script>
	$(document).ready(function(){
		$(".manage-booking").click(function(){
			
			var bookingID = $( this ).attr( "data-booking-id" );
			var	trID = "#tr-" + bookingID;
			var	actTrID = "#active-tr-" + bookingID;
			
			//slide up all others (shouldnt be more than one..)
			/* $('.td-on-show').slideUp( "fast" ); */
			/* $( '.td-on-show' ).removeClass('td-on-show'); */

			
			 if ( $( trID + ":first" ).is( ":hidden" ) ) { 
		  			$( trID ).slideDown( "fast" );
		  			$( actTrID ).addClass('tr-active');
		  			
		  		    /* $( trID ).parent().addClass('td-on-show');  */
		  			/* alert("hello"); */
		  			
		  		
		  		} else {
			    	$( trID ).slideUp( "fast" );
			    	$( actTrID ).removeClass('tr-active');
			    	/* $( trID ).parent().removeClass('td-on-show'); */
				} 
				
				
				
				//load in trip/booking details dynamically
				$( trID ).html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/booking-details.php?id=' + bookingID);


		});
	});
</script>
			
<script type="text/javascript">
	$(function () {		
		$('input#id-search').quicksearch('table tbody tr');		
	});
</script>