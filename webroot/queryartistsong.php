<?php
if( !defined( 'MUSICSTATS' ) ) {
        echo "This file is part of MusicStats and is not a valid entry point\n";
        die( 1 );
}


function queryartistsong($query)
{
	include 'dbconnect.php';
        $result = $connect->query($query);

        // if the $result contains at least one row
        $rank=0;
        if ($result->num_rows > 0) {
          // output data of each row from $result
          echo '<table>';
          while($row = $result->fetch_assoc()) {
            $rank++;
            //echo '<tr><td>'. $rank . '</td><td>' . $row['count']. '</td><td>'. $row['artist']. '</td><td>'. $row['track'] . '</td></tr>';

        echo '<tr><td>'. $rank . '</td><td>' . $row['count']. '</td><td><a href="artist.php?artist=' . $row['artist']. '">' . $row['artist'] . '</a></td><td><a href="track.php?artist=' . urlencode($row['artist']) . '&track=' . urlencode($row['track']) . '">' . $row['track'] . '</a></td></tr>';


          }
          echo '</table>';
        }
        else {
          echo '0 results';
        }
}


?>
