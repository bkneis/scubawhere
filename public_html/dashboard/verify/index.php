<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/engine/core/init.php"); ?>
<?php $thedomain = $_SERVER['HTTP_HOST']; ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
  <TITLE>Scuba Where | Verification </TITLE>
  <link rel="stylesheet" type="text/css" href="http://<?php echo $thedomain; ?>/common/css/universal-styles.css?i=<?php echo rand(0, 10000) ?>">
  <link rel="stylesheet" type="text/css" href="index.css?i=<?php echo rand(0, 10000) ?>">
</HEAD>
<BODY>

	<?php
	   //INCLUDE HEADER
	   $header = $_SERVER['DOCUMENT_ROOT'];
	   $header .= "/common/header/header.php";
	   include_once($header);
    ?>

	<div id="page-wrapper">

		<h1 class="h1">Thank you for registering your Dive Center!</h1>
		<h3 class="h3">Just one more step to activate your account!</h3>
		<p>We've sent you an email with an activation code to the adress you registered with. Just enter this code here or click the link in the email.</p>

		<hr class="hr" />

		<br />

		<form action="../../engine/company/verify_company.php" method="post" accept-charset="utf-8">
			<label>Your email</label>
			<br/>
			<input type="email" name="email" class="txt-in" id="email" value="<?php echo isset($_GET['email']) ? $_GET['email'] : '';?>">
			<br/>
			<br/>
			<label>Activation Code (sent to you via email)</label>
			<br/>
			<input type="text" name="code" class="txt-in" id="code" value="<?php echo isset($_GET['code']) ? $_GET['code'] : '';?>">
			<br/>
			<br/>
			<input type="submit" value="Activate" class="frm-sbmt"/>

		</form>
	</div>

	<?php
	   //INCLUDE FOOTER
	   $footer = $_SERVER['DOCUMENT_ROOT'];
	   $footer .= "/common/footer/footer.php";
	   include_once($footer);
    ?>

</BODY>
</HTML>
