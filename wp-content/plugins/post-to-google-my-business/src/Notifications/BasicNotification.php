<?php


namespace PGMB\Notifications;


class BasicNotification implements Notification {
	public $identifier, $data;
	private $section;

	/**
	 * AdminNotification constructor.
	 *
	 * @param $section
	 * @param $identifier
	 * @param $data
	 */
	public function __construct($section, $identifier, $data) {
		$this->identifier = $identifier;
		$this->section = $section;
		$this->data = $data;
	}

	public static function create($section, $identifier, $title, $text, $image, $alt){
		$data =[
			'title' => $title,
			'text' => $text,
			'image' => $image,
			'alt' => $alt,
		];
		return new static($section, $identifier, $data);
	}

	public function get_title(){
		return $this->data['title'];
	}

	public function get_text(){
		return $this->data['text'];
	}

	public function get_image(){
		return $this->data['image'];
	}

	public function get_alt(){
		return $this->data['alt'];
	}

	public function get_section() {
		return $this->section;
	}

	public function get_identifier() {
		return $this->identifier;
	}

	public function get_data() {
		return $this->data;
	}
}
