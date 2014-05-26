<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	
?>
<?php 
	//function cuts the length of stings to length if over that
	function cutString($string, $length){
	
		if(strlen($string) > $length){
			$string = substr($string, 0, $length);
			$string = $string . "[...]";
		}
		
		return $string;
	}
?>

<!-- Live on page form validation -->
<script>
	$.validate({
    modules : 'location, date, security, file',
    onModulesLoaded : function() {
    }
  });
</script>


<script>
	//switch between switch and list view (switch by default)
	$(document).ready(function(){
		//add active class by default to #switch
		$('#switch').addClass('switch-act');
		$("#list-switch-wrap").html('<img src="img/loading.gif">').load('tabs/trip-list-switch/switch.php');
		
		
		//switch clicked
		$("#switch").click(function(){
			$('#switch').addClass('switch-act');
			$('#list').removeClass('switch-act');
			
			$("#list-switch-wrap").html('<img src="img/loading.gif">').load('tabs/trip-list-switch/switch.php');
			
		});
		
		//list clicked
		$("#list").click(function(){
			$('#list').addClass('switch-act');
			$('#switch').removeClass('switch-act');
			
			$("#list-switch-wrap").html('<img src="img/loading.gif">').load('tabs/trip-list-switch/list.php');
		});
	});
</script>
	
<div id="wrapper">
	
	<div id="list-switch" class="floating">
		
		<div id="switch">
			Switch View
		</div>
		
		<div id="list">
			List View
		</div>
	</div>
	
	<div id="list-switch-wrap">
		
	</div>
	
	
	
	<div id="activate-wrap">
	</div>
    
</div>

	

