<style>
	body {
		line-height: 1.5;
	}
</style>

<h1>Database Schemas</h1>

<p>
	Newest file on top:
</p>

<?php

$excluded_filenames = [
	'.',
	'..',
	'index.php'
];

$files = scandir('.', 1);

foreach($files as $filename) {
    if(!in_array($filename, $excluded_filenames)) {
    	echo "<a href='./$filename'>$filename</a><br />";
    }
}

?>
