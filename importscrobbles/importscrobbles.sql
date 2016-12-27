load data infile '/usr/local/importscrobbles/exported_tracks.txt' ignore into table playsimport fields terminated by '\t' lines terminated by '\n' (@epochtime,track,artist,album,trackmbid,artistmbid,albummbid) set dt = FROM_UNIXTIME( @epochtime );
truncate mostplayedrankings;
insert into mostplayedrankings (plays, track, artist) select count(1), track, artist from plays group by 2, 3 order by 1 desc;

/*insert into mostplayedrankings (plays, track, artist) select count(*) as count, ifnull(newtrack,track) as track , artist from plays p left outer join trackversions t on p.track=t.oldtrack group by 2, 3 order by 1 desc;
*/

truncate mostplayedrankingslast12mths;
insert into mostplayedrankingslast12mths (plays, track, artist) select count(1), track, artist from plays where dt>=(CURRENT_DATE - INTERVAL 12 MONTH) group by 2, 3 order by 1 desc;    
/* insert into mostplayedrankingslast12mths (plays, track, artist) select count(*) as count, ifnull(newtrack,track) as track , artist from plays p left outer join trackversions t on p.track=t.oldtrack where dt>=(CURRENT_DATE - INTERVAL 12 MONTH);
*/
