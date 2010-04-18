<?php

// dessine le graphe des transactions entre utilisateurs
// echo "SELECT user_src,user_dst,sum(amount) FROM tb_transaction GROUP BY user_src,user_dst;" | sqlite3 twitbank.sqlite3

require_once( '../common/config.php' );
require_once( '../common/db.php' );
require_once( '../common/User.class.php' );



global $dbh;
global $config;


if ( isset( $_GET["id"] ) ){ $id = $_GET["id"]; } else { $id = null ; }
$user = new User( $id );
$user_sent = 0;
if ( !$user->isValid() ) {
	die("Invalid username" . $user->error);
}

$user_graph =  "../cache/user-web-{$user->name}.svgz";
$user_map = "../cache/user-web-{$user->name}.map";
$usercolor="darkgreen";
$userfillcolor="darkseagreen2";

$update = false;
if ( !file_exists( $user_graph ) ) { $update = true; }
if ( !file_exists( $user_map ) ) { $update = true; }

$query = "SELECT count(*) as cnt FROM tb_user "
	. "WHERE id = '{$user->name}' AND update_stamp > graph_stamp";
$result = $dbh->query( $query );
if ( $result ) {
	$row = $result->fetch();
	$update = ( $row['cnt'] > 0 );
}

// force when cache is missing !
if ( !file_exists( $user_graph ) ) { $update = true; }
if ( !file_exists( $user_map ) ) { $update = true; }

if ( $update ){
	/* do upgrade graph */
	$query = "SELECT user_src,user_dst,sum(amount) as amount_sum "
		. "FROM tb_transaction "
		. "WHERE user_src = '{$user->name}' OR user_dst = '{$user->name}' "
		. "GROUP BY user_src,user_dst";

	$result = $dbh->query( $query );
	if ( $result ) {
		$tmpfname = tempnam("/tmp", "twitbank2");

		$fd = fopen( $tmpfname, "w" );
		fwrite($fd, "digraph tb_transaction {\n");
		fwrite($fd, "overlap=scale\n");
		// fwrite($fd, "overlap=false\n");
		fwrite($fd, "splines=true\n");
		// fwrite($fd, "rotate=90\n");
		// fwrite($fd, "rankdir=BT\n");
		// fwrite($fd, "ratio=0.66\n");
		// fwrite($fd, "concentrate=true\n");
		// fwrite($fd, "packMode=clust\n");


		fwrite($fd, "edge [color=brown]\n");
		fwrite($fd, "node [style=filled, shape=note, fillcolor=$userfillcolor, color=$usercolor]\n");
		fwrite($fd, "\"{$user->name}\" [URL=\"user?id={$user->name}\"]\n" );
		while ( $row = $result->fetch() ) {
			$color="blue";
			if ( $row['user_src'] != $user->name ) {
				$color="blue";
				fwrite($fd, "\"{$row['user_src']}\" [URL=\"user?id={$row['user_src']}\", fillcolor=$userfillcolor], color=$usercolor\n" );
			}
			if ( $row['user_dst'] != $user->name ) {
				$color="brown";
				fwrite($fd, "\"{$row['user_dst']}\" [URL=\"user?id={$row['user_dst']}\", fillcolor=$userfillcolor], color=$usercolor\n" );
			}
			fwrite($fd, "  \"{$row['user_src']}\" -> \"{$row['user_dst']}\" [label={$row['amount_sum']}, fontcolor={$color},color={$color}]\n" );
		}
		fwrite($fd, "}\n");
		fclose($fd);
		system("nice -n10 fdp -Tsvgz -o$tmpfname.svgz $tmpfname");

		unlink("$tmpfname");
		rename("$tmpfname.svgz", $user_graph );
	}

	$query = "UPDATE tb_user SET graph_stamp = current_timestamp WHERE id = '{$user->name}'";
	$dbh->exec( $query );
}

header( "Location: $user_graph" );

?>
