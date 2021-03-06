<?php
define( 'MUSICSTATS', true );
include 'dbconnect.php';
include 'header.php';
$track=addslashes($_GET['track']);
$artist=$_GET['artist'];
define( 'MUSICSTATS', true );

echo '<h2>' . $artist . ' - ' . stripslashes($track) . '</h2>';

echo '<table>';

$query="select rank,plays from mostplayedrankings where track='$track' and artist='$artist'";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo '<tr><td><b>Total plays: </td><td>' . $row['plays'] . '</b></td></tr>';
    echo '<tr><td>Overall ranking: </td><td>' . $row['rank'] . '</tr></tr>';
  }
}
else {
  echo '0 results';
}

$query="select min(dt) as first,max(dt) as last from plays where track='$track' and artist='$artist'";
$result = $connect->query($query);
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
$result=$connect->query("select count(*) as count, year(dt) as year from plays where track='$track' and artist='$artist' group by year order by year");
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
$result=$connect->query("select dt,oldartist,oldtrack from plays where track='$track' and artist='$artist' order by dt");
if ($result->num_rows > 0) {
        echo '<table>';
  while($row = $result->fetch_assoc()) {
      echo '<tr><td>' . $row['dt'] . '</td><td>' . $row['oldartist'] . '</td><td>' . $row['oldtrack'] . '</td></tr>';
    }
     echo '</table>';
  }
  else {
    echo '0 results';
  }

?>
</body></html>
