<?php
$dir_open = opendir('.');

$excluded_filenames = [
	'.',
	'..',
	'index.php'
];

while(false !== ($filename = readdir($dir_open))) {
    if(!in_array($filename, $excluded_filenames)) {
        echo "<a href='./$filename'>$filename</a><br />";
    }
}

closedir($dir_open);
?>
