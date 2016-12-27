<html><head>
<title>MP3Stats</title>
<link rel="stylesheet" href="/musicstats.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8">
</head>
<body>
<?php
$track=$_GET['track'];
$artist=$_GET['artist'];

?>
<center>
<h1>Mp3 Stats</h1>
<h2><?php echo $artist . " - " . $track . "</h2>";

$connect = new mysqli("localhost", "musicstats", "4f6hmklFHf54", "musicstats");
if (!$connect->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
}

echo '<table>';

#$query="select count(*) as count from plays where track='$track' and artist='$artist'";
$query="select rank,plays from mostplayedrankings where track='$track' and artist='$artist'";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  // output data of each row from $result
  while($row = $result->fetch_assoc()) {
    echo '<tr><td><b>Total plays: </td><td>' . $row['plays'] . '</b></td></tr>';
    echo '<tr><td>Overall ranking: </td><td>' . $row['rank'] . '</tr></tr>';
#	$plays=$row['count'];
  }
}
else {
  echo '0 results';
}


/* $query="select rank from mostplayedrankings where track='$track' and artist='$artist'";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  // output data of each row from $result
  while($row = $result->fetch_assoc()) {
    echo '<tr><td>Overall ranking:</td><td>' . $row['rank'] . '</td></tr>';
  }
}
else {
  echo '0 results';
}
*/

$query="select min(dt) as first,max(dt) as last from plays where track='$track' and artist='$artist'";
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
/*
$query="select max(dt) as dt from plays where track='$track' and artist='$artist'";
$result = $connect->query($query);
if ($result->num_rows > 0) {
  // output data of each row from $result
  while($row = $result->fetch_assoc()) {
    echo '<tr><td>Latest play:</td><td>' . $row['dt'] . '</td></tr>';
  }
}
else {
  echo '0 results';
}

*/
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
