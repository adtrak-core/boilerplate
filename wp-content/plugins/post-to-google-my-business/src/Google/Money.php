<?php


namespace PGMB\Google;

//todo: complete this class if the LocalPostProduct post type ever returns
class Money extends AbstractGoogleJsonObject {
	protected $currencyCodes;
	public function __construct($currencyCode, $units, $nanos) {
		$this->setCurrencyCode($currencyCode);
		$this->setUnits($units);
		$this->setNanos($nanos);
	}

	public function setCurrencyCode($currencyCode){
		$this->jsonOutput['currencyCode'] = $currencyCode;
	}

	public function setUnits($units){
		$this->jsonOutput['units'] = $units;
	}

	public function setNanos($nanos){
		$this->jsonOutput['nanos'] = $nanos;
	}

	public static function fromArray( $array ) {
		// TODO: Implement fromArray() method.
	}
}
