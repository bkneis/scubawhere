<?php
$root =  $_SERVER['DOCUMENT_ROOT'];
require_once($root."/engine/core/init.php");
require_once($root."/engine/core/db/security.php");


protect_page(DB_TYPE_COMPANIES);


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
  <TITLE>Scuba Where | Account Settings</TITLE>
  <link rel="stylesheet" href="../common/css/universal-styles.css?i=<?php echo rand(0, 10000) ?>" type="text/css" media="screen" charset="utf-8">
  <link rel="stylesheet" type="text/css" href="../css/style.css?i=<?php echo rand(0, 10000) ?>">
  <link rel="stylesheet" type="text/css" href="css/style.css?i=<?php echo rand(0, 10000) ?>">
  
  <script src="<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/common/js/jquery.js" ?>"></script>
  
  <script>
  	$(document).ready(function(){
		  	$("#remove-account").click(function(){
		  		
		  		var r=confirm("Are you sure you want to do this? Your account will be deleted.");
				if (r==true)
				  {
				  	$('#remove-result').load('options/delete-account.php');
				  }
				else
				  {
				  	$('#remove-result').html('Your account HAS NOT been deleted.');
				  }
		  		
		  	});
		});
  </script>
</HEAD>
<BODY>
	
	<?php 
	   //INCLUDE HEADER
	   $header = $_SERVER['DOCUMENT_ROOT'];
	   $header .= "/dive-admin/common/header/header.php";
	   include_once($header);
    ?>
	
	<div id="page-wrapper">
		<div id="remove-account">Remove Account</div>
		<div id="remove-result"></div>
	</div>
	
	<?php 
	   //INCLUDE FOOTER
	   $footer = $_SERVER['DOCUMENT_ROOT'];
	   $footer .= "/dive-admin/common/footer/footer.php";
	   include_once($footer);
    ?>
    
</BODY>
</HTML>