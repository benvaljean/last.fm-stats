#!/bin/sh
set -eu

# The positional argument keeps the pre-container invocation compatible.
LASTFM_USER="${LASTFM_USER:-${1:-}}"
: "${LASTFM_USER:?LASTFM_USER must be set (or pass it as the first argument)}"
: "${MUSICSTATS_DB_HOST:?MUSICSTATS_DB_HOST must be set}"
: "${MUSICSTATS_DB_PORT:?MUSICSTATS_DB_PORT must be set}"
: "${MUSICSTATS_DB_NAME:?MUSICSTATS_DB_NAME must be set}"
: "${MUSICSTATS_DB_USER:?MUSICSTATS_DB_USER must be set}"
: "${MUSICSTATS_DB_PASSWORD:?MUSICSTATS_DB_PASSWORD must be set}"

export MYSQL_PWD="$MUSICSTATS_DB_PASSWORD"
mysql_args="--protocol=TCP --host=$MUSICSTATS_DB_HOST --port=$MUSICSTATS_DB_PORT --user=$MUSICSTATS_DB_USER $MUSICSTATS_DB_NAME"
initial_from_epoch="${MUSICSTATS_INITIAL_FROM_EPOCH:-0}"

log() {
    printf '%s musicstats-import: %s\n' "$(date -u +'%Y-%m-%dT%H:%M:%SZ')" "$*"
}

case "$initial_from_epoch" in
    ''|*[!0-9]*)
        log "MUSICSTATS_INITIAL_FROM_EPOCH must be a non-negative Unix epoch"
        exit 2
        ;;
esac

last_epoch="$(mysql $mysql_args --skip-column-names --batch --execute \
    "SELECT COALESCE(MAX(lastfm_epoch), $initial_from_epoch) FROM playsimport;")"
from_epoch=$((last_epoch - 86400))
if [ "$from_epoch" -lt 0 ]; then
    from_epoch=0
fi

cd /work
rm -f exported_tracks.txt
log "Exporting user $LASTFM_USER from epoch $from_epoch (database maximum: $last_epoch)"
python /app/lastexport.py --user "$LASTFM_USER" --from-epoch "$from_epoch" --outfile exported_tracks.txt
log "Loading exported tracks into $MUSICSTATS_DB_NAME"
mysql --local-infile=1 $mysql_args < /app/importscrobbles.sql
log "Import completed successfully"
