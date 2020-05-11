<?php


namespace PGMB\Placeholders;


class SiteVariables implements VariableInterface {

	/**
	 * @inheritDoc
	 */
	public function variables() {
		$site_variables = array('name', 'description', 'url', 'pingback_url', 'atom_url', 'rdf_url', 'rss_url', 'rss2_url', 'comments_atom_url', 'comments_rss2_url');
		$variables = [];
		foreach($site_variables as $variable){
			$variables['%site_'.$variable.'%'] = get_bloginfo($variable);
		}
		return $variables;
	}
}
