<?php
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';
$track=$_GET['track'] ?? '';
$artist=$_GET['artist'] ?? '';

echo '<h2>' . $artist . ' - ' . stripslashes($track) . '</h2>';

echo '<table>';

$result = musicstats_query($connect, 'SELECT `rank`, `plays` FROM mostplayedrankings WHERE track = ? AND artist = ?', [$track, $artist]);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo '<tr><td><b>Total plays: </td><td>' . $row['plays'] . '</b></td></tr>';
    echo '<tr><td>Overall ranking: </td><td>' . $row['rank'] . '</tr></tr>';
  }
}
else {
  echo '0 results';
}

$result = musicstats_query($connect, 'SELECT MIN(dt) AS first, MAX(dt) AS last FROM plays WHERE track = ? AND artist = ?', [$track, $artist]);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo '<tr><td>First play:</td><td>' . $row['first'] . '</td></tr>';
    echo '<tr><td>Latest play:</td><td>' . $row['last'] . '</td></tr>';
  }
}
else {
  echo '0 results';
}

echo '</table>';
$result = musicstats_query($connect, 'SELECT COUNT(*) AS count, YEAR(dt) AS year FROM plays WHERE track = ? AND artist = ? GROUP BY year ORDER BY year', [$track, $artist]);
if ($result->num_rows > 0) {
	echo '<h2>Plays by year</h2><table>';
  while($row = $result->fetch_assoc()) {
      echo '<tr><td>' . $row['year'] . '</td><td>' .  $row['count'] . '</td></tr>';
    }
     echo '</table>';
  }
  else {
    echo '0 results';
  }
?>
<h2>Play history</h2>
<?php
$start = microtime(true);
$result = musicstats_query($connect, 'SELECT dt, oldartist, oldtrack FROM plays WHERE track = ? AND artist = ? ORDER BY dt', [$track, $artist]);
$queryTime = microtime(true) - $start;
if ($result->num_rows > 0) {
        echo '<table>';
  while($row = $result->fetch_assoc()) {
      echo '<tr><td>' . $row['dt'] . '</td><td>' . $row['oldartist'] . '</td><td>' . $row['oldtrack'] . '</td></tr>';
    }
     echo '</table>';
     $totalTime = microtime(true) - $start;
     printf("Query: %.4f sec, total: %.4f sec\n", $queryTime, $totalTime);
  }
  else {
    echo '0 results';
  }

?>
</body></html>
