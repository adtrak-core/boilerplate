<?php


namespace PGMB\Google;


interface LocalPostJsonDeserializeInterface {
	public static function fromJson($json);
	public static function fromArray($array);
	public function __get( $name );
}
