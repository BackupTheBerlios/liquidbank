<?php

class User {
	var $error = null;
	var $name = null;
	private $loaded = null;
	private $loaded_vars = array();

	function __construct( $user ) {
		$this->name = $user;
	
	}

	function isValid() {
		global $dbh;
		global $config;

		if ( is_null( $this->name )) {
			return false;			
		}
		if ( ! preg_match('/^[a-zA-Z0-9_]+$/', $this->name) ) {
			$error = "Invalid user name : {$this->name}";
			return false;
		}

		$query = "SELECT * FROM tb_user WHERE id='{$this->name}' ";
		$result = $dbh->query( $query );
		if ( $result ) {
			$row = $result->fetch();
			if ($row) {
				return true;
			} else {
				$this->error = "User {$this->name} never participated to the {$config['currency']} exchange market.";
				return false;
			}
		} else {
			$this->error = "Error while querying the database.";
			return false;
		}
	}

	function balance() {
		$sent = $this->outcomes();
		$recv = $this->incomes();
		$balance = $recv - $sent;
		return $balance;
	}

	function outcomes() {
		global $dbh;

		$query = "SELECT sum(amount) as amount_sent FROM tb_transaction WHERE user_src = '{$this->name}' ";
		$result = $dbh->query( $query );
		if ( $result ) {
			$row = $result->fetch();
			if ($row) { return $row['amount_sent']; }
		}
		return 0;
	}

	function incomes() {
		global $dbh;

		$query = "SELECT sum(amount) as amount_recv FROM tb_transaction WHERE user_dst = '{$this->name}' ";
		$result = $dbh->query( $query );
		if ( $result ) {
			$row = $result->fetch();
			if ($row) {  return $row['amount_recv']; }
		}
		return 0;
	}

	function load() {
		if ($this->loaded ) return;

		$api_url = "http://twitter.com/users/show/{$this->name}.json";
		$json_content = @file_get_contents( $api_url );
		if ( $json_content !== false ) {
			$this->loaded_vars = json_decode($json_content, true);
			$this->loaded = true;
			return true;
		}
		return false;
	}

	function __get( $var ){
		if (array_key_exists( $var, $this->loaded_vars )){
			return $this->loaded_vars[ $var ];
		} else {
			return $this->$var;
		}	
	}
}

?>
