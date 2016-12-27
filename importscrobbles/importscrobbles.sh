#!/bin/bash

cd /usr/local/importscrobbles
rm exported_tracks.txt
python lastexport.py --user $1
mysql -u root musicstats < importscrobbles.sql
