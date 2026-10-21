# last.fm-stats
Using this toolset your last.fm scrobbles are exported to mysql and a php webroot is supplied to query it. The main advantage to this is you can merge scrobbles from other sources, query it with the flexibility of mysql instead of using the last.fm web frontend and of course own your own data. In my circumstance, I had 'scrobbles' from a very old Winamp plugin called brain - https://winampheritage.com/plugin/the-brain/125483 - which I wanted to merge into a single lifetime view. Libre fm does allow historic scrobbles but last.fm does not and many player only support last.fm. - The best solution was to write something myself.

## Features
 - Query your scrobbles from mysql!
 - Handling of bad timezone scrobbles
 - Handling of duplicate scrobbles after timezone shift
 - PHP site with queries
 - Streamlit setup - currently only local
 - Artist and track version alias merging
 - Support for manual scrobbles in the `manualplays` table
 - Support for other fixed sources - such as brain winamp - in the `brainplays` table.

## Requirements
 - Python
 - MySQL with timezones added
 - Last.fm account
 - PHP capable web server

## Components
 - A modified version of lastexport.py , a script that exports your scrobbles to a mysql DB. My fork adds incremental support.
 - Import scrobbles - a wrapper for the above
 - PHP webroot to query the mysql DB
 - Streamlit dashboards
 - Helm chart for installation of kubernetes

## Incorrect timezone handling
Historically some apps have scrobbled as the local timezone and not as UTC, this caused errors in the date time being displayed, when this got 'fixed' the datetime changed and as this was the key field there were duplicates. In the new DB scema the `lastfm_epoch` is stored so there can never be duplicates again. For the bad timezone scrobbles their incorrect timezone can be stated in the `tz` field in the form found in /usr/share/zoneinfo along with setting `epoch_semantics=bad-local` in the import script or as an update. The views will then handle the timezone conversion.

## Timezone shift
Historically some users have reported that when moving abroad  - or to a different timezone - there are issues with apps handling it incorrectly. As the key field is always the `lastfm_epoch` reported by the last.fm api these now should never happen.

## MySQL timezones
The `CONVERT_TZ()` function in the views requures time zone data, stored in /usr/share/zoneinfo

`mysql_tzinfo_to_sql /usr/share/zoneinfo >timezones.sql` will create a dump that can be added as root on the `mysql` db. If this has issues with some weird/unrequired timezones they can be added individually:

```
mysql_tzinfo_to_sql /usr/share/zoneinfo/Europe/London Europe/London >mysql-tz-london.sql
mysql_tzinfo_to_sql /usr/share/zoneinfo/Asia/Ho_Chi_Minh Asia/Ho_Chi_Minh >mysql-tz-saigon.sql
mysql_tzinfo_to_sql /usr/share/zoneinfo/Asia/Shanghai Asia/Shanghai >mysql-tz-shanghai.sql
mysql_tzinfo_to_sql /usr/share/zoneinfo/UTC UTC >mysql-tz-utc.sql
```

## Streamlit
A basic local-only setup of streamlit pages is available, configure the DB credentials in `.streamlit/secrets.toml` and start it by running `streamlit run app.py` from the repo root dir.

## Artist/track version/alias merging
last.fm does not allow the merging of versons/aliases of songs or indeed artists. Consider the following scrobbles:

```
2011-05-16 23:12:29	Queen	Somebody to Love
2012-10-14 04:10:24	Queen	Somebody To Love - 2011 Remaster
2013-01-14 20:12:26	Queen	Somebody To Love - 2011 Remaster
2013-02-09 15:12:50	Queen	Somebody to Love
2013-02-24 04:33:10	Queen	Somebody to Love
2013-03-05 21:00:56	Queen	Somebody To Love - Live at the Bowl
```

These should all be listed as 'Somebody to Love' as they are versions of the same song. Artists also sometimes have aliases due to different languages and/or bad ID3 tags.

To add track aliases:
```
insert into trackversions (oldtrack,newtrack) values('Somebody To Love - 2011 Remaster','Somebody to Love');
```
To add an artist version due to multiple languages of their name for instance:
```
insert into artistersions (oldartist,newartist) values("걸스데이","Girl's Day");
```

## DB Schema
### brainplays
Files that were originally played with the brain winamp plugim, not sourced from scrobbles:

create table musicstatslive.brainplays ( `dt` DATETIME NOT NULL, `artist` VARCHAR(100) NOT NULL, `track` VARCHAR(100) NOT NULL, `album` varchar(100) DEFAULT NULL,
`trackmbid` varchar(37) DEFAULT NULL,   `artistmbid` varchar(37) DEFAULT NULL,   `albummbid` varchar(37) DEFAULT NULL,
PRIMARY KEY (`dt`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

insert into musicstatslive.brainplays (dt, artist,track,album,trackmbid,artistmbid,albummbid) select dt, artist,track,album,trackmbid,artistmbid,albummbid from musicstats.playsimport where dt <'2013-02-08';

### playsimport
Scrobbles imported from `importscrobbles.sh` and `lastexport.py`

```
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
);
```

### manualplays
Nanually add scrobbles if these were missed:

```
CREATE TABLE `manualplays` (
  `dt` datetime NOT NULL,
  `track` varchar(100) NOT NULL,
  `artist` varchar(100) NOT NULL,
  `album` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`dt`,`track`,`artist`)
);
```

## View Schema

### debugplays
Perform timezone conversions and UNION the 3 scrobble tables

```
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
```

### plays
Add `artistversions` and `trackversions`, this is the view queried by the app.

```
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
```

## Notes
 - There are many many improvements to be made in the code and but feel free to play with it and make suggestions.
