#!/bin/bash

cd /usr/local/importscrobbles
rm exported_tracks.txt
python lastexport.py --user $1
mysql --local-infile=1 -u root musicstats < importscrobbles.sql
