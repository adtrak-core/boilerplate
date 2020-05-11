<?php


namespace PGMB\Placeholders;


use PGMB\Vendor\Html2Text\Html2Text;

class PostVariables implements VariableInterface {

	private $post_id;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}


	public function parse_post_content($post_content){
		$text = wpautop($post_content); //Add paragraph tags
		$text = preg_replace("~(?:\[/?)[^\]]+/?\]~s", '', $text); //Strip shortcodes
		$parse_html = new Html2Text(
			$text,
			array(
				'width'    => 0,
			)
		);
		$text = $parse_html->getText();
		$text = trim($text);
		return $text;
	}

	/**
	 * @inheritDoc
	 */
	public function variables() {
		$post = get_post($this->post_id);
		$variables = [];
		foreach($post as $key => $value){
			$variables['%'.$key.'%'] = $value;
		}
		$variables['%post_content%'] = $this->parse_post_content($variables['%post_content%']);
		return $variables;
	}
}
