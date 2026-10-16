SET time_zone = '+00:00'; /* Ensure no risk of running in a foreign country and affected the interpreted TZ */
LOAD DATA LOCAL INFILE 'exported_tracks.txt'
IGNORE INTO TABLE playsimport
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
(@epochtime,track,artist,album,trackmbid,artistmbid,albummbid)
SET
    lastfm_epoch = @epochtime,
    lastfm_dt = FROM_UNIXTIME(@epochtime),
    tz = 'Europe/London',
    epoch_semantics = 'utc';

truncate mostplayedrankings;
insert into mostplayedrankings (plays, track, artist) select count(*) as count, ifnull(newtrack,track) as track , artist from plays p left outer join trackversions t on p.track=t.oldtrack group by 2, 3 order by 1 desc;

truncate mostplayedrankingslast12mths;
insert into mostplayedrankingslast12mths (plays, track, artist) select count(*) as count, ifnull(newtrack,track) as track , artist from plays p left outer join trackversions t on p.track=t.oldtrack where dt>=(CURRENT_DATE - INTERVAL 12 MONTH) group by 2, 3 order by 1 desc;
