<?php


namespace PGMB\Placeholders;


interface VariableInterface {

	/**
	 * @return array Variable => Replacement
	 */
	public function variables();
}
