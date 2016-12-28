<?php
define( 'MUSICSTATS', true );
include 'queryartistsong.php';
include 'dbconnect.php';
include 'header.php';
$latest=$_GET['latest'];
$start=$_GET['start'];
$end=$_GET['end'];

$scrobbles = 250;

if($latest == true)
{
    echo '<h2>Latest ' . $scrobbles . ' scrobbles</h2>';
    queryscrobbleslatest($scrobbles);
}

else
{
    echo '<h2>Scrobbles between ' . $start . ' and ' . $end . '</h2>';
	queryscrobblesrange($start,$end);
}

$conn->close(); ?>
</html>
