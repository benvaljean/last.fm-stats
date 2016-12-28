<?php
if( !defined( 'MUSICSTATS' ) ) {
        echo "This file is part of MusicStats and is not a valid entry point\n";
        die( 1 );
}
$header = <<<END
<html><head>
<title>Music Stats</title>
<link rel="stylesheet" href="musicstats.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8">
</head>
<body>
<center><h1>Music Stats</h1>
<a href="/musicstats"><img class="upperleft" height="25" width="25" src="http://icons.iconarchive.com/icons/double-j-design/origami-colored-pencil/128/blue-home-icon.png">
<a/>
<form action="search.php">
<input class="upperright" type="text" name="q" placeholder="Search..">
</form>
END;
echo $header;
?>
