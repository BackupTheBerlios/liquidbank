<?php

require_once( "common/config.php" );
// require_once( "templates/logbox.php" );
require( 'common/Template.class.php' );

$tpl = new Template( 'templates/header.php' );
$tpl->bind('CURRENCY', $config['currency'] );
$tpl->publish();

?>

<h3>Latest transactions graph</h3>
<p>
<!-- <a href="graph-global.php" style="background:none;" target="_blank" > -->
<!-- <img src="graph-global.php"  style="width: 100%;"/> -->

<object data="graph-global.php" type="image/svg+xml"
        width="100%" >
    <embed src="graph-global.php" type="image/svg+xml"
            width="100%"  />
</object>
<!-- </a> -->
<!-- <i>oOps... rendering error: THE GLOBAL GRAPH IS TOO BIG!</i>
-->
</p>

<?php 
require_once( "templates/footer.php" );

?>
