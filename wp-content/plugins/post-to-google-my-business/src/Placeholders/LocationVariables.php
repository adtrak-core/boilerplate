<?php


namespace PGMB\Placeholders;


class LocationVariables implements VariableInterface {
	private $location;

	/**
	 * LocationVariables constructor.
	 *
	 * @param $location
	 */
	public function __construct( $location ) {
		$this->location = $location;
	}

	/**
	 * @inheritDoc
	 */
	public function variables() {
		return [
			'%location_primaryPhone%'    => $this->location->primaryPhone,
			'%location_websiteUrl%'    => $this->location->websiteUrl,
		];
	}
}
