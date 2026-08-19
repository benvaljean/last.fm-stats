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
 - *Future* use streamlit

## DB Schema
### brainplays
Files that were originally played with the brain winamp plugim, not sourced from scrobbles:

create table musicstatslive.brainplays ( `dt` DATETIME NOT NULL, `artist` VARCHAR(100) NOT NULL, `track` VARCHAR(100) NOT NULL, `album` varchar(100) DEFAULT NULL,
`trackmbid` varchar(37) DEFAULT NULL,   `artistmbid` varchar(37) DEFAULT NULL,   `albummbid` varchar(37) DEFAULT NULL,
PRIMARY KEY (`dt`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

insert into musicstatslive.brainplays (dt, artist,track,album,trackmbid,artistmbid,albummbid) select dt, artist,track,album,trackmbid,artistmbid,albummbid from musicstats.playsimport where dt <'2013-02-08';

### playsimport
Scrobbles imported from `importscrobbles.sh` and `lastexport.py`

CREATE TABLE `playsimport` (
  `lastfm_epoch` bigint unsigned NOT NULL,
  `lastfm_dt` datetime NOT NULL,
  `tz` enum('Europe/London','Asia/Shanghai','Asia/Ho_Chi_Minh','Asia/Hong_Kong') NOT NULL,
  `epoch_semantics` enum('utc','bad-local') NOT NULL,
  `track` varchar(100) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `album` varchar(100) DEFAULT NULL,
  `trackmbid` varchar(37) DEFAULT NULL,
  `artistmbid` varchar(37) DEFAULT NULL,
  `albummbid` varchar(37) DEFAULT NULL,
  PRIMARY KEY (`lastfm_epoch`);

### manualplays
Nanually add scrobbles if these were missed:

CREATE TABLE `manualplays` (
  `dt` datetime NOT NULL,
  `track` varchar(100) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `album` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`dt`,`track`,`artist`)
);

## View Schema

### debugplays
Perform timezone conversions and UNION the 3 sorcde tables

CREATE OR REPLACE VIEW debugplays AS

SELECT
	p.lastfm_epoch,
	p.tz,
	p.epoch_semantics,
	p.lastfm_dt,
	CASE
        WHEN p.epoch_semantics = 'bad-local' THEN p.lastfm_dt
        ELSE CONVERT_TZ(p.lastfm_dt, 'UTC', p.tz)
    END AS dt,
    p.artist,
    p.track,
    p.album,
    p.trackmbid,
    p.artistmbid,
    p.albummbid,
    'lastfm' AS source
FROM playsimport p

UNION ALL

SELECT
	NULL as lastfm_epoch,
	NULL as tz,
	NULL as epoch_semantics,
	NULL AS lastfm_dt,
    b.dt,
    b.artist,
    b.track,
    b.album,
    b.trackmbid,
    b.artistmbid,
    b.albummbid,
    'brain' AS source
FROM brainplays b

UNION ALL

select
	NULL as lastfm_epoch,
	NULL as tz,
	NULL as epoch_semantics,
	NULL AS lastfm_dt,
	m.dt,
	m.artist,
	m.track,
	NULL as album,
	NULL as trackmbid,
	null as artistmbid,
	null as albummbid,
	'manual' as source
FROM manualplays m;


### plays
Will be used by streamlit

CREATE OR REPLACE VIEW plays AS
SELECT
    p.dt,
    p.tz,
    p.artist AS oldartist,
    IFNULL(a.newartist, p.artist) AS artist,
    p.track AS oldtrack,
    IFNULL(t.newtrack, p.track) AS track,
    p.album,
    p.trackmbid,
    p.artistmbid,
    p.albummbid,
    p.source
FROM debugplays p
LEFT JOIN trackversions t
    ON p.track = t.oldtrack
LEFT JOIN artistversions a
    ON p.artist = a.oldartist
ORDER BY p.dt asc;

## Notes
 - There are many many improvements to be made in the code and but feel free to play with it and make suggestions.
