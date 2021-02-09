<?php

namespace WPScan\Checks;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * System.
 *
 * General system functions.
 *
 * @since 1.0.0
 */
class System {

	public $OPT_FATAL_ERRORS = 'wpscan_fatal_errors';


	/**
	 * A list of registered checks.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $checks = array();

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $parent parent.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		register_shutdown_function( array( $this, 'catch_errors' ) );

		add_action( 'admin_notices', array( $this, 'display_errors' ) );

		add_action( 'plugins_loaded', array( $this, 'load_checks' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'wp_ajax_wpscan_check_action', array( $this, 'handle_actions' ) );
	}

	/**
	 * Register Admin Scripts
	 *
	 * @since 1.0.0
	 * @param string $hook parent.
	 * @access public
	 * @return void
	 */
	public function admin_enqueue( $hook ) {
		if ( $hook === $this->parent->page_hook ) {
			wp_enqueue_script(
				'wpscan-security-checks',
				plugins_url( 'assets/js/security-checks.js', WPSCAN_PLUGIN_FILE ),
				array( 'jquery-ui-tooltip' )
			);
		}
	}

	/**
	 * Load checks files.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function load_checks() {
		$dir     = $this->parent->plugin_dir . 'security-checks';
		$folders = array_diff( scandir( $dir ), array('..', '.') );

		foreach ( $folders as $folder ) {
			$file = "$dir/$folder/check.php";

			if ( '.' === $folder[0] ) continue;

			require_once $file;

			$data = get_file_data( $file, array( 'classname' => 'classname' ) );

			$data['instance'] = new $data['classname']( $folder, "$dir/$folder", $this->parent );

			$this->checks[ $folder ] = $data;
		}
	}

	/**
	 * Register a shutdown hook to catch errors
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function catch_errors() {
		$error = error_get_last();

		if ($error && $error['type']) {
			
			if ( basename($error['file']) == 'check.php' ) {
				$errors = get_option($this->OPT_FATAL_ERRORS, array() );
				
				array_push( $errors, $error );

				update_option( $this->OPT_FATAL_ERRORS, array_unique( $errors ) );

				$report = $this->parent->get_report();

				$report['cache'] = strtotime( current_time( 'mysql' ) );

				update_option( $this->parent->OPT_REPORT, $report );

				$this->parent->classes['account']->update_account_status();

				delete_transient( $this->parent->WPSCAN_TRANSIENT_CRON );
			}
		}   
	}

	/**
	 * Display fatal errors
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function display_errors() {
		$screen = get_current_screen();
		$errors = get_option($this->OPT_FATAL_ERRORS, array() );
		

		if ( strstr( $screen->id, $this->parent->classes['report']->page ) ) {
			foreach ($errors as $err ) {
				$msg = explode('Stack', $err['message'])[0];
				$msg = trim($msg);

				echo "<div class='notice notice-error'><p>$msg</p></div>";
			}
		}
	}

	/**
	 * List vulnerabilities in the report.
	 *
	 * @since 1.0.0
	 *
	 * @param object $check - The check instance.
	 *
	 * @access public
	 * @return string
	 */
	public function list_check_vulnerabilities( $instance ) {
		$vulnerabilities = $instance->get_vulnerabilities();
		$count           = $instance->get_vulnerabilities_count();
		$ignored         = $this->parent->get_ignored_vulnerabilities();

		$not_checked_text = __( 'Not checked yet. Click the Run button to run a scan', 'wpscan' );

		if ( ! isset( $vulnerabilities ) ) {
			echo esc_html( $not_checked_text );
		} elseif ( empty( $vulnerabilities ) || 0 === $count ) {
			echo esc_html( $instance->success_message() );
		} else {
			$list = array();

			foreach ( $vulnerabilities as $item ) {
				if ( in_array( $item['id'], $ignored, true ) ) {
					continue;
				}

				$html  = "<div class='vulnerability'>";
				$html .= "<div class='vulnerability-severity'>";
				$html .= "<span class='wpscan-" . esc_attr( $item['severity'] ) . "'>" . esc_html( $item['severity'] ) ."</span>";
				$html .= '</div>';
				$html .= "<div class='vulnerability-title'>" . wp_kses( $item['title'], array( 'a' => array( 'href' => array() ) ) ) . '</div>';
				$html .= '</div>';
				$list[] = $html;
			}

			echo join( '<br>', $list );
		}
	}

	/**
	 * Display actions buttons
	 *
	 * @since 1.0.0
	 *
	 * @param object $instance - The check instance.
	 *
	 * @access public
	 * @return string
	 */
	public function list_actions( $instance ) {
		foreach ( $instance->actions as $action ) {
			$confirm = isset( $action['confirm'] ) ? $action['confirm'] : false;

			echo sprintf(
				"<button class='button' data-check-id='%s' data-confirm='%s' data-action='%s'> %s</button>",
				esc_attr( $instance->id ),
				esc_attr( $confirm ),
				esc_attr( $action['id'] ),
				esc_html( $action['title'] )
			);
		}
	}

	/**
	 * Load checks files.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function handle_actions() {
		check_ajax_referer( 'wpscan' );

		if ( ! current_user_can( $this->parent->WPSCAN_ROLE ) ) {
			wp_die();
		}

		$check  = isset( $_POST['check'] ) ? $_POST['check'] : false;
		$action = isset( $_POST['action_id'] ) ? $_POST['action_id'] : false;

		if ( $action && $check ) {
			$action = array_filter(
				$this->checks[ $check ]['instance']->actions,
				function ( $i ) use ( $action ) {
					return $i['id'] === $action;
				}
			);

			$action = current( $action );

			if ( method_exists( $this->checks[ $check ]['instance'], $action['method'] ) ) {
				$res = call_user_func( array( $this->checks[ $check ]['instance'], $action['method'] ) );
				if ( $res ) {
					wp_send_json_success();
				} else {
					wp_send_json_error();
				}
			}
		}

		wp_send_json_error();
	}
}
