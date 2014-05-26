<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	include_once($root."/engine/core/db/interface/bookings.php");
	include_once($root."/engine/core/db/interface/users.php");
	include_once($root."/engine/core/db/interface/tickets.php");
	include_once($root."/engine/core/db/interface/trips.php");
	include_once($root."/engine/core/db/interface/places.php");
	include_once($root."/engine/core/db/interface/agencies.php");
?>

<?php 

	$booking_id = $_GET['id']; 
	
	$booking = get_booking_by_id($booking_id);
	
	//trip/session details
	$session = get_session_by_id($session_ticket['sessionID']);
	
	$trip = get_trip_by_id($session['tripID']);
	
	//ticket details
	$session_ticket_id = $booking['sessionTicketID'];
	
	$session_ticket = get_session_ticket_by_id($session_ticket_id);
	
	$ticket = get_ticket_by_id($session_ticket['ticketID']);
	
		//tickets availiable
		$bookingCount = get_session_ticket_booking_count($session['id'], $ticket['id']);
		
		$available = $ticket['capacity'] - $bookingCount;

	//user details
	$user_id = $booking['userID'];
	
	$user = get_user_by_id($user_id);
	
	//country details
	$country = get_COUNTRY_CONSTANT_by_id($user['countryID']);
	
	//certification details
	if($user['certificateID'] > 0){
		$certificateArray = get_CERTIFICATE_CONSTANT_by_id($user['certificateID']);
		$certificate = $certificateArray['name'];
		$certificateAgencyArray = get_AGENCY_CONSTANT_by_id($certificateArray['agencyID']);
		$certificateAgency = $certificateAgencyArray['name'];
	}else{
		$certificate = "N/A";
		$certificateAgency = "N/A";
	}
?>


<div class="details-col marg-right">
	<h3>User</h3>
	<table>
		<tr>
			<td>DOB:</td>
			<td><?php echo date("d M Y",strtotime($user['dob'])); ?></td>
		</tr>
		<tr>
			<td>Country:</td>
			<td><?php echo $country['name']; ?></td>
		</tr>
		<tr>
			<td>Phone No:</td>
			<td><?php echo $country['phone']; ?></td>
		</tr>
		<tr>
			<td>Certification:</td>
			<td><?php echo $certificate; ?></td>
		</tr>
		<tr>
			<td>Certifying agency:</td>
			<td><?php echo $certificateAgency; ?></td>
		</tr>
	</table>
</div>

<div class="details-col marg-right">
	<h3>Trip</h3>
	<table>
		<tr>
			<td>Name:</td>
			<td><?php echo $trip['name']; ?></td>
		</tr>
		<tr>
			<td>Duration:</td>
			<td><?php echo $trip['duration']; ?></td>
		</tr>
		<tr>
			<td>Start Date/Time:</td>
			<td><?php echo date("d M Y H:i",strtotime($session['start'])); ?></td>
		</tr>
		<tr>
			<td>End Date/Time:</td>
			<td><?php echo date("d M Y H:i",strtotime($session['end'])); ?></td>
		</tr>
	</table>
</div>

<div class="details-col">
	<h3>Ticket</h3>
	<table>
		<tr>
			<td>Name:</td>
			<td><?php echo $ticket['name']; ?></td>
		</tr>
		<tr>
			<td>Price:</td>
			<td>&pound;<?php echo $ticket['price']; ?></td>
		</tr>
		<tr>
			<td>Max capacity:</td>
			<td><?php echo $ticket['capacity']; ?></td>
		</tr>
		<tr>
			<td>Currently available:</td>
			<td><?php echo $available; ?></td>
		</tr>
	</table>
</div>
<div class="edit-user" id="edit-user-<?php echo $user_id; ?>"></div>
<div class="edit-bar">
	<span class="edit-this"><span class="edit-pencil"></span>Edit</span>
	<span class="cancel-this" id="<?php echo $booking_id; ?>">Cancel/Refund</span>
	<!-- <span class="del-cookie">*Delete cookie*</span> -->
</div>


<script>
	$(document).ready(function(){
		$(".edit-this").click(function(){
			
			$('#edit-user-<?php echo $user_id; ?>').html('<div id="loading-centre"><img src="img/loading.gif"></div>').load('tabs/find-user-booking/edit.php?id=' + <?php echo $user_id; ?>);
		});
	});
</script>

<script>
	$(document).ready(function(){
		$(".cancel-this").click(function(event){
			event.stopImmediatePropagation();//stops click event executing twice.
			//make sure they are strings
			var bookingRef = $(this).attr('id').toString();
			var tempCart = $.cookie('cart');
		/* 	tempCart = tempCart.toString(); */
						
			//check whether the string/"array" contains this booking ref already
		    if(tempCart.indexOf(bookingRef) >= 0){
				//it is already in the string/"array"
				//so it needs to be deleted
				alert("removing");
				removeFromTransactionCart(bookingRef);
				
				//change tab text
				$("#" + bookingRef).html('Cancel/Refund');
					
			}else{
				//it doesn't contain the ref yet
				alert("Adding");
				addToTransactionCart(bookingRef);
						
				
				//change tab text
				$("#" + bookingRef).html('Remove From Cart'); 

			}
			
			alert($.cookie('cart'));
		});
	});
</script>

<!-- temporary delete cookie function -->
<script>
	$(document).ready(function(){
		$(".del-cookie").click(function(){
			$.cookie("cart", null);
		});
	});
</script>
