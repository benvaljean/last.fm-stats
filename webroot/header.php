<?php
if( !defined( 'MUSICSTATS' ) ) {
        echo "This file is part of MusicStats and is not a valid entry point\n";
        die( 1 );
}
$header = <<<END
<html><head>
<title>Music Stats</title>
<link rel="stylesheet" href="/musicstats.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8">
</head>
<body>
<center><h1>Music Stats</h1>
END;
echo $header;
?>
