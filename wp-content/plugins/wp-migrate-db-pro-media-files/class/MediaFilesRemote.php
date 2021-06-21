<?php

namespace DeliciousBrains\WPMDBMF;

use DeliciousBrains\WPMDB\Pro\Transfers\Files\PluginHelper;

class MediaFilesRemote {
    /**
     * @var PluginHelper
     */
    private $plugin_helper;

    public function __construct(
        PluginHelper $plugin_helper
	) {
        $this->plugin_helper = $plugin_helper;
    }

	public function register() {
		// Remote AJAX handlers
		add_action( 'wp_ajax_nopriv_wpmdbmf_respond_to_get_remote_media', array( $this, 'respond_to_get_remote_media' ) );

        add_action('wp_ajax_nopriv_wpmdbmf_respond_to_save_queue_status', array($this, 'ajax_mf_respond_to_save_queue_status'));
        add_action('wp_ajax_nopriv_wpmdbmf_transfers_send_file', array($this, 'ajax_mf_respond_to_request_files',));
        add_action('wp_ajax_nopriv_wpmdbmf_transfers_receive_file', array($this, 'ajax_mf_respond_to_post_file'));
	}

    /**
     * @param $stage
     *
     * @return mixed|null
     */
    public function respond_to_get_remote_media()
    {
        return $this->plugin_helper->respond_to_get_remote_folders('media_files');
    }

    public function ajax_mf_respond_to_save_queue_status(){
        return $this->plugin_helper->respond_to_save_queue_status();
    }

    public function ajax_mf_respond_to_request_files(){
        return $this->plugin_helper->respond_to_request_files();
    }

    public function ajax_mf_respond_to_post_file(){
        return $this->plugin_helper->respond_to_post_file();
    }

}
