<?php


namespace PGMB\Placeholders;


use stdClass;

class LocationVariables implements VariableInterface {
	private $location;


	/**
	 * LocationVariables constructor.
	 *
	 * @param $location stdClass Location object from the Google API
	 */
	public function __construct( $location ) {
		$this->location = $location;
	}


	/**
	 * Recursive function to loop through the different layers of the location data object
	 *
	 * @param $prop_value array|object - The data to process
	 * @param $current_prefix string - The variable prefix for the current depth
	 * @param $variables array
	 */
	protected function location_variables_recurse($prop_value, $current_prefix, &$variables){
		foreach($prop_value as $key => $value){
			if(is_array($value) || is_object($value)) {
				$this->location_variables_recurse($value, "{$current_prefix}_{$key}", $variables);
				continue;
			}
			$variables["{$current_prefix}_{$key}%"] = $value;
		}
	}


	/**
	 * @inheritDoc
	 */
	public function variables() {
		$variables = [];
		$this->location_variables_recurse($this->location, "%location", $variables);
		return $variables;
	}
}
