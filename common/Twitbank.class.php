<?php

class Twitbank {
	private $old_twit_id = null;

	function __construct() {
	}

	function getCurrentTwitId() {
		global $dbh;

		$query = "SELECT value FROM tb_config WHERE label = 'latest_twit_id'";
		$result = $dbh->query($query);
		if ( $result ) {
			$row = $result->fetch();
			$this->old_twit_id = $row['value'];
		} else {
			$this->old_twit_id = 0;
		}
		return 	$this->old_twit_id;
	}
	
	function setCurrentTwitId( $new_twit_id )	{
		global $dbh;
		
		if ( is_null( $this->old_twit_id ) ) { 
			$this->getCurrentTwitId(); 
		}

		if ( $this->old_twit_id != $new_twit_id ) {
			// write max id to config
			$query = "UPDATE tb_config SET value = :id WHERE label = 'latest_twit_id'";
			$stmt = $dbh->prepare( $query );
			$stmt->bindParam( ':id', $new_twit_id, PDO::PARAM_STR );
			$stmt->execute();

			$query = "UPDATE tb_config SET value = current_timestamp WHERE label = 'update_stamp'";
			$stmt = $dbh->prepare( $query );
			$stmt->execute();
			return true;
		} else {
			return false;
		}
	}
}

?>
