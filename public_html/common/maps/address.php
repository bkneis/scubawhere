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
      <h1>Address Locator</h1>
      <?php include 'address_locator.php'; ?>      
  </body>
</html>
