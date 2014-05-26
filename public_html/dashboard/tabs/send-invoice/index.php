<script src="tabs/edit-agent/js/script.js"></script>

<div id="wrapper">
<div class="row">
		<div class="box30">
		<label class="dgreyb">Select Agent</label>
		
		<div class="padder">
		
		<ul>
		<li>Agent 1</li>		
		<li>Agent 2</li>
		<li>Agent 3</li>		
		<li>Agent 4</li>
		</ul>
			
		</div>
		
	</div>
	<div class="box70">
		<label class="dgreyb">Send Invoice</label>
		
		<div class="padder">
			
			<form id="add-agent-form">
				<div class="form-row">
					<label class="field-label">Agent Name</label>
					<input type="text" name="name" readonly>
				</div>
				
				<div class="form-row">
					<label class="field-label">Agent's website (Optional)</label>
					<input type="text" name="website" readonly>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch Name</label>
					<input type="text" name="branch_name" readonly>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch Address</label>
					<input type="text" name="branch_address" readonly>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch Telephone</label>
					<input type="text" name="branch_phone" readonly>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch E-Mail</label>
					<input type="text" name="branch_email" readonly>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Billing Address</label>
					<textarea name="billing_address" readonly></textarea>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Billing Phone Number</label>
					<input type="text" name="billing_phone" readonly>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Billing E-Mail Address</label>
					<input type="text" name="billing_email" readonly>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Commission</label>
					<input type="text" name="commission">
				</div>

				<div class="form-row">
					<label class="">Business Terms</label>
					<div class="box50" style="padding-left:4.5cm;">
						
						<select id="terms-select">
							<option>Please select..</option>
                                                        <option>Full Amount</option>
                                                        <option>Deposit Only</option>
                                                        <option>Banned</option>
						</select>
					</div>
				</div>
				
				<div class="form-row">
					<label class="field-label">Comments</label>
					<input type="text" name="comments">
				</div>
				
				<input type="hidden" class="token" name="_token">
				<input type="submit" class="bttn blueb" id="send-invoice" value="Send Invoice">

			</form>
		</div>
		
	</div>
	</div>
</div>