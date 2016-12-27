# last.fm-stats
Super charge your last.fm statistics!

Using this tool-set additonal interesting statistics are available from your last.fm scrobbles.

## Requirements
 - Python
 - MySQL
 - Last.fm account
 - PHP capable web server

## Components
 - A modified version of lastexport.py , a script that exports your scrobbles to a mysql DB. My fork adds incremental support.
 - Import scrobbles - a wrapper for the above
 - PHP webroot to query the mysql DB


## Notes
 - There are many many improvements to be made in the code and but feel free to play with it and make suggestions.
