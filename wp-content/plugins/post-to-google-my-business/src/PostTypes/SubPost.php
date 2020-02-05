<?php


namespace PGMB\PostTypes;

use InvalidArgumentException;

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

	//Default form fields
	private $form_fields = [
		'mbp_attachment_type'   => 'PHOTO',
		'mbp_topic_type'        => 'STANDARD',
		'mbp_post_attachment'   => '',
		'mbp_post_text'         => '',
		'mbp_event_title'       => '',
		'mbp_event_start_date'  => '',
		'mbp_event_end_date'    => '',
		'mbp_offer_title'       => '',
		'mbp_offer_coupon'      => '',
		'mbp_offer_redeemlink'  => '',
		'mbp_offer_terms'       => '',
		'mbp_button_type'       => 'LEARN_MORE',
		'mbp_button_url'        => '',
		'mbp_schedule'          => false,
		'mbp_scheduled_date'    => '',
		'mbp_cron_schedule'     => '0 12 * * 1',
		'mbp_repost'            => false,
		'mbp_repost_stop_after' => 'executions',
		'mbp_repost_stop_date'  => '',
		'mbp_reposts'           => 1,
		'mbp_selected_location' => [],
		'mbp_content_image'     => false,
		'mbp_featured_image'    => true
	];

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

	public function auto_form_fields($template, $cta, $url_template, $content_image = false, $featured_image = true, $locations = []){
		$add_cta = true;
		if($cta == 'NONE' || $cta == 'ACTION_TYPE_UNSPECIFIED'){ $add_cta = false; }
		$updated_fields = [
			'mbp_post_text'         => $template,
			'mbp_button'            => $add_cta,
			'mbp_button_type'       => $cta,
			'mbp_selected_location' => $locations,
			'mbp_button_url'        => $url_template,
			'mbp_content_image'     => $content_image,
			'mbp_featured_image'    => $featured_image,
		];
		$this->form_fields = array_merge($this->form_fields, $updated_fields);
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
