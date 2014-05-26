<script src="tabs/add-agent/js/script.js"></script>
<script type="text/javascript">
    function showMe (box) {
        
        var chboxs = document.getElementsByName("c1");
        var vis = "none";
        for(var i=0;i<chboxs.length;i++) { 
            if(chboxs[i].checked){
             vis = "block";
                break;
            }
        }
        document.getElementById(box).style.display = vis;
    
    
    }
    </script>

<div id="wrapper">
		<div class="box100">
		<label class="dgreyb">Create Agent</label>
		
		<div class="padder">
			
			<form id="add-agent-form">
				<div class="form-row">
					<label class="field-label">Agent Name</label>
					<input type="text" name="name">
				</div>
				
				<div class="form-row">
					<label class="field-label">Agent's Website (Optional)</label>
					<input type="text" name="website">
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch Name</label>
					<input type="text" name="branch_name">
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch Address</label>
					<textarea name="branch_address" rows="3" cols="20"></textarea>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch Telephone</label>
					<input type="text" name="branch_phone">
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Branch E-Mail</label>
					<input type="text" name="branch_email">
				</div>
				
				<div class="form-row">
					<input type="checkbox" name="c1" onclick="showMe('billing-div')">Billing details are the same?
				</div>
                                
                                <div id="billing-div" style="display:none">
                                <div class="form-row">
					<label class="field-label">Billing Address</label>
					<textarea name="billing_address" rows="3" cols="20"></textarea>
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Billing Phone Number</label>
					<input type="text" name="billing_phone">
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Billing E-Mail Address</label>
					<input type="text" name="billing_email">
				</div>
				
				</div>
                                
                                <div class="form-row">
					<label class="field-label">Commission</label>
					<input type="text" name="commission">
				</div>

				<div class="form-row">
					<label class="">Business Terms</label>
					<div class="box50" style="padding-left:4.5cm;">
						
						<select id="terms">
							<option>Please select..</option>
                                                        <option>Full Amount</option>
                                                        <option>Deposit Only</option>
                                                        <option>Banned</option>
						</select>
					</div>
				</div> 
				
				<input type="hidden" class="token" name="_token">
				<input type="submit" class="bttn blueb" id="add-agent" value="Add Agent">

			</form>
		</div>
		
	</div>
</div>