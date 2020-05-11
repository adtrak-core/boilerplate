<?php

if(!class_exists('MBP_Connector')){
	class MBP_Connector{
		//const API_URL 		= 'https://util.tycoonmedia.net/api/v1/';

		private $api_url;
		private $api_connected;

		public function __construct($api_connected){
			$this->api_connected = $api_connected;
		}

		public function init(){
			$this->api_url = apply_filters('mbp_api_url', 'https://util.tycoonmedia.net/api/v1/');
			add_action('admin_post_mbp_generate_url', array(&$this, 'generate_url'));
			add_action('admin_post_mbp_disconnect', array(&$this, 'unlink_site'));
			add_action('admin_post_mbp_revoke', array(&$this, 'revoke_access'));
			add_action('admin_post_mbp_response', array(&$this, 'handle_response'));
			add_action('mbp_refresh_token', array(&$this, 'refresh_token'));
			$this->check_refresh_event();
		}

		public function check_refresh_event(){
			if(!wp_next_scheduled('mbp_refresh_token')){
				wp_schedule_event(time() + DAY_IN_SECONDS, 'daily', 'mbp_refresh_token');
			}
		}


		public function generate_url(){
			if(!current_user_can("manage_options")){ wp_die(__('No permission to edit options', 'post-to-google-my-business')); }

			$response = wp_remote_post($this->api_url.'google/authlink/',
				apply_filters('mbp_generate_url_args', array(
					'timeout'	=> 20,
					'body' => array(
						'post_url' 	=> esc_url(admin_url('admin-post.php')),
						'nonce'		=> wp_create_nonce('mbp_generate_url')
					)
				))
			);

			if(is_wp_error($response)){
				$error_message = $response->get_error_message();
				wp_die(sprintf(__('Something went wrong: %s', 'post-to-google-my-business'), $error_message));
			}

			$data = json_decode($response['body']);
			update_option('mbp_request_key', sanitize_key($data->key));
			delete_transient('mbp_get_accounts_cache');
			wp_redirect($data->url);
			exit;
		}


		public function handle_response(){
			if(!current_user_can('manage_options')){ wp_die(__('No permission to edit options', 'post-to-google-my-business')); }

			if(!wp_verify_nonce(sanitize_key($_REQUEST['nonce']), 'mbp_generate_url')){ wp_die(__('Invalid nonce', 'post-to-google-my-business')); }

			update_option('mbp_api_key', sanitize_key($_REQUEST['key']));
			update_option('mbp_site_key', sanitize_key($_REQUEST['sitekey']));
			update_option('mbp_api_token', sanitize_key($_REQUEST['token']));

			wp_safe_redirect(admin_url('admin.php?page=post_to_google_my_business'));
			exit;
		}


		public function refresh_token(){
			if(!$this->api_connected){ return false; }
			$api = MBP_api::getInstance();
			$token = $api->refresh_token();
			if(!$token){
				return false;
			}
			update_option('mbp_api_token', sanitize_key($token));
			return true;
		}


		public function revoke_access(){
			$api = MBP_api::getInstance();
			$result = $api->revoke_access();
			update_option('mbp_api_key', false);
			update_option('mbp_site_key', false);
			update_option('mbp_api_token', false);
			delete_transient('mbp_get_accounts_cache');
			update_option('mbp_google_settings', false);
			//update_option('mbp_google_business', false);
			wp_safe_redirect(admin_url('admin.php?page=post_to_google_my_business'));
		}

		public function unlink_site(){
			update_option('mbp_api_key', false);
			update_option('mbp_site_key', false);
			update_option('mbp_api_token', false);
			delete_transient('mbp_get_accounts_cache');
			update_option('mbp_google_settings', false);
			//update_option('mbp_google_business', false);
			wp_safe_redirect(admin_url('admin.php?page=post_to_google_my_business'));
		}

	}

}
