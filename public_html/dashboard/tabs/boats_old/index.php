<div id="wrapper">
	
	<div class="yellow-helper">Please enter your boats and room types, then simply drag and drop your room types onto the appropriate boat.</div>
	
	
	<div class="box100 blueb">
		<label class="blueb">Room Types</label>
		
		<!-- using a list to make items easier to drag and drop -->
		<ul class="table-list" id="types-list">
			
				
		</ul>
		
		<div class="box-foot" id="new-type-foot"><span>Add new type:</span>
			<form>
				<input type="text" id="new-type-name" placeholder="Type Name" />
				<textarea id="new-type-description" placeholder="Type Description"></textarea>
				<input type="submit" id="save-type" value="Save" class="bttn blueb" />
			</form>
		</div>
	</div>
	
	<div class="box100 blueb">
		<label class="blueb">Boats</label>
		
		<!-- using a list to make items easier to drag and drop -->
		<ul class="table-list" id="boats-list">
			
		</ul>
		
		<div class="box-foot" id="new-boat-foot"><span>Add new boat:</span>
			<form class="validate">
				<input class="valid" type="text" id="new-boat-name" placeholder="Boat Name" />
				<input class="valid" type="text" id="new-boat-cap" placeholder="Boat Capacity" />
				<textarea class="valid" id="new-boat-description" placeholder="Boat Description"></textarea>
				<input type="submit" id="save-boat" value="Save" class="bttn blueb validate" />
			</form>
		</div>
		
	</div>
</div>


<script src="tabs/boats/js/script.js"></script>