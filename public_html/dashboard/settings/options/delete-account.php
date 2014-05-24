<?php
	$root =  $_SERVER['DOCUMENT_ROOT'];
	require_once($root."/engine/core/init.php");
	require_once($root."/engine/core/db/companies.php");
	delete_company_by_id($_SESSION['id']);
?>

Your Account Has Been Deleted. GOODBYE.