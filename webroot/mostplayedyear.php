<?php
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';

$year=$_GET['year'];
$month=$_GET['month'];
if (strlen($month) == 0){
//year mode
  $doyear=1;
  $criteria="year(dt)=$year";
} else {
  $criteria="year(dt)=$year and month(dt)=$month";
  $monthtext=date("F", mktime(0, 0, 0, $month));
}
?>

<h2>Most-played songs in <?php echo $monthtext . " " . $year; ?></h2>
<?php

if ($doyear==1) {
  $query="select month(dt) as monthnumeric, monthname(dt) as month, count(1) as count from plays where year(dt)=$year group by month(dt) order by month(dt) asc";
  $result = $connect->query($query);
  if ($result->num_rows > 0) {
    // output data of each row from $result
      echo '<h2>Month</h2><table>';
    while($row = $result->fetch_assoc()) {
      echo '<tr><td><a href="mostplayedyear.php?year=' . $year . '&month=' . $row['monthnumeric']. '">' .  $row['month'] . '</a></td><td>'. $row['count']. '</td></tr>';
    }
     echo '</table>';
  }
  else {
    echo '0 results';
  }
}


include 'queryartistsong.php';
queryartistsong("select count(*) as count, artist, track from plays where $criteria group by 2, 3 order by 1 desc limit 50")

?>
<h2>Most played artists in <?php echo $monthtext . " " . $year; ?></h2>
<table>
<?php
$query = "select count(*) as count, artist from plays where $criteria group by 2 order by 1 desc limit 25";

$result = $connect->query($query);

// if the $result contains at least one row
$rank=0;
if ($result->num_rows > 0) {
  // output data of each row from $result
  while($row = $result->fetch_assoc()) {
    $rank++;
    echo '<tr><td>'. $rank . '</td><td>' . $row['count']. '</td><td>'. $row['artist']. '</td></tr>';
  }
}
else {
  echo '0 results';
}

?>
</table>

<?php $conn->close(); ?>
</html>
