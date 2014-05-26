<?php

	$root = $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/db/interface/tickets.php");


    $company_id = $_SESSION['id'];
	$savedTickets = get_tickets_by_company($company_id);
	//get total tickets for company
	$numTickets = count_tickets_by_company($company_id);

?>


<div class="floating">
		<span class="form-item">
			<label class="item-head">Saved Tickets</label>
			
			<table id="ticket-table">
				<thead>
					<tr>
						<th class="ticket-col-one">Name</th>
						<th class="ticket-col-two">Description</th>
						<th class="ticket-col-thee">Price</th>
						<th class="ticket-col-four">Duration</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($savedTickets as $ticket){ ?>
						<tr id="<?php echo $ticket['id']; ?>">
							<td class="ticket-col-one"><?php echo $ticket['name']; ?></td>
							<td class="ticket-col-two"><?php echo $ticket['description']; ?></td>
							<td class="ticket-col-three"><?php echo $ticket['price']; ?></td>
							<td class="ticket-col-four"><?php echo $ticket['duration']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			
			
			
		</span>
</div>

<div class="floating">
		<span class="form-item">
			<label class="item-head">Edit</label>
			
			<form action="/engine/trips/edit_ticket.php" method="post">
			<div id="edit-ticket-wrap">
				<div style="text-align: center; padding: 20px; font-size: 24px; color: #8b8b8b;">No ticket selected..</div>
				
			</div>
			
			</form>
			
		</span>
</div>
