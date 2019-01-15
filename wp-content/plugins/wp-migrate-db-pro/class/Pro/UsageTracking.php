<?php

namespace DeliciousBrains\WPMDB\Pro;

use DeliciousBrains\WPMDB\Common\Error\ErrorLog;
use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\FormData\FormData;
use DeliciousBrains\WPMDB\Common\MigrationState\StateDataContainer;
use DeliciousBrains\WPMDB\Common\Properties\DynamicProperties;
use DeliciousBrains\WPMDB\Common\Properties\Properties;
use DeliciousBrains\WPMDB\Common\Settings\Settings;
use DeliciousBrains\WPMDB\Pro\UI\Template;

class UsageTracking {
	/**
	 * @var StateDataContainer
	 */
	private $state_container;
	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var License
	 */
	private $license;
	/**
	 * @var Properties
	 */
	private $props;
	/**
	 * @var Filesystem
	 */
	private $filesystem;
	/**
	 * @var ErrorLog
	 */
	private $error_log;
	/**
	 * @var Template
	 */
	private $template;

	private $dynamic_props;
	/**
	 * @var FormData
	 */
	private $form_data;

	public function __construct(
		Settings $settings,
		License $license,
		Filesystem $filesystem,
		ErrorLog $error_log,
		Template $template,
		FormData $form_data,
		DynamicProperties $dynamic_properties,
		StateDataContainer $state_data_container,
		Properties $properties
	) {

		$this->state_container = $state_data_container;
		$this->settings        = $settings->get_settings();
		$this->license         = $license;
		$this->props           = $properties;
		$this->filesystem      = $filesystem;
		$this->error_log       = $error_log;
		$this->template        = $template;
		$this->dynamic_props   = $dynamic_properties;
		$this->form_data       = $form_data;
	}

	/**
	 * Adds/updates the `wpmdb_usage` option with most recent 'qualified' plugin use,
	 * stores time as well as the action (push/pull/export/find-replace)
	 *
	 * @param string $action
	 */
	public static function log_usage( $action = '' ) {
		update_site_option( 'wpmdb_usage', array( 'action' => $action, 'time' => time() ) );
	}

	/**
	 * Gets just the timestamp of the latest usage to send with the API requests
	 *
	 * @return int
	 */
	public function get_last_usage_time() {
		$option = get_site_option( 'wpmdb_usage' );

		return ( $option && $option['time'] ) ? $option['time'] : 0;
	}

	public function register() {
		add_action( 'wpmdb_additional_settings_advanced', array( $this, 'template_toggle_usage_tracking' ) );
		if ( false !== $this->settings['allow_tracking'] ) {
			add_action( 'wpmdb_initiate_migration', array( $this, 'log_migration_event' ), 100 );
			add_action( 'wpmdb_notices', array( $this, 'template_notice_enable_usage_tracking' ) );
		}
	}

	public function log_migration_event() {
		$state_data = $this->state_container->getData();
		$settings   = $this->settings;

		if ( true !== $settings['allow_tracking'] ) {
			return false;
		}

		$api_url = apply_filters( 'wpmdb_logging_endpoint_url', 'https://api2.deliciousbrains.com/event' );

		$log_data = array(
			'local_timestamp'                        => time(),
			'licence_key'                            => $this->license->get_licence_key(),
			'cli'                                    => $this->dynamic_props->doing_cli_migration,
			'setting-compatibility_plugin_installed' => $this->filesystem->file_exists( $this->props->mu_plugin_dest ),
		);

		foreach ( $this->form_data->parse_migration_form_data( $state_data['form_data'] ) as $key => $val ) {
			if ( 'connection_info' === $key ) {
				continue;
			}
			$log_data[ 'profile-' . $key ] = $val;
		}

		foreach ( $settings as $key => $val ) {
			if ( 'profiles' === $key || 'key' === $key ) {
				continue;
			}
			$log_data[ 'setting-' . $key ] = $val;
		}

		foreach ( $GLOBALS['wpmdb_meta'] as $plugin => $arr ) {
			$log_data[ $plugin . '-active' ]  = true;
			$log_data[ $plugin . '-version' ] = $arr['version'];
		}

		foreach ( $state_data['site_details'] as $site => $info ) {
			$log_data[ $site . '-site_url' ] = $info['site_url'];
			$log_data[ $site . '-home_url' ] = $info['home_url'];
			$log_data[ $site . '-prefix' ]   = $info['prefix'];

			$log_data[ $site . '-is_multisite' ] = $info['is_multisite'];

			if ( isset( $info['subsites'] ) && is_array( $info['subsites'] ) ) {
				$log_data[ $site . '-subsite_count' ] = count( $info['subsites'] );
			}

			$log_data[ $site . '-is_subdomain_install' ] = $info['is_subdomain_install'];
		}

		$diagnostic_log = [];

		foreach ( $this->error_log->get_diagnostic_info() as $group_name => $data ) {
			foreach ( $data as $key => $val ) {
				if ( 0 === $key ) {
					continue 1;
				}
				$key_name = $group_name;
				if ( is_string( $key ) ) {
					$key_name .= "-{$key}";
				}
				$diagnostic_log[ $key_name ] = $val;
			}
		}

		$log_data['diagnostic_log'] = $diagnostic_log;

		foreach ( $log_data as $key => $val ) {
			if ( strstr( $key, 'count' ) || is_array( $val ) ) {
				continue;
			}
			if ( '1' === $val ) {
				$log_data[ $key ] = true;
				continue;
			}
			if ( '0' === $val ) {
				$log_data[ $key ] = false;
				continue;
			}
			if ( 'true' === $val ) {
				$log_data[ $key ] = true;
				continue;
			}
			if ( 'false' === $val ) {
				$log_data[ $key ] = false;
				continue;
			}
		}

		$remote_post_args = array(
			'blocking' => apply_filters( 'wpmdb_logging_blocking_requests', false ),
			'timeout'  => 60,
			'method'   => 'POST',
			'headers'  => array( 'Content-Type' => 'application/json' ),
			'body'     => json_encode( $log_data ),
		);

		$result = wp_remote_post( $api_url, $remote_post_args );
		if ( is_wp_error( $result ) || $result['response']['code'] >= 400 ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Error logging migration event' );
				error_log( print_r( $result, 1 ) );
			}
			$this->error_log->log_error( 'Error logging Migration event', $result );
		}

		return true;
	}

	public function template_notice_enable_usage_tracking() {
		if ( 'boolean' !== gettype( $this->settings['allow_tracking'] ) ) {
			$this->template->template( 'notice-enable-usage-tracking', 'pro' );
		}
	}

	public function template_toggle_usage_tracking() {
		$this->template->template( 'toggle-usage-tracking', 'pro' );
	}
}
