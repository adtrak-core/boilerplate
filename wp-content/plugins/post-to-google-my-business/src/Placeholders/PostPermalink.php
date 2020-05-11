<?php


namespace PGMB\Placeholders;


class PostPermalink implements VariableInterface {

	private $post_id;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * @inheritDoc
	 */
	public function variables() {
		return [
			'%post_permalink%'    => get_permalink($this->post_id)
		];
	}
}
