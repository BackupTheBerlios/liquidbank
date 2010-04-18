<?php

require_once("lib/Currency.class.php");

try {
	$currency = new Currency( $_GET["currency"] );
} catch ( Exception $exception ) {
	die($exception->getMessage());
}

try {
	$dbh = new PDO("sqlite:".$currency->db);
} catch( PDOException $exception ) {
	die($exception->getMessage());
}

$query = 'SELECT count(id) as count FROM tb_user;';
$result = $dbh->query($query);
$row = $result->fetch();

$info = array();
$info["currency"] = $currency;
$info["user_count"] = $row["count"];

?>
<html>
<head>
</head>
<body>
<p>Currency : <?php print $currency->name; ?></p>
<p>Total users : <?php print $info["user_count"]; ?></p>
</body>
</html>
