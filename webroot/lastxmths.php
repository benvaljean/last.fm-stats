<?php
define( 'MUSICSTATS', true );
include 'queryartistsong.php';
include 'dbconnect.php';
include 'header.php';
$months=filter_input(INPUT_GET, 'months', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
?>
<h2>Most-played songs last <?php echo $months; ?> months</h2>
<?php queryartistsong('SELECT COUNT(*) AS count, artist, track FROM plays WHERE dt >= (CURRENT_DATE - INTERVAL ? MONTH) GROUP BY 2, 3 ORDER BY 1 DESC LIMIT 100', [$months]) ?>

<h2>Most played artists last <?php echo $months; ?> months</h2>
<?php queryartistsong('SELECT COUNT(*) AS count, artist FROM plays WHERE dt >= (CURRENT_DATE - INTERVAL ? MONTH) GROUP BY 2 ORDER BY 1 DESC LIMIT 50', [$months]); ?>
</html>
