<?php
if( !defined( 'MUSICSTATS' ) ) {
        echo "This file is part of MusicStats and is not a valid entry point\n";
        die( 1 );
}


$connect = new mysqli("localhost", "musicstats", "passwordhere", "musicstats");
if (!$connect->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
}
?>
