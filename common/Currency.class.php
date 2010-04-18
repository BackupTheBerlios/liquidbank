<?php

class Currency {
	var $db;
	var $name;

	function __construct( $name ) {
		$this->name = $name;

		if ( ! preg_match("/^[a-zA-Z0-9]+$/", $name ) ) {
			throw new Exception("Currency $name is not valid");
		}

		$this->db = realpath( dirname( __FILE__ ) . "/../currency/$name.sqlite3" ) ;

		if ( ! file_exists( $this->db ) ) {
			throw new Exception("Currency $name was not registered" );
		}

	}

}

?>
