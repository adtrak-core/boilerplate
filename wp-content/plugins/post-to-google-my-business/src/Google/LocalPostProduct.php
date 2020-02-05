<?php


namespace PGMB\Google;

//todo: complete this class if the LocalPostProduct post type ever returns
class LocalPostProduct extends AbstractGoogleJsonObject {

	public function __construct($productName, Money $lowerPrice, $upperPrice) {
	}


	public function setProductName($productName){
		$this->jsonOutput['productName'] = $productName;
	}

	public static function fromArray( $array ) {
		// TODO: Implement fromArray() method.
	}
}
