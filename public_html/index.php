<?php

// This page should never be accessible, because the .htaccess reroutes everything to /blog
// But in case that mod_rewrites fails, this page is here to redirect the user with PHP

header("Location: /blog/");
exit;

?>
