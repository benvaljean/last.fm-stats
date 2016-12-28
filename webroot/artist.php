<?php
$artist=$_GET['artist'];
define( 'MUSICSTATS', true );
include 'header.php';
include 'dbconnect.php';
include 'queryartistsong.php';
?>
<h2><?php echo $artist . "</h2>";

echo '<table>';

$query="select count(*) as plays from plays where artist='$artist'";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo '<tr><td><b>Total plays: </td><td>' . $row['plays'] . '</b></td></tr>';
  }
}
else {
  echo '0 results';
}

$query="select min(dt) as first,max(dt) as last from plays where artist='$artist'";
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
$result=$connect->query("select count(*) as count, year(dt) as year from plays where artist='$artist' group by year order by year");
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

queryartistsong("select count(*) as count, track from plays where artist='$artist' group by 2 order by 1 desc"); 

$conn->close(); ?>
</body></html>
