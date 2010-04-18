<?php

global $dbh;
global $config;

$query = "SELECT * FROM tb_transaction "
	. "WHERE user_src = '[[USERNAME]]' OR user_dst = '[[USERNAME]]' "
	. "ORDER BY id DESC "
	. "LIMIT 0,50";

$result = $dbh->query( $query );
if ( $result ) {
	print "<ul>";
	while ( $row = $result->fetch() ) {
		print "<li class='transaction'>";
		print "<p class='summary'>";

		if ( $row['user_src' ] == '[[USERNAME]]' ) {
			$him = $row['user_src'];
			$other = $row['user_dst'];
			$color = 'red';
			$action = 'gave';
			$actionword = 'to';
		} else {
			$him = $row['user_dst'];
			$other = $row['user_src'];
			$color = 'blue';
			$action = 'received';
			$actionword = 'from';
		}

		print "<a href='user.php?id=$him' class='username'>$him</a> ";
		print "<span class='action_$action'>$action</span> ";
		print "<span class='amount_$action'>{$row['amount']} {$config['currency']}</span> ";
		print "<span class='txt'>$actionword</span> ";
		print "<a href='user.php?id=$other' class='username'>$other</a> ";
		print "</p>";

		$title = preg_replace("/^'(.*)'$/", "$1", $row['title']);
		print "<p class='title'><b>&lt;{$row['user_src']}&gt;:</b> $title</p>";
		print "</li>";
	}
	print "</ul>";
}

?>
