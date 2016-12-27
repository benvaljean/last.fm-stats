<?php
define( 'MUSICSTATS', true );
?>
<html><head>
<title>MP3Stats</title>
<link rel="stylesheet" href="/musicstats.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8">
</head>
<body>
<center><h1>Mp3 Stats</h1>

<h2>Most-played songs last 12 months</h2>

<?php
include 'queryartistsong.php';
queryartistsong("select count(*) as count, artist, track from plays where dt>=(CURRENT_DATE - INTERVAL 12 MONTH) group by 2, 3 order by 1 desc limit 100")
?>

<h2>Most played artists last 12 months</h2>
<table>
<?php
include 'dbconnect.php';
$query = "select count(*) as count, artist from plays where dt >=(CURRENT_DATE - INTERVAL 12 MONTH) group by 2 order by 1 desc limit 50";

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
