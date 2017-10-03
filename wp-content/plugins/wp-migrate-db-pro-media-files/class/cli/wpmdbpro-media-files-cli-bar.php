<?php

/**
 * Class WPMDBPro_Media_Files_CLI_Bar
 * Simple wrapper for \cli\progress\bar that
 * provides setter access to the _message property
 */
class WPMDBPro_Media_Files_CLI_Bar extends \cli\progress\Bar {

    public function setMessage( $message ) {
        $this->_message = $message;
    }
}

/**
 * Class WPMDBPro_Media_Files_CLI_Bar_NoOp
 * Provides a mostly non-operative interface for
 * \cli\progress\bar when bar output is not desirable
 */
class WPMDBPro_Media_Files_CLI_Bar_NoOp {

	private $_message = '';

	public function __construct() {
	}
	public function setTotal() {
	}
	public function setMessage( $message ) {
		$this->_message = $message;
	}
	public function tick(){
	}
	public function finish(){
		// log last _message to show count of files migrated
		WP_CLI::log( $this->_message );
	}
}
