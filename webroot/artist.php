<html><head>
<title>MP3Stats</title>
<link rel="stylesheet" href="/musicstats.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8">
</head>
<body>
<?php
$artist=$_GET['artist'];

?>
<center>
<h1>Mp3 Stats</h1>
<h2><?php echo $artist . "</h2>";

$connect = new mysqli("localhost", "musicstats", "4f6hmklFHf54", "musicstats");
if (!$connect->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
}

echo '<table>';

$query="select count(*) as plays from plays where artist='$artist'";
#$query="select rank,plays from mostplayedrankings where track='$track' and artist='$artist'";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  // output data of each row from $result
  while($row = $result->fetch_assoc()) {
    echo '<tr><td><b>Total plays: </td><td>' . $row['plays'] . '</b></td></tr>';
#    echo '<tr><td>Overall ranking: </td><td>' . $row['rank'] . '</tr></tr>';
  }
}
else {
  echo '0 results';
}


$query="select min(dt) as first,max(dt) as last from plays where artist='$artist'";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  // output data of each row from $result
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

$query = "select count(*) as count, track from plays where artist='$artist' group by 2 order by 1 desc";

$result = $connect->query($query);
echo '<table>';
// if the $result contains at least one row
$rank=0;
if ($result->num_rows > 0) {
  // output data of each row from $result
  while($row = $result->fetch_assoc()) {
    $rank++;
    //echo '<tr><td>'. $rank . '</td><td>' . $row['count']. '</td><td>'. $row['artist']. '</td><td>'. $row['track'] . '</td></tr>';

echo '<tr><td>'. $rank . '</td><td>' . $row['count']. '</td><td><a href="track.php?artist=' . $artist . '&track=' . addslashes($row['track']) . '">' . $row['track'] . '</a></td></tr>';


  }
echo '</table>';
}
else {
  echo '0 results';
}




?>
</body></html>
