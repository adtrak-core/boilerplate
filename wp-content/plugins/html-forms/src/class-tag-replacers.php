<?php

namespace HTML_Forms;

use WP_Post;

class TagReplacers {

	/**
	* @param string $prop
	* @return mixed
	*/
	public function user( $prop ) {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$user = wp_get_current_user();
		return isset( $user->{$prop} ) ? $user->{$prop} : '';
	}

	/**
	* @param string $prop
	* @return mixed
	*/
	public function post( $prop ) {
		global $post;

		if ( ! $post instanceof WP_Post || ! isset( $post->{$prop} ) ) {
			return '';
		}

		return $post->{$prop};
	}

	/**
	* @param string $key
	* @return mixed
	*/
	public function url_params( $key ) {
		if ( ! isset( $_GET[ $key ] ) ) {
			return '';
		}

		return esc_attr( strip_tags( $_GET[ $key ] ) );
	}
}
