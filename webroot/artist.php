<?php
$artist=$_GET['artist'] ?? '';
define( 'MUSICSTATS', true );
include 'header.php';
include 'dbconnect.php';
include 'queryartistsong.php';
?>
<h2><?php echo stripslashes($artist) . "</h2>";

echo '<table>';

$result = musicstats_query($connect, 'SELECT COUNT(*) AS plays FROM plays WHERE artist = ?', [$artist]);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo '<tr><td><b>Total plays: </td><td>' . $row['plays'] . '</b></td></tr>';
  }
}
else {
  echo '0 results';
}

$result = musicstats_query($connect, 'SELECT MIN(dt) AS first, MAX(dt) AS last FROM plays WHERE artist = ?', [$artist]);
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
$result = musicstats_query($connect, 'SELECT COUNT(*) AS count, YEAR(dt) AS year FROM plays WHERE artist = ? GROUP BY year ORDER BY year', [$artist]);
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
<h2>Most-played songs by <?php echo $artist . "</h2>";

queryartistsong('SELECT COUNT(*) AS count, artist, track FROM plays WHERE artist = ? GROUP BY artist, track ORDER BY count DESC', [$artist]);

?>
</body></html>
