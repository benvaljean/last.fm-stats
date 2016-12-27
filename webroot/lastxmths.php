<?php
define( 'MUSICSTATS', true );
include 'queryartistsong.php';
include 'dbconnect.php';
include 'header.php';
$months=$_GET['months'];
?>
<h2>Most-played songs last <?php echo $months; ?> months</h2>

<?php
queryartistsong("select count(*) as count, artist, track from plays where dt>=(CURRENT_DATE - INTERVAL $months MONTH) group by 2, 3 order by 1 desc limit 100")
?>

<h2>Most played artists last <?php echo $months; ?> months</h2>
<table>
<?php
$query = "select count(*) as count, artist from plays where dt >=(CURRENT_DATE - INTERVAL $months MONTH) group by 2 order by 1 desc limit 50";

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
