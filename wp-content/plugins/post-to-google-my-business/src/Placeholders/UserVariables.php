<?php


namespace PGMB\Placeholders;


class UserVariables implements VariableInterface {

	private $post_id;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * @inheritDoc
	 */
	public function variables() {
		$post = get_post($this->post_id);
		$variables = [];
		//User info
		$user_variables = array(
			'aim',
			'description',
			'display_name',
			'first_name',
			'jabber',
			'last_name',
			'nickname',
			'user_email',
			'user_nicename',
			'user_url',
			'yim'
		);
		foreach($user_variables as $variable){
			$variables['%author_'.$variable.'%'] = get_the_author_meta($variable, $post->post_author);
		}
		return $variables;
	}
}
