
<div class="floating">
	<form id="add-tickets" action="/engine/trips/add_tickets.php" method="post">
		<span id="single-tickets-wrap" class="form-item">
			<label class="item-head">Tickets</label>
			
			<div class="single-ticket floating">
				<label class="item-head">Single Ticket <span class="remove-ticket">&#10005;</span></label>
				<div class="ticket-body">
					<label class="ticket-label">Ticket Name</label>
					<input type="text" placeholder="Name" name="name[]" class="form-text ticket-input"/>
					
					<label class="ticket-label">Description</label>
					<textarea placeholder="Description" name="description[]" class="form-area ticket-area"/>
					
					<div class="price-dur-wrap">
					<span>
						<label class="ticket-label">Duration</label>
						<input type="text" placeholder="Duration" name="duration[]" class="form-text ticket-input"/>
					</span>
					<span>
						<label class="ticket-label">Price</label>
						<input type="text" placeholder="Price" name="price[]" class="form-text ticket-input"/>
					</span>
					</div>
				</div>
			</div>
			
		</span>
		<a id="add-another">+ Add Another</a>
		<input type="hidden" name="ticket-count" value="1"/>
		<input type="submit" value="Save" id="save-tickets" class="form-button">
	</form>
	
	
</div>

