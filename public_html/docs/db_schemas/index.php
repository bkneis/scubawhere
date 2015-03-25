<?php
$dir_open = opendir('.');

while(false !== ($filename = readdir($dir_open))){
    if($filename != "." && $filename != ".."){
        $link = "<a href='./$filename'>$filename</a><br />";
        echo $link;
    }
}

closedir($dir_open);
?>