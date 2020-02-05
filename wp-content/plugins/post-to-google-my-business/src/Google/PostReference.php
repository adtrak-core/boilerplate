<?php


namespace PGMB\Google;


class PostReference {

	private $gmb_post_details = [];
	private $gmb_errors = [];
	private $post_location;

	public function set_post_location($location){
		$this->post_location = $location;
	}

	public function set_gmb_post_details($name, $createTime, $updateTime, $state, $searchUrl){
		$this->gmb_post_details = [
			'name'          => $name,
			'createTime'    => $createTime,
			'updateTime'    => $updateTime,
			'state'         => $state,
			'searchUrl'     => $searchUrl,
		];
	}
	public function add_gmb_error($error){
		$this->gmb_errors[] = $error;
	}

	public static function load_from_meta($item){

	}
}
