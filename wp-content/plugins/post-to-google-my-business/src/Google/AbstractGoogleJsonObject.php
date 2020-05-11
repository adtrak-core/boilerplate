<?php


namespace PGMB\Google;

use JsonSerializable;
use InvalidArgumentException;

abstract class AbstractGoogleJsonObject implements JsonSerializable, LocalPostJsonDeserializeInterface {
	protected $jsonOutput = [];
	public function jsonSerialize() {
		return $this->jsonOutput;
	}

	public static function fromJson($json){
		$localPostData = json_decode($json, true);

		return static::fromArray($localPostData);
	}

	public function getArray(){
		return $this->jsonOutput;
	}

	public function __get( $name ) {
		if(!array_key_exists($name, $this->jsonOutput)){
			throw new InvalidArgumentException(__("Unknown property", "post-to-google-my-business"));
		}
		return $this->jsonOutput[$name];
	}
}
