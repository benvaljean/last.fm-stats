<?php
if (!defined('MUSICSTATS')) {
    echo "This file is part of MusicStats and is not a valid entry point\n";
    die(1);
}

$host = getenv('MUSICSTATS_DB_HOST') ?: 'localhost';
$port = (int) (getenv('MUSICSTATS_DB_PORT') ?: 3306);
$user = getenv('MUSICSTATS_DB_USER') ?: 'musicstats';
$password = getenv('MUSICSTATS_DB_PASSWORD') ?: '';
$database = getenv('MUSICSTATS_DB_NAME') ?: 'musicstats';

$connect = new mysqli($host, $user, $password, $database, $port);
if ($connect->connect_error) {
    die('Database connection failed: ' . $connect->connect_error);
}

if (!$connect->set_charset('utf8')) {
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
