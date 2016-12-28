<?php
$q=$_GET['q'];
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';
include 'queryartistsong.php';
?>

<h2>Artist results</h2>

<?php queryartistsong("select distinct(artist) from plays where artist like '%$q%' limit 50",false); ?>

<h2>Song results</h2>

<?php queryartistsong("select artist,track from plays where track like '%$q%' group by 2 limit 50",false); ?> 

<?php $conn->close(); ?>
</html>
