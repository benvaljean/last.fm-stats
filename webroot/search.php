<?php
$q=$_GET['q'] ?? '';
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';
include 'queryartistsong.php';
?>

<h2>Artist results</h2>

<?php queryartistsong('SELECT DISTINCT artist FROM plays WHERE artist LIKE ? LIMIT 50', ['%' . $q . '%'], rankingformat:false); ?>

<h2>Song results</h2>

<?php queryartistsong('SELECT artist, track FROM plays WHERE track LIKE ? LIMIT 50', ['%' . $q . '%'], rankingformat:false); ?>

</html>
