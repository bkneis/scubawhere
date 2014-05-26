<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once($root."/engine/core/init.php");

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Map Testing</title>       
  </head>
  <body>
      <h1>Dive Locator</h1>
      <?php include 'dive_locator.php'; ?>
  </body>
</html>