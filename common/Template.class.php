<?php

class Template {
	var $vars; 
	var $template;

	function __construct( $filepath ) {
		$this->vars = array();
		$this->template = file_get_contents( $filepath );

		preg_match_all( '/\[\[(?P<var>[A-Z0-9]+)\]\]/', $this->template, $matches,  PREG_SET_ORDER );
		
		foreach( $matches as $match => $mset ){
			$this->vars[ $mset['var'] ] = null ;
		}
	}

	function bind( $var, $content ) {
		if ( array_key_exists( $var, $this->vars ) ) {
			$this->vars[$var] = $content;
		} else {
			throw new Exception("Unknown template variable $var");
		}
	}

	function publish() {
		$template = $this->template;
		foreach ( $this->vars as $var => $content ) {
			$template = str_replace( '[[' . $var . ']]', $content, $template );
		}

		eval( '?>' . $template . '<?' );
	}
}
?>
