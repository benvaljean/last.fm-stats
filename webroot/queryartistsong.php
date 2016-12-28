<?php
if( !defined( 'MUSICSTATS' ) ) {
        echo "This file is part of MusicStats and is not a valid entry point\n";
        die( 1 );
}

function queryartistsong($query,$rankingformat = true,$dateformat = false)
{
		include 'dbconnect.php';
        $result = $connect->query($query);
        $rank=0;
        if ($result->num_rows > 0) {
          echo '<table><tr>';
          while($row = $result->fetch_assoc()) {
            $rank++;
            if($rankingformat == true) { echo '<td>'. $rank . '</td>'; }
            if($dateformat == true) { echo '<td>'. $row['dt'] . '</td>'; }
            echo '<td>' . $row['count']. '</td><td><a href="artist.php?artist=' . urlencode($row['artist']). '">' . $row['artist'] . '</a></td><td><a href="track.php?artist=' . urlencode($row['artist']) . '&track=' . urlencode($row['track']) . '">' . $row['track'] . '</a></td></tr>';
          }
          echo '</table>';
        }
        else {
          echo '0 results';
        }
}

function queryscrobbleslatest($scrobbles)
{
	queryartistsong("(SELECT dt,artist,track FROM plays ORDER BY dt DESC LIMIT $scrobbles) ORDER BY dt ASC",false,true);
}

function queryscrobblesrange($start,$end)
{
	queryartistsong("select dt,artist,track from plays where dt >= '$start' and dt <= '$end' order by 1",false,true);
}
?>
