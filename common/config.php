<?php

error_reporting(E_ALL); 
global $config;

$config['localpath'] = dirname( __FILE__ ) . "/.." ;
$config['currency'] = 'exploracoeur';
$config['initial_balance'] = 200;
$config['database'] = "sqlite:{$config['localpath']}/twitbank.sqlite3";
?>
