#!/bin/sh

set -u

ROOTDIR=`dirname $0`/..
SETUPDIR=$ROOTDIR/setup
DB=$ROOTDIR/twitbank.sqlite3
URL=http://twitbank.glenux.net/twitbank.sqlite3

if [ -f ${DB} ]; then mv -f ${DB} ${DB}.old ; fi
wget $URL -O $DB

chmod 666 ${DB}
chmod 777 $ROOTDIR
chmod 777 $ROOTDIR/cache

## clear cache
sqlite3 ${DB} <<EOF
UPDATE tb_config SET value = 0 WHERE label = 'latest_twit_id';
UPDATE tb_config SET value = current_timestamp WHERE label = 'update_stamp';
UPDATE tb_config SET value = current_timestamp WHERE label = 'graph_stamp';
EOF

exit 0
