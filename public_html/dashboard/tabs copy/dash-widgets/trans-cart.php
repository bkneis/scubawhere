<script>
	
		var cartString = $.cookie('cart').toString();
		var cartStringLength = cartString.length;
		cartStringLength = (cartStringLength + 1) / 2;
		
		if(cartStringLength > 0){
		
			if(cartStringLength == 1){
				$('#can-delete').html('');
			}
			
			$("#trans-num").html(cartStringLength);
			
		}
		
</script>

<div id="trans-num">0</div>
<div id="trans-unit">item<span id="can-delete">s</span> in your transaction cart.</div>

