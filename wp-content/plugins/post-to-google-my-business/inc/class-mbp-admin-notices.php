<?php
	if(!defined('ABSPATH')){ die(); }

	if(!class_exists('MBP_Admin_Notices')){
		class MBP_Admin_Notices{

			private static $instance;
			private $admin_notices;
			const TYPES = 'error,warning,info,success';

			public function __construct(){
				$this->admin_notices = new stdClass();
				foreach(explode(',', self::TYPES) as $type){
					$this->admin_notices->{$type} = array();
				}
				add_action('admin_notices', array(&$this, 'action_admin_notices'));
				add_action( 'admin_enqueue_scripts', array( &$this, 'action_admin_enqueue_scripts' ) );
				add_action( 'admin_init', array( &$this, 'action_admin_init' ) );
			}

			public static function getInstance(){
				if(!isset(self::$instance)){
					self::$instance = new self();
				}
				return self::$instance;
			}

			public function error( $message, $dismiss_option = false ) {
				$this->notice( 'error', $message, $dismiss_option );
			}

			public function warning( $message, $dismiss_option = false ) {
				$this->notice( 'warning', $message, $dismiss_option );
			}

			public function success( $message, $dismiss_option = false ) {
				$this->notice( 'success', $message, $dismiss_option );
			}

			public function info( $message, $dismiss_option = false ) {
				$this->notice( 'info', $message, $dismiss_option );
			}

			private function notice( $type, $message, $dismiss_option ) {
				$notice = new stdClass();
				$notice->message = $message;
				$notice->dismiss_option = $dismiss_option;

				$this->admin_notices->{$type}[] = $notice;
			}

			public function action_admin_notices() {
				foreach ( explode( ',', self::TYPES ) as $type ) {
					foreach ( $this->admin_notices->{$type} as $admin_notice ) {

						$dismiss_url = add_query_arg( array(
							'mbp_dismiss' => $admin_notice->dismiss_option
						), admin_url() );

						if ( ! get_option( "mbp_dismissed_{$admin_notice->dismiss_option}" ) ) {
							?><div
								class="notice mbp-notice notice-<?php echo $type;

								if ( $admin_notice->dismiss_option ) {
									echo ' is-dismissible" data-dismiss-url="' . esc_url( $dismiss_url );
								} ?>">

								<h2><?php echo "Post to Google My Business $type"; ?></h2>
								<p><?php echo $admin_notice->message; ?></p>

							</div><?php
						}
					}
				}
			}

			public function action_admin_enqueue_scripts() {
				wp_enqueue_script(
					'mbp-notice',
					plugins_url('js/notice.js', __FILE__),
					array('jquery'),
					'1.0.0',
					true
				);
			}

			public function action_admin_init() {
				$dismiss_option = filter_input( INPUT_GET, 'mbp_dismiss', FILTER_SANITIZE_STRING );
				if ( is_string( $dismiss_option ) ) {
					update_option( "mbp_dismissed_$dismiss_option", true );
					wp_die();
				}
			}


			public static function error_handler( $errno, $errstr, $errfile, $errline, $errcontext ) {
				if ( ! ( error_reporting() & $errno ) ) {
					// This error code is not included in error_reporting
					return;
				}

				$message = "Error: $errstr, File: $errfile, Line: $errline, PHP version: " . PHP_VERSION . " OS: " . PHP_OS;

				$self = self::getInstance();
				switch ($errno) {
					case E_USER_ERROR:
						$self->error( $message );
						break;

					case E_USER_WARNING:
						$self->warning( $message );
						break;

					case E_USER_NOTICE:
					default:
						$self->info( $message );
						break;
				}

				// write to wp-content/debug.log if logging enabled
				error_log( $message );

				// Don't execute PHP internal error handler
				return true;
			}


		}
	}
?>
