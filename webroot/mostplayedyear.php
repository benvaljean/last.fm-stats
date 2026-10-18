<?php
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';

$year=filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 9999]]);
if ($year === false || $year === null) {
  http_response_code(400);
  exit('A valid year is required.');
}
$monthtext="";

if (!isset($_GET['month'])){
//year mode
  $doyear=1;
  $criteria='YEAR(dt) = ?';
  $criteriaParams=[$year];
} else {
  $doyear=0;
  $month=filter_input(INPUT_GET, 'month', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
  if ($month === false || $month === null) {
    http_response_code(400);
    exit('The month must be between 1 and 12.');
  }
  $criteria='YEAR(dt) = ? AND MONTH(dt) = ?';
  $criteriaParams=[$year, $month];
  $monthtext=date("F", mktime(0, 0, 0, $month));
}
?>

<h2>Most-played songs in <?php echo $monthtext . " " . $year; ?></h2>
<?php

if ($doyear==1) {
  $result = musicstats_query($connect, 'SELECT MONTH(dt) AS monthnumeric, MONTHNAME(dt) AS month, COUNT(1) AS count FROM plays WHERE YEAR(dt) = ? GROUP BY monthnumeric, month ORDER BY MONTH(dt) ASC', [$year]);
  if ($result->num_rows > 0) {
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
queryartistsong("SELECT COUNT(*) AS count, artist, track FROM plays WHERE $criteria GROUP BY 2, 3 ORDER BY 1 DESC LIMIT 50", $criteriaParams)

?>
<h2>Most played artists in <?php echo $monthtext . " " . $year; ?></h2>
<table>
<?php
$result = musicstats_query($connect, "SELECT COUNT(*) AS count, artist FROM plays WHERE $criteria GROUP BY 2 ORDER BY 1 DESC LIMIT 25", $criteriaParams);

$rank=0;
if ($result->num_rows > 0) {
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

</html>
