<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/bookings.php");
	require_once($root."/engine/core/db/interface/trips.php");
	require_once($root."/engine/core/db/interface/sessions.php");
	require_once($root."/engine/core/db/interface/users.php");
	require_once($root."/engine/core/db/interface/tickets.php");
	
	$tripID = $_GET['id'];
	$sessID = $_GET['sessID'];
	
	
	if(isset($sessID)){
		
		$sessionTickets = get_session_tickets_by_session($sessID);
		
		if(is_array($sessionTickets)){
			foreach($sessionTickets as $sessionTicket){
				$bookings = get_bookings_by_session_ticket($sessionTicket['id']);
				
				if(is_array($bookings)){
					foreach($bookings as $booking){
					
						$user = get_user_by_id($booking['userID']);
						$ticket = get_ticket_by_id($sessionTicket['ticketID']);
						
						
						$bookingArry[] = array(
							'id'  => $booking['id'],
							'firstName'  => $user['firstName'],
							'lastName'  => $user['lastName'],
							'date'  => $booking['created'],
							'ticket'  => $ticket['name'],
							'email'  => $user['email']
						);
					}
				}
			}
		}
	}else{
	
		$sessions = get_sessions_by_trip($tripID);    
		if(is_array($sessions)){
			foreach($sessions as $session){
				$sessionTickets = get_session_tickets_by_session($session['id']);
				
				if(is_array($sessionTickets)){
					foreach($sessionTickets as $sessionTicket){
						$bookings = get_bookings_by_session_ticket($sessionTicket['id']);
						
						if(is_array($bookings)){
							foreach($bookings as $booking){
							
								$user = get_user_by_id($booking['userID']);
								$ticket = get_ticket_by_id($sessionTicket['ticketID']);
								
								
								$bookingArry[] = array(
									'id'  => $booking['id'],
									'firstName'  => $user['firstName'],
									'lastName'  => $user['lastName'],
									'date'  => $booking['created'],
									'ticket'  => $ticket['name'],
									'email'  => $user['email']
								);
							}
						}
					}
				}
			}
		}
	}
?>



<table border="0" class="table">
	<thead class="thead">
		<tr class="tr">
			<th class="th">Name</th>
			<th class="th">Email</th>
			<th class="th">Date Booked</th>
			<th class="th">Ticket</th>
		</tr>
	</thead>
	<tbody class="tbody" id="load-bookings">
		<?php if(is_array($bookingArry)){ ?>
			<?php foreach($bookingArry as $booking){ ?>
				<tr class="tr">
					<td class="td"><p><?php echo $booking['lastName']; ?>, <?php echo $booking['firstName']; ?></p></td>
					<td class="td"><p><?php echo $booking['email']; ?></p></td>
					<td class="td"><p><?php echo $booking['date']; ?></p></td>
					<td class="td"><p><?php echo $booking['ticket']; ?></p></td>
				</tr>
					 
			<?php } ?>
		<?php }else{ ?>
			<tr class="tr">
				<td class="no-bookings" colspan="4"><p>There are no bookings for this trip..</p></td>
			</tr>
		<?php } ?>
	</tbody>
</table>
