<?php $thedomain = $_SERVER['HTTP_HOST']; ?>
<?php 
	   //INCLUDE HEADER
	   $header = $_SERVER['DOCUMENT_ROOT'];
	   $header .= "/common/ga.php";
	   include_once($header);
?>

<link rel="stylesheet" type="text/css" href="http://<?php echo $thedomain; ?>/common/header/header.css?i=<?php echo rand(0, 10000) ?>">


<div id="nv1">
	<div id="nv1-wrapper">
		<?php
			//if checkLogin returns not logged in
			if (!$_SESSION['loggedIn'] === true) {
		?>
		<img src="http://<?php echo $thedomain; ?>/img/scuba.png" height="50px">
		<h1 id="nv1-logo"><a href="http://<?php echo $thedomain; ?>/">Scuba Where</a></h1>
		<div class="nv1-opt"><a href="http://<?php echo $thedomain; ?>/register/">Register</a></div>
		<div class="nv1-opt"><a href="http://<?php echo $thedomain; ?>/login/">Login</a></div>
		<div class="nv1-opt"><a href="http://<?php echo $thedomain; ?>/discover/">Discover</a></div>
		<form action="http://<?php echo $thedomain; ?>/explore/" method="get" id="nv1-search-form">
			<input type="text" id="nv1-search-box" placeholder="Example: Red Sea">
			<input type="submit" value="Search" class="nv1-search">
		</form>
		<?php
			}
			//if checkLogin returns logged in
			else {
		?>
		<img src="http://<?php echo $thedomain; ?>/img/scuba.png" height="50px">
		<h1 id="nv1-logo"><a href="http://<?php echo $thedomain; ?>/">Scuba Where</a></h1>
		<div class="nv1-opt"><a href="#">Account</a></div>
		<div class="nv1-opt"><a href="#">Cart</a></div>
		<div class="nv1-opt"><a href="http://<?php echo $thedomain; ?>/discover/">Discover</a></div>
		<form action="http://<?php echo $thedomain; ?>/explore/" method="get" id="nv1-search-form">
			<input type="text" id="nv1-search-box" placeholder="Example: Red Sea">
			<input type="submit" value="Search" class="nv1-search">
		</form>
		<?php				
			}
		?>
	</div>
</div>