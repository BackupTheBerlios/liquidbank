
set -u

ROOTDIR=`dirname $0`/..
SETUPDIR=$ROOTDIR/setup
DB=$ROOTDIR/twitbank.sqlite3
STAMP=`date +%Y-%m-%d_%H%M`

DUMP=$ROOTDIR/twitbank.sql

sqlite3 "$DB" <<EOF
.output "$DUMP"
.dump
EOF

mv "$DB" "$DB.bak.$STAMP"

# create as new !
$SETUPDIR/install.sh

# reload, removing create statements
sed -e '/^CREATE.*;$/d' -e '/^CREATE.*($/,/;$/d' "$DUMP" | sqlite3 "$DB"

#rm -f "$DUMP"

