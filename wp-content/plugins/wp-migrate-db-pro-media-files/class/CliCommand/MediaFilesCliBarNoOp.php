<?php

namespace DeliciousBrains\WPMDBMF\CliCommand;

class MediaFilesCliBarNoOp {

	private $_message = '';

	public function __construct() {
	}

	public function setTotal() {
	}

	public function setMessage( $message ) {
		$this->_message = $message;
	}

	public function tick() {
	}

	public function finish() {
		// log last _message to show count of files migrated
		\WP_CLI::log( $this->_message );
	}
}
