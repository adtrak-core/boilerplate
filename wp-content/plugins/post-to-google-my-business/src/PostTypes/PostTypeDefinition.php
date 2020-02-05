<?php


namespace PGMB\PostTypes;


interface PostTypeDefinition {
	/**
	 * @return array Array of arguments for registering a post type.
	 */
	public static function post_type_data();
}
