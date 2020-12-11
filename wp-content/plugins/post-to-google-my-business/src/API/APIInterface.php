<?php


namespace PGMB\API;


use PGMB\Google\PublishedLocalPost;

interface APIInterface {
	public function get_accounts($flush_cache);
	public function get_locations($account_name, $flush_cache);
	public function get_location($location_name, $flush_cache);

	public function create_post($company_id, $args);


	public function delete_post($name);

	/**
	 * @param $post_id
	 * @param $args
	 * @param $mask
	 *
	 * @return PublishedLocalPost
	 */
	public function update_post($post_id, $args, $mask);


	public function refresh_token();


	public function revoke_access();
}
