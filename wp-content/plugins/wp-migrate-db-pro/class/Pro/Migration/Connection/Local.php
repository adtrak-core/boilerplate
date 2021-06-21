<?php

namespace DeliciousBrains\WPMDB\Pro\Migration\Connection;

use DeliciousBrains\WPMDB\Common\Http\Helper;
use DeliciousBrains\WPMDB\Common\Http\Http;
use DeliciousBrains\WPMDB\Common\Http\RemotePost;
use DeliciousBrains\WPMDB\Common\Http\WPMDBRestAPIServer;
use DeliciousBrains\WPMDB\Common\MigrationPersistence\Persistence;
use DeliciousBrains\WPMDB\Common\Properties\DynamicProperties;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Util\Util;
use DeliciousBrains\WPMDB\Pro\License;


class Local
{

    /**
     * @var Http
     */
    private $http;
    /**
     * @var Helper
     */
    private $http_helper;
    /**
     * @var Properties
     */
    private $props;
    /**
     * @var License
     */
    private $license;
    /**
     * @var RemotePost
     */
    private $remote_post;
    /**
     * @var Util
     */
    private $util;
    /**
     * @var DynamicProperties
     */
    private $dynamic_props;
    /**
     * @var WPMDBRestAPIServer
     */
    private $rest_API_server;

    public function __construct(
        Http $http,
        Helper $http_helper,
        Properties $props,
        License $license,
        RemotePost $remote_post,
        Util $util,
        WPMDBRestAPIServer $rest_API_server
    ) {
        $this->http            = $http;
        $this->http_helper     = $http_helper;
        $this->props           = $props;
        $this->license         = $license;
        $this->remote_post     = $remote_post;
        $this->util            = $util;
        $this->dynamic_props   = DynamicProperties::getInstance();
        $this->rest_API_server = $rest_API_server;
    }

    public function register()
    {
        // @TODO probably need to force flush rewrite rules
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function register_rest_routes()
    {
        $this->rest_API_server->registerRestRoute('/verify-connection', [
            'methods'  => 'POST',
            'callback' => [$this, 'ajax_verify_connection_to_remote_site'],
        ]);
    }

    /**
     * WP REST API endpoint call to `/verify-connection`
     * Verifies that the local site has a valid licence.
     * Sends a request to the remote site to collect additional information required to complete the migration.
     *
     * @return mixed
     */
    public function ajax_verify_connection_to_remote_site()
    {
	    $_POST = $this->http_helper->convert_json_body_to_post();

	    $key_rules = apply_filters(
		    'wpmdb_verify_connection_key_rules',
		    array(
			    'action'                      => 'key',
			    'url'                         => 'url',
			    'key'                         => 'string',
			    'intent'                      => 'key',
			    'nonce'                       => 'key',
			    'convert_post_type_selection' => 'numeric',
			    'profile'                     => 'numeric',
		    ),
		    __FUNCTION__
	    );

	    Persistence::cleanupStateOptions(); // Wipe old migration options
	    $state_data = Persistence::setPostData( $key_rules, __METHOD__ );

	    if ( !$this->license->is_valid_licence() ) {
		    $message = __( 'Please activate your license before attempting a pull or push migration.', 'wp-migrate-db' );

		    return $this->http->end_ajax( new \WP_Error( 'invalid-license', $message ) );
	    }

	    $data = array(
		    'action'  => 'wpmdb_verify_connection_to_remote_site',
		    'intent'  => $state_data['intent'],
		    'referer' => $this->util->get_short_home_address_from_url( home_url() ),
		    'version' => $this->props->plugin_version,
	    );

	    $data = apply_filters( 'wpmdb_verify_connection_to_remote_site_args', $data, $state_data );

	    $data['sig']     = $this->http_helper->create_signature( $data, $state_data['key'] );
	    $ajax_url        = $this->util->ajax_url();
	    $timeout         = apply_filters( 'wpmdb_prepare_remote_connection_timeout', 30 );
	    $remote_response = $this->remote_post->post( $ajax_url, $data, __FUNCTION__, compact( 'timeout' ), true );

	    $url_bits = Util::parse_url( $this->dynamic_props->attempting_to_connect_to );

	    // WP_Error is thrown manually by remote_post() to tell us something went wrong
	    if ( is_wp_error( $remote_response ) ) {
            return $this->http->end_ajax(
                $remote_response
            );
	    }

	    $response = false;
	    if ( is_serialized( $remote_response ) ) {
		    $response = unserialize( $remote_response );
	    }

	    if ( !$response ) {
		    return $this->http->end_ajax(
			    new \WP_Error( 'unserialize-failure', __( 'Failed attempting to unserialize the response from the remote server. Please contact support.', 'wp-migrate-db' ) ),
			    $remote_response
		    );
	    }

        $data['scheme'] = $url_bits['scheme'];
	    $data += $response;

        // Store response in DB
        Persistence::storeRemoteResponse($data);

        // Check tables exist on remote/local and update profile

        return $this->http->end_ajax($data);
    }
}
