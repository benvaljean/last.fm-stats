<?php
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';
include 'queryartistsong.php';

$result = musicstats_query($connect, 'SELECT MAX(dt) AS latest FROM plays');
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $latest = $row['latest'];
  }
}
else {
  $latest = '0 results';
}
?>

<div class="alignleft">Last scrobble: <?php echo $latest; ?>  <a href="scrobbles.php?latest=true">Latest scrobbles</a></div>
<div class="alignright">
Last <a href="lastxmths.php?months=3">3</a> | <a href="lastxmths.php?months=6">6</a> | <a href="lastxmths.php?months=12">12</a> | <a href="lastxmths.php?months=18">18</a> | <a href="lastxmths.php?months=24">24</a> | <a href="lastxmths.php?months=48">48</a> | <a href="lastxmths.php?months=60">60</a> | <a href="lastxmths.php?months=120">120</a> months

<form action="scrobbles.php"><input type="text" name="start"> <input type="text" name="end"><input type="submit" value="Date range"></form>
</div>
<table>
<?php


$result = musicstats_query($connect, 'SELECT EXTRACT(YEAR FROM dt) AS year, COUNT(1) AS count FROM plays GROUP BY year ORDER BY year ASC');

$rank=0;
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $rank++;
    echo '<tr><td>'. $row['count']. '</td><td><a href="mostplayedyear.php?year='. $row['year']. '">'. $row['year'].'</a></td></tr>';
  }
}
else {
  echo '0 results';
}
?>
</table>

<h2>Most-played songs test</h2>
<?php queryartistsong('SELECT COUNT(*) AS count, artist, track FROM plays GROUP BY 2, 3 ORDER BY 1 DESC LIMIT 200'); ?>

<h2>Most played artists</h2>
<?php queryartistsong('SELECT COUNT(*) AS count, artist FROM plays GROUP BY 2 ORDER BY 1 DESC LIMIT 100'); ?>

</html>
