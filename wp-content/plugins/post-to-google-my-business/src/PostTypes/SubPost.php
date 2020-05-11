<?php


namespace PGMB\PostTypes;

use InvalidArgumentException;
use PGMB\FormFields;

class SubPost extends AbstractPostType {
	const POST_TYPE	= 'mbp-google-subposts';

	public static function post_type_data() {
		return [
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'can_export' => true
		];
	}

	private $is_autopost = false;

	private $form_fields;

	public function __construct() {
		$this->form_fields = FormFields::default_post_fields();
	}

	private $draft = false;

	public function get_post_data() {
		$base = parent::get_post_data();
		$fields = [
			'post_type' => self::POST_TYPE,
			'meta_input' => [
				'mbp_form_fields'		=> $this->form_fields,
				'_mbp_is_autopost'      => $this->is_autopost,
			],
			'post_status' => $this->draft ? 'draft' : 'publish'
		];
		return array_merge($base, $fields);
	}


	public function set_form_fields($fields){
		if(!is_array($fields)){ throw new InvalidArgumentException("Form fields expects an array"); }
		$this->form_fields = $fields;
	}


	public function set_draft($draft = true){
		if($draft){
			$this->draft = true;
			return;
		}
		$this->draft = false;
	}

	public function set_autopost($autopost = true){
		$this->is_autopost = $autopost;
	}

}
