<?php

// dessine le graphe des transactions entre utilisateurs
// echo "SELECT user_src,user_dst,sum(amount) FROM tb_transaction GROUP BY user_src,user_dst;" | sqlite3 twitbank.sqlite3
//

require_once( "common/config.php" );
require_once( "common/db.php" );

global $dbh;
global $config;


$global_graph = "cache/graph-global.svgz";
$global_map = "cache/graph-global.map";

$update = false;
$query = "SELECT * from tb_config WHERE label='update_stamp' OR label='graph_stamp' ORDER BY value ASC;";
$result = $dbh->query( $query );
if ( $result ) {
	$row = $result->fetch();
	$update = ( $row['label'] == 'graph_stamp' );
}
if ( !file_exists( $global_graph ) ){
	$update = true;
}

if ( $update ) {
	$users = array();
	$query = "SELECT id FROM tb_user";
	$result = $dbh->query( $query );
	if ( $result ) {
		while ( $row = $result->fetch() ) {
			array_push( $users, $row['id'] );
		}
	}

	#$query = "SELECT count(*) as cnt,max(id) as max_id, user_src,user_dst,sum(amount) as amount_sum FROM tb_transaction GROUP BY user_src, user_dst ORDER BY max_id DESC LIMIT 0,100";
	
	#$query = "SELECT count(*) as cnt,user_src,user_dst,sum(amount) as amount_sum FROM ( SELECT * FROM tb_transaction ORDER BY id DESC LIMIT 0,50 ) GROUP BY user_src,user_dst";
	$query = "SELECT count(*) as cnt,user_src,user_dst,sum(amount) as amount_sum FROM tb_transaction GROUP BY user_src,user_dst";
	$result = $dbh->query( $query );
	if ( $result ) {
		$tmpfname = tempnam("/tmp", "twitbank2");

		$fd = fopen( $tmpfname, "w" );
		fwrite($fd, "digraph tb_transaction {\n");
		fwrite($fd, "overlap=scale\n");
		//fwrite($fd, "overlap=false\n");
		fwrite($fd, "splines=true\n");
		//fwrite($fd, "rotate=90\n");
		//fwrite($fd, "rankdir=BT\n");
		//fwrite($fd, "ratio=0.66\n");
		fwrite($fd, "concentrate=true\n");
		fwrite($fd, "packMode=clust\n");
		/*
		   foreach( $users as $user ) {
		   fwrite($fd, "  $user [ 
		   }*/


		fwrite($fd, "edge [color=brown]\n");
		fwrite($fd, "node [style=filled, shape=note, fillcolor=darkseagreen2, color=darkgreen]\n");
		while ( $row = $result->fetch() ) {
			if ($row['cnt'] > 1) {
				fwrite($fd, "  \"{$row['user_src']}\" -> \"{$row['user_dst']}\" [label={$row['amount_sum']}]\n" );
			}
		}
		fwrite($fd, "}\n");
		fclose($fd);
		//system("fdp -Tpng $tmpfname > $tmpfname.2");
		// echo "running ?";
		#system("dot -Gsize=\"8,6\" -Tpng $tmpfname > $tmpfname.2");
		system("nice -n10 dot -Gsize=\"8,6\" -Tsvgz $tmpfname > $tmpfname.2");
		//system("twopi -Tpng $tmpfname > $tmpfname.2");
		//system("neato -Tpng $tmpfname > $tmpfname.2");
		rename( "$tmpfname.2", $global_graph );
		unlink( "$tmpfname" );
	}

	$query = "UPDATE tb_config SET value = current_timestamp WHERE label = 'graph_stamp'";
	$stmt = $dbh->prepare( $query );
	$stmt->execute();
}

header( "Location: $global_graph" );

?>
