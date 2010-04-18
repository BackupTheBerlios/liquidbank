<?php

require( 'common/Template.class.php' );

$tpl = new Template( 'templates/header.php' );
$tpl->bind('CURRENCY', '');
$tpl->publish();

/* test directory / file permissions */

print "<p>";

print "Testing 'cache' directory permissions... ";
if ( is_writable( "cache" ) ){
	print "<span class='msg_ok'>ok</span>";
} else {
	print "<span class='msg_error'>error: directory should be writable!</span>";
}
print "<br />";

print "Testing '.' directory permissions... ";
if ( is_writable( "." ) ) {
	print "<span class='msg_ok'>ok</span>";
} else {
	print "<span class='msg_error'>error: directory should be writable!</span>";
}
print "<br/>";


$tpl = new Template( 'templates/footer.php' );
$tpl->publish();

?>
