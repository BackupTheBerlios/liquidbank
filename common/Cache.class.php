<?php

class Cache {

	var $id = null;
	var $created_on = null;
	var $expired_on = null;

	function __construct( $id=null ) {
		if ( ! is_null $id ) ) {
			load( $id );
		}
	}

	/**
	 * purge all content belonging to that user
	 */
	function purge_user( $user_id ) {
		// FIXME: get all filenames, 
		// FIXME: remove files from cache directory
		// FIXME: remove entries from database
		throw new Exception("not implemented");
	}

	/**
	 * purge all content with given type
	 */
	function purge_content( $content_id ) {
		// FIXME: get all filenames, 
		// FIXME: remove files from cache directory
		// FIXME: remove entries from database
		throw new Exception("not implemented");
	}

	/**
	 * load id data from database
	 */
	function load( $id ){
		throw new Exception("not implemented");
	}


	function filename() {
		$str = sprintf( "%s:%s:%s", 'id', 'owner', 'created_on' );
		$filename = md5( $str );
		return $filename;
	}

	/**
	 * save cache info to database
	 */
	function save( ) {
	}
}

?>
