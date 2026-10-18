<?php
if( !defined( 'MUSICSTATS' ) ) {
        echo "This file is part of MusicStats and is not a valid entry point\n";
        die( 1 );
}


$connect = new mysqli("localhost", "musicstats", "passwordhere", "musicstats");
if (!$connect->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $connect->error);
}

if (!function_exists('musicstats_query')) {
function musicstats_query(mysqli $connect, string $sql, array $params = []): mysqli_result
{
        $statement = $connect->prepare($sql);
        if ($statement === false) {
                throw new RuntimeException('Could not prepare database statement: ' . $connect->error);
        }

        $statement->execute($params);
        $result = $statement->get_result();
        $statement->close();
        return $result;
}
}
?>
