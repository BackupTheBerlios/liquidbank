<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>TwitBank</title>
	</title>
	<link rel="stylesheet" type="text/css" href="twitbank.css" />
</head>

<body>

<div id="page">
<?php if ( strlen( '[[CURRENCY]]' ) == 0 ) { ?>
<h1>TwitBank (Setup)</h1>
<?php } else { ?>
<h1><a href="index.php">TwitBank</a> (for <span class="currencyname">[[CURRENCY]]</span>)</h1>
<?php } ?>
