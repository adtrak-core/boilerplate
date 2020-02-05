<?php


namespace PGMB\PostTypes;


interface PostTypeItem {
	/**
	 * @return array Array or arguments ready to insert using wp_insert_post
	 */
	public function get_post_data();
}
