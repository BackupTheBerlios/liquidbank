<?php

require_once( "common/config.php" );
require_once( "common/db.php" );
require_once( 'common/Template.class.php' );
//require_once( "common/User.class.php" );

$tpl = new Template( 'templates/header.php' );
$tpl->bind('CURRENCY', $config['currency'] );
$tpl->publish();
// require_once( "templates/logbox.php" );

?>

<h3>About TwitBank</h3>
<p>TwitBank is a free and opensource currency manager inspired by <a
href="http://twollars.com">Twollars</a>.</p>

<p>
<!-- 
<a href="update.php" >Update transactions</a>
<a href="update-graph.php" >Update transactions graph</a>
!-->
<a href="user.php" class="link2users">All users</a>
<a href="graph.php" class="link2graph">All transactions</a>
</p>

<h3>Latest transactions</h3>
<?php
$tpl = new Template('templates/transaction-all.php');
//	$tpl->bind( "USERNAME", $user->name );
$tpl->publish();
?>
<h3>More to come</h3>

<p>
To stay tuned about updates and roadmap, or if you want to contribute, join <a href="http://groups.google.com/group/twitbank-dev">TwitBank's mailing list</a> or have a look 
at <a href="http://wiki.glenux.net/TwitBank">TwitBank's official wiki page</a>!
</p>

<h3>Getting the sources</h3>
<p>You can download the source code easily:<p>
<p class="code" style="text-align:center;">
svn checkout https://websvn.glenux.net/svn/Upoc/twitbank/branches/0.1
</p>
<p>(login: anonymous)</p>

<?php
require_once( "templates/footer.php" );

?>
