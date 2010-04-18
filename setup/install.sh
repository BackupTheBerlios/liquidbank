#!/bin/sh

set -u

ROOTDIR=`dirname $0`/..
SETUPDIR=$ROOTDIR/setup
DB=$ROOTDIR/twitbank.sqlite3

if [ -f ${DB} ]; then mv -f ${DB} ${DB}.old ; fi
cat $SETUPDIR/create.sql | sqlite3 ${DB}
chmod 666 ${DB}
chmod 777 $ROOTDIR
chmod 777 $ROOTDIR/cache

exit 0
