<?php

global $config;

try {
	$dbh = new PDO( $config["database" ] );
} catch( PDOException $exception ) {
	die( $exception->getMessage() );
}

?>
