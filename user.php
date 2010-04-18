<?php

require_once( "common/config.php" );
require_once( "common/db.php" );
require_once( "common/Template.class.php" );
require_once( "common/User.class.php" );

$tpl = new Template( 'templates/header.php' );
$tpl->bind('CURRENCY', $config['currency'] );
$tpl->publish();

if ( isset( $_GET["id"] ) ){ $id = $_GET["id"]; } else { $id = null ; }
$user = new User( $id );

$user_sent = 0;
if ( $user->isValid() && $user->load() ) {
	$sent = $user->outcomes();
	$recv = $user->incomes();
	$balance = $config['initial_balance'] + $user->balance();

	print "<p>";
	print "<a href='user.php' class='link2users'>All users</a> ";
	print "</p>";

	print "<h2>Stats for {$user->screen_name}";
    print (strtolower($user->name)!=strtolower($user->screen_name))?" ({$user->name})" : "";
	print "</h2>";
	print "<p class='user'>";
	print "<a href='http://twitter.com/{$user->name}' class='twitterlink'>{$user->name}'s on twitter</a>";

	print "<img src='{$user->profile_image_url}' class='avatar'/>";
	print "<b>Location:</b> {$user->location}<br/>";
	print "<b>Bio:</b> {$user->description}<br/>";
	print "</p>";
	print "<h3 style='clear:both;'>Account</h3>";
	print "<p>";
	//print "<img src='graph/user-balance?id={$user->name}' style='border: none; max-width: 100%; float:right;' />\n";
	printf( "%ss sent : %01.2f<br/>\n", $config['currency'], $sent );
	printf( "%ss received : %01.2f<br/>\n", $config['currency'], $recv );
	printf( "%ss balance : %01.2f<br/>\n", $config['currency'], $balance ) ;
	print "</p>";
	
	print "<h3>Transaction graph (latest 50 only)</h3>";
	print "<p style='text-align:center;'>\n";
	#print "<a href='graph/user-web?id={$user->name}' style='background:none;'>";
	#print "<img src='graph/user-web?id={$user->name}' style='border: none; max-width: 100%;' />\n";
print <<<EOF
<object data="graph/user-web?id={$user->name}" type="image/svgz+xml"
        style="max-width: 100%;" >
    <embed src="graph/user-web?id={$user->name}" type="image/svgz+xml"
		style="max-width: 100%;"  />
</object>
EOF;
	print "</a>\n";
	print "</p>";

	print "<h3>Transaction history (latest 50 only)</h3>";
	$tpl = new Template('templates/transaction-user.php');
	$tpl->bind( "USERNAME", $user->name );
	$tpl->publish();
} else {
	if ( $user->error ) {
		print "<p class='error'>ERROR: {$user->error}</p>";
	}

	$usercount = 0;
	$query = "SELECT count(*) as ct FROM tb_user ";
	$result = $dbh->query( $query );
	if ( $result ) {
		$row = $result->fetch();
		$usercount = $row['ct'];
	}
	print "<h3>$usercount participating users</h3>";

	$query = "SELECT id, registered FROM tb_user ORDER BY id ASC ";
	$result = $dbh->query( $query );
	if ( $result ) {
		$letter = null;
		$oldletter = null;
		while ($row = $result->fetch() ) {
			$letter = $row['id'][0];
			if (is_null( $oldletter )) {
				$oldletter == $letter;
			}
			if ($oldletter != $letter) {
				print "<h4>" . strtoupper( $letter ) . "...</h4>";
			} 
			print "<a href='user.php?id={$row['id']}' class='username'>{$row['id']}</a>";
			if ($row['registered']) {
				print "(registered)";
			}
			print " ";
			$oldletter = $letter;
		}
	}
}


//print_r($tpl);

/* 
 * download transactions (CSV, TXT, etc.)
 * download transaction graph
 * 
 * twitter avatar
 * twitter description
 *
 * twitter join date
 * currency join date
 *
 * currency sent
 * currency received
 * currency balance
 * currency "generosity ranking"
 *
 * transaction list
 */

require_once( "templates/footer.php" );

?>
