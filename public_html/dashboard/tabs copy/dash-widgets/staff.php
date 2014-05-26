<?php 
	$root =  $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/init.php");
	require_once($root."/engine/core/db/interface/personnel.php");
	
	$staff = get_all_staff_by_company($_SESSION['id']);
?>

<div class="block-title">Staff</div>

<?php if(empty($staff)){ ?> 
	<div id="no-staff">You haven't added any staff yet..</div>
<?php }else{ ?>
	<div id="no-staff">You have got some staff **Change this bit now**..</div>
<?php } ?>