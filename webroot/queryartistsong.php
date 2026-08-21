<?php
if( !defined( 'MUSICSTATS' ) ) {
        echo "This file is part of MusicStats and is not a valid entry point\n";
        die( 1 );
}

function queryartistsong($query,$rankingformat = true,$dateformat = false)
{
		include 'dbconnect.php';
        $start = microtime(true);
        $result = $connect->query($query);
        $queryTime = microtime(true) - $start;
        $rank=0;
        if ($result->num_rows > 0) {
          echo '<table><tr>';
          while($row = $result->fetch_assoc()) {
            $rank++;
            if($rankingformat == true) { echo '<td>'. $rank . '</td>'; }
            if($dateformat == true) { echo '<td>'. $row['dt'] . '</td>'; }
            if(isset($row['count'])) { echo '<td>' . $row['count']. '</td>'; }
            if (isset($row['artist'])) {
              echo '<td><a href="artist.php?artist='. urlencode($row['artist']). '">' . $row['artist'] . '</a></td>';
            }
            if (isset($row['track'])) {
              echo '<td><a href="track.php?artist=' . urlencode($row['artist'])
                  . '&track=' . urlencode($row['track']) . '">' . $row['track'] . '</a></td>';
            }
            echo '</tr>';

          }
          echo '</table>';
          $totalTime = microtime(true) - $start;
          printf("Query: %.4f sec, total: %.4f sec\n", $queryTime, $totalTime);
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
