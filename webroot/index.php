<?php
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';
include 'queryartistsong.php';

$query="select max(dt) as latest from plays";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $latest = $row['latest'];
  }
}
else {
  $latest = '0 results';
}
?>

<div class="alignleft">Last scrobble: <?php echo $latest; ?>  <a href="http://jessica.rabbit.name/musicstats/scrobbles.php?latest=true">Latest scrobbles</a></div>
<div class="alignright">
Last <a href="lastxmths.php?months=3">3</a> | <a href="lastxmths.php?months=6">6</a> | <a href="lastxmths.php?months=12">12</a> | <a href="lastxmths.php?months=18">18</a> | <a href="lastxmths.php?months=24">24</a> | <a href="lastxmths.php?months=48">48</a> | <a href="lastxmths.php?months=60">60</a> | <a href="lastxmths.php?months=120">120</a> months

<form action="scrobbles.php"><input type="text" name="start"> <input type="text" name="end"><input type="submit" value="Date range"></form>
</div>
<table>
<?php


$query="select extract(YEAR from dt) as year, count(1) as count from plays group by year order by year asc";

$result = $connect->query($query);

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

<h2>Most-played songs</h2>
<?php queryartistsong("select count(*) as count, artist, track from plays group by 2, 3 order by 1 desc limit 200"); ?>

<h2>Most played artists</h2>
<?php queryartistsong("select count(*) as count, artist from plays group by 2 order by 1 desc limit 100"); ?>

<?php $conn->close(); ?>
</html>
