<?php

use  PGMB\Notifications\NotificationManager ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone ;
use  PGMB\WeDevsSettingsAPI ;
if ( !class_exists( 'MBP_Admin_Page_Settings' ) ) {
    class MBP_Admin_Page_Settings
    {
        const  POST_EDITOR_CALLBACK_PREFIX = 'mbp_settings_posteditor' ;
        const  BUSINESSSELECTOR_CALLBACK_PREFIX = "mbp_settings_selector" ;
        const  SETTINGS_PAGE = 'post_to_google_my_business' ;
        const  FIELD_PREFIX = 'mbp_quick_post_settings[autopost_template]' ;
        const  NOTIFICATION_SECTION = "dashboard-notifications" ;
        const  NEW_FEATURES_SECTION = "feature-notifications" ;
        private  $settings_api ;
        protected  $business_selector ;
        private  $gutenberg_post_types_without_fields = false ;
        /**
         * @var NotificationManager
         */
        public  $notification_manager ;
        private  $plugin_version ;
        private  $api_connected ;
        public function __construct( $plugin_version, $api_connected, NotificationManager $notification_manager )
        {
            $this->notification_manager = $notification_manager;
            $this->settings_api = new WeDevsSettingsAPI();
            $this->plugin_version = $plugin_version;
            $this->api_connected = $api_connected;
            $this->business_selector = new \PGMB\Components\BusinessSelector( MBP_api::getInstance() );
        }
        
        public function init()
        {
            add_action( 'admin_init', array( &$this, 'admin_init' ) );
            add_action( 'admin_menu', array( $this, 'add_menu' ) );
            add_action( 'wp_ajax_mbp_delete_notification', [ $this, 'ajax_delete_notification' ] );
            //add_action('w$this->business_selectorp_ajax_mbp_get_businesses', array(&$this, 'get_businesses_ajax'));
            $post_editor = new \PGMB\Components\PostEditor();
            $post_editor->register_ajax_callbacks( self::POST_EDITOR_CALLBACK_PREFIX );
            $this->business_selector->register_ajax_callbacks( self::BUSINESSSELECTOR_CALLBACK_PREFIX );
            $this->business_selector->set_field_name( 'mbp_google_settings[google_location]' );
            $calendar_feed = new \PGMB\Calendar\Feed();
            $calendar_feed->init( 'mbp_get_timegrid_feed' );
            //				$notification = \PGMB\Notifications\BasicNotification::create(
            //                    self::NOTIFICATION_SECTION,
            //					"new-notificatio54224n",
            //					"Cool stuff updated vcoodsfg dffdg ffdg",
            //					"Notification tdfgdfgdfgfdext dfgdfgdfg fdgdfgdf gdfgdfgdfg ",
            //					"img/koen.png",
            //					"Profile photo"
            //				);
            //				$this->notification_manager->add_notification($notification);
        }
        
        public function admin_init()
        {
            $this->settings_api->set_sections( $this->get_settings_sections() );
            $this->settings_api->set_fields( $this->get_settings_fields() );
            $this->settings_api->admin_init();
            add_action( 'wsa_form_top_mbp_google_settings', array( &$this, 'google_form_top' ) );
            add_action( 'wsa_form_top_mbp_quick_post_settings', array( $this, 'quick_post_top' ) );
            add_action( 'wsa_form_bottom_mbp_debug_info', array( &$this, 'debug_info' ) );
            add_action( 'wsa_form_bottom_mbp_dashboard', [ $this, 'dashboard' ] );
            add_action( 'wsa_form_bottom_mbp_post_type_settings', [ $this, 'gutenberg_post_types_info' ] );
            //add_action('wsa_form_bottom_mbp_google_settings', array(&$this, 'google_form_bottom'));
        }
        
        public function load_js()
        {
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        }
        
        public function enqueue_scripts( $hook )
        {
            wp_enqueue_style( 'jquery-ui', plugins_url( '../css/jquery-ui.min.css', __FILE__ ) );
            wp_enqueue_script(
                'mbp-settings-page',
                plugins_url( '../js/settings.js', __FILE__ ),
                array( 'jquery', 'jquery-ui-datepicker' ),
                $this->plugin_version,
                true
            );
            $localize_vars = [
                'refresh_locations'                => __( 'Refresh locations', 'post-to-google-my-business' ),
                'please_wait'                      => __( 'Please wait...', 'post-to-google-my-business' ),
                'POST_EDITOR_CALLBACK_PREFIX'      => self::POST_EDITOR_CALLBACK_PREFIX,
                'BUSINESSSELECTOR_CALLBACK_PREFIX' => self::BUSINESSSELECTOR_CALLBACK_PREFIX,
                'FIELD_PREFIX'                     => self::FIELD_PREFIX,
                'CALENDAR_TIMEZONE'                => WpDateTimeZone::getWpTimezone()->getName(),
            ];
            wp_localize_script( 'mbp-settings-page', 'mbp_localize_script', $localize_vars );
        }
        
        function get_current_setting( $option, $section, $default = '' )
        {
            $options = get_option( $section );
            if ( isset( $options[$option] ) ) {
                return $options[$option];
            }
            return $default;
        }
        
        public function get_settings_page()
        {
            return self::SETTINGS_PAGE;
        }
        
        function get_settings_sections()
        {
            /*
            //if(mbp_fs()->is_plan_or_trial__premium_only('pro')){
            	$sections[] = array(
            		'id'    => 'mbp_post_type_settings',
            		'title' => __('Post type settings', 'post-to-google-my-business')
            	);
            //}
            */
            return array(
                array(
                'id'    => 'mbp_dashboard',
                'title' => __( 'Dashboard', 'post-to-google-my-business' ),
            ),
                array(
                'id'    => 'mbp_google_settings',
                'title' => __( 'Google settings', 'post-to-google-my-business' ),
            ),
                array(
                'id'    => 'mbp_quick_post_settings',
                'title' => __( 'Auto-post settings', 'post-to-google-my-business' ),
            ),
                array(
                'id'    => 'mbp_post_type_settings',
                'title' => __( 'Post type settings', 'post-to-google-my-business' ),
            ),
                array(
                'id'    => 'mbp_debug_info',
                'title' => __( 'Debug', 'post-to-google-my-business' ),
            )
            );
        }
        
        function get_settings_fields()
        {
            $fields = array(
                'mbp_google_settings'     => array( array(
                'name'     => 'google_location',
                'label'    => __( 'Default location', 'post-to-google-my-business' ),
                'desc'     => __( 'Select the post-types where the GMB metabox should be displayed', 'post-to-google-my-business' ),
                'callback' => array( &$this, 'settings_field_google_business' ),
            ) ),
                'mbp_quick_post_settings' => array( array(
                'name'  => 'invert',
                'label' => __( 'Post to GMB by default', 'post-to-google-my-business' ),
                'desc'  => __( 'The Auto-post checkbox will be checked by default, and your WordPress posts will be automatically published to GMB, unless you uncheck it.', 'post-to-google-my-business' ),
                'type'  => 'checkbox',
            ), array(
                'name'              => 'autopost_template',
                'label'             => __( 'Default template', 'post-to-google-my-business' ),
                'desc'              => sprintf( __( 'The template for new Google posts when using quick post. Supports <a target="_blank" href="%s">variables</a> and <a target="_blank" href="%s">spintax</a> (premium only)', 'post-to-google-my-business' ), 'https://tycoonmedia.net/blog/using-the-quick-publish-feature/', 'https://tycoonmedia.net/blog/using-spintax/' ),
                'callback'          => [ $this, 'settings_field_autopost_template' ],
                'sanitize_callback' => [ $this, 'validate_autopost_template' ],
                'default'           => \PGMB\FormFields::default_autopost_fields(),
            ) ),
            );
            //					$fields['mbp_quick_post_settings'][] =
            //						array(
            //							'name'			=> 'url',
            //							'label'			=> __('Button URL', 'post-to-google-my-business'),
            //							'desc'			=> __('The URL where people should be redirected after clicking the button', 'post-to-google-my-business'),
            //							'type'			=> 'text',
            //							'sanitize_callback' => array(&$this, 'validate_quick_post_template'),
            //							'default'		=> '%post_permalink%'
            //						);
            $fields['mbp_post_type_settings'] = array( array(
                'name'              => 'post_types',
                'label'             => __( 'Enabled for post types', 'post-to-google-my-business' ),
                'desc'              => __( 'Select the post-types where the GMB metabox should be displayed', 'post-to-google-my-business' ),
                'type'              => 'multicheck',
                'default'           => array(
                'post' => 'post',
            ),
                'options'           => $this->settings_field_post_types(),
                'sanitize_callback' => array( $this, 'validate_post_types__premium_only' ),
            ) );
            return $fields;
        }
        
        public function settings_field_google_business( $args )
        {
            $value = $this->settings_api->get_option( $args['id'], $args['section'], $args['std'] );
            $name = sprintf( '%1$s[%2$s]', $args['section'], $args['id'] );
            //$user = $this->get_current_setting('google_user', 'mbp_google_settings');
            ?>
                <div class="mbp-google-settings-business-selector">
                    <?php 
            $this->business_selector->set_field_name( $name );
            $this->business_selector->set_selected_locations( $value );
            echo  $this->business_selector->location_blocked_info() ;
            echo  $this->business_selector->generate() ;
            echo  $this->business_selector->business_selector_controls() ;
            ?>
                </div>
                <br /><br />
				<?php 
            echo  $this->message_of_the_day() ;
        }
        
        protected function get_notification_count_html()
        {
            if ( mbp_fs()->is_in_trial_promotion() ) {
                return '';
            }
            $count = $this->notification_manager->notification_count( self::NOTIFICATION_SECTION );
            if ( $count >= 1 ) {
                return '<span class="update-plugins"><span class="update-count">' . $count . '</span></span>';
            }
            return '';
        }
        
        // <span class="update-plugins"><span class="update-count">1</span></span>
        public function add_menu()
        {
            add_menu_page(
                __( 'Post to Google My Business settings', 'post-to-google-my-business' ),
                sprintf( __( 'Post to GMB %s', 'post-to-google-my-business' ), $this->get_notification_count_html() ),
                'manage_options',
                'post_to_google_my_business',
                [ $this, 'admin_page' ],
                MBP_Plugin::dashicon()
            );
            $page = add_submenu_page(
                'post_to_google_my_business',
                __( 'Post to Google My Business settings', 'post-to-google-my-business' ),
                sprintf( __( 'Settings %s', 'post-to-google-my-business' ), $this->get_notification_count_html() ),
                'manage_options',
                'post_to_google_my_business',
                [ $this, 'admin_page' ]
            );
            //				$dashboard = add_submenu_page(
            //					'post_to_google_my_business',
            //					__('Post to Google My Business dashboard', 'post-to-google-my-business'),
            //					__('Dashboard', 'post-to-google-my-business'),
            //					'manage_options',
            //					$this::SETTINGS_PAGE.'#dashboard',
            //					array(&$this, 'admin_page')
            //                );
            add_action( "load-{$page}", [ $this, 'load_js' ] );
        }
        
        public function is_configured()
        {
            if ( $this->api_connected ) {
                return sprintf( '<br /><span class="dashicons dashicons-yes"></span> %s<br /><br />', __( 'Connected', 'post-to-google-my-business' ) );
            }
            return sprintf( '<br /><span class="dashicons dashicons-no"></span> %s<br /><br />', __( 'Not connected', 'post-to-google-my-business' ) );
        }
        
        public function admin_page()
        {
            if ( !current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
            }
            include plugin_dir_path( __FILE__ ) . '../templates/settings.php';
        }
        
        public function google_form_top()
        {
            echo  $this->is_configured() ;
            echo  $this->auth_urls() ;
            echo  '<br /><br />' ;
        }
        
        public function quick_post_top()
        {
        }
        
        public function settings_field_autopost_template( $args )
        {
            $value = $this->settings_api->get_option( $args['id'], $args['section'], $args['std'] );
            $name = sprintf( '%1$s[%2$s]', $args['section'], $args['id'] );
            //$user = $this->get_current_setting('google_user', 'mbp_google_settings');
            \PGMB\Components\PostEditor::draw( false, $value, $name );
        }
        
        public function validate_autopost_template( $value )
        {
            //print_r($value);
            //error_log($value);
            return $value;
        }
        
        public function debug_info()
        {
            
            if ( !class_exists( 'WP_Debug_Data' ) ) {
                $wp_debug_data_file = ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
                
                if ( !file_exists( $wp_debug_data_file ) ) {
                    _e( 'Your WordPress version does not yet support the WP_Debug_Data class, please update.', 'post-to-google-my-business' );
                    return false;
                }
                
                require_once $wp_debug_data_file;
            }
            
            _e( 'Please supply the debug data below with your support requests', 'post-to-google-my-business' );
            echo  "<br /><br />" ;
            wp_enqueue_style( 'site-health' );
            wp_enqueue_script( 'site-health' );
            //wp_enqueue_script('postbox');
            //WP_Debug_Data::check_for_updates();
            $info = WP_Debug_Data::debug_data();
            ?>
                    <div class="site-health-copy-buttons">
                        <div class="copy-button-wrapper">
                            <button type="button" class="button copy-button" data-clipboard-text="<?php 
            echo  esc_attr( WP_Debug_Data::format( $info, 'debug' ) ) ;
            ?>">
                                <?php 
            _e( 'Copy site info to clipboard' );
            ?>
                            </button>
                            <span class="success" aria-hidden="true"><?php 
            _e( 'Copied!' );
            ?></span>
                        </div>
                    </div>
                    <div id="health-check-debug" class="health-check-accordion">
                            <?php 
            $sizes_fields = array(
                'uploads_size',
                'themes_size',
                'plugins_size',
                'wordpress_size',
                'database_size',
                'total_size'
            );
            foreach ( $info as $section => $details ) {
                if ( !isset( $details['fields'] ) || empty($details['fields']) ) {
                    continue;
                }
                ?>

                                <h3 class="health-check-accordion-heading">
                                    <button aria-expanded="false" class="health-check-accordion-trigger" aria-controls="health-check-accordion-block-<?php 
                echo  esc_attr( $section ) ;
                ?>" type="button">
                                        <span class="title">
                                            <?php 
                echo  esc_html( $details['label'] ) ;
                ?>
                                            <?php 
                if ( isset( $details['show_count'] ) && $details['show_count'] ) {
                    printf( '(%d)', count( $details['fields'] ) );
                }
                ?>
                                        </span>
                                        <?php 
                if ( 'wp-paths-sizes' === $section ) {
                    ?>
                                            <span class="health-check-wp-paths-sizes spinner"></span>
                                            <?php 
                }
                ?>
                                        <span class="icon"></span>
                                    </button>
                                </h3>

                                <div id="health-check-accordion-block-<?php 
                echo  esc_attr( $section ) ;
                ?>" class="health-check-accordion-panel" hidden="hidden">
                                    <?php 
                if ( isset( $details['description'] ) && !empty($details['description']) ) {
                    printf( '<p>%s</p>', $details['description'] );
                }
                ?>
                                    <table class="widefat striped health-check-table" role="presentation">
                                        <tbody>
                                        <?php 
                foreach ( $details['fields'] as $field_name => $field ) {
                    
                    if ( is_array( $field['value'] ) ) {
                        $values = '<ul>';
                        foreach ( $field['value'] as $name => $value ) {
                            $values .= sprintf( '<li>%s: %s</li>', esc_html( $name ), esc_html( $value ) );
                        }
                        $values .= '</ul>';
                    } else {
                        $values = esc_html( $field['value'] );
                    }
                    
                    
                    if ( in_array( $field_name, $sizes_fields, true ) ) {
                        printf(
                            '<tr><td>%s</td><td class="%s">%s</td></tr>',
                            esc_html( $field['label'] ),
                            esc_attr( $field_name ),
                            $values
                        );
                    } else {
                        printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $field['label'] ), $values );
                    }
                
                }
                ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php 
            }
            ?>
                        </div>
                <?php 
        }
        
        public function auth_urls()
        {
            $configured = $this->api_connected;
            echo  sprintf(
                '<a href="%s" class="button%s">%s</a>',
                esc_url( admin_url( 'admin-post.php?action=mbp_generate_url' ) ),
                ( $configured ? '' : '-primary' ),
                ( $configured ? esc_html__( 'Reconnect to Google My Business', 'post-to-google-my-business' ) : esc_html__( 'Connect to Google My Business', 'post-to-google-my-business' ) )
            ) ;
            
            if ( $configured ) {
                echo  sprintf( '<br /><br /><a href="%s">%s</a>', esc_url( admin_url( 'admin-post.php?action=mbp_disconnect' ) ), esc_html__( 'Disconnect this website from Google My Business', 'post-to-google-my-business' ) ) ;
                echo  '<br /><br />' ;
                echo  sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin-post.php?action=mbp_revoke' ) ), esc_html__( 'Revoke Google account authorization', 'post-to-google-my-business' ) ) ;
            }
        
        }
        
        public function dashboard()
        {
            include dirname( __FILE__ ) . '/../templates/dashboard.php';
        }
        
        public function get_notifications()
        {
            foreach ( $this->notification_manager->get_notifications( self::NOTIFICATION_SECTION ) as $identifier => $data ) {
                $notification = new \PGMB\Notifications\BasicNotification( self::NOTIFICATION_SECTION, $identifier, $data );
                ?>
					<div class="pgmb-message pgmb-notification" data-section="<?php 
                echo  self::NOTIFICATION_SECTION ;
                ?>" data-identifier="<?php 
                echo  $identifier ;
                ?>">
						<button type="button" class="notice-dismiss mbp-notice-dismiss"><span class="screen-reader-text"><?php 
                _e( "Dismiss this notice.", "post-to-google-my-business" );
                ?></span></button>
						<img src="<?php 
                echo  plugins_url( "/../" . $notification->get_image(), __FILE__ ) ;
                ?>" alt="<?php 
                echo  $notification->get_alt() ;
                ?>" />
						<h3><?php 
                echo  $notification->get_title() ;
                ?></h3>
						<?php 
                echo  $notification->get_text() ;
                ?>
						<div class="clear"></div>
					</div>
					<?php 
            }
        }
        
        public function get_new_features()
        {
            foreach ( $this->notification_manager->get_notifications( self::NEW_FEATURES_SECTION ) as $identifier => $data ) {
                $new_feature = new \PGMB\Notifications\FeatureNotification( self::NEW_FEATURES_SECTION, $identifier, $data );
                ?>
				    <div class="pgmb-message pgmb-new-feature" data-section="<?php 
                echo  self::NEW_FEATURES_SECTION ;
                ?>" data-identifier="<?php 
                echo  $identifier ;
                ?>">
					    <button type="button" class="notice-dismiss mbp-notice-dismiss"><span class="screen-reader-text"><?php 
                _e( "Dismiss this notice.", "post-to-google-my-business" );
                ?></span></button>
                        <h3><?php 
                echo  $new_feature->get_title() ;
                ?></h3>
                        <img src="<?php 
                echo  plugins_url( "/../" . $new_feature->get_image(), __FILE__ ) ;
                ?>" alt="<?php 
                echo  $new_feature->get_alt() ;
                ?>" />
					    <?php 
                echo  $new_feature->get_text() ;
                ?>
                    </div>
					<?php 
            }
        }
        
        public function ajax_delete_notification()
        {
            $identifier = sanitize_key( $_REQUEST['identifier'] );
            $section = sanitize_key( $_REQUEST['section'] );
            $ignore = ( isset( $_REQUEST['ignore'] ) ? json_decode( $_REQUEST['ignore'] ) : false );
            $this->notification_manager->delete_notification( $section, $identifier, $ignore );
            wp_send_json_success();
        }
        
        public function message_of_the_day()
        {
            
            if ( !mbp_fs()->can_use_premium_code() ) {
                $messages = apply_filters( 'mbp_motd', array(
                    /*
                    sprintf('%s <a target="_blank" href="%s">%s</a> %s',
                    	__('Get more visitors to your website with a call-to-action button in your post.', 'post-to-google-my-business'),
                    	esc_url(admin_url('options-general.php?page=my_business_post-pricing')),
                    	__('Upgrade to Premium', 'post-to-google-my-business'),
                    	__('for call-to-action buttons, post statistics and more.', 'post-to-google-my-business')
                    )
                    */
                    sprintf(
                        '%s <a target="_blank" href="%s">%s</a> %s',
                        __( 'Manage multiple businesses or locations?', 'post-to-google-my-business' ),
                        mbp_fs()->get_upgrade_url(),
                        __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                        __( 'to pick a location per post, or post to multiple locations at once.', 'post-to-google-my-business' )
                    ),
                    sprintf(
                        '%s <a target="_blank" href="%s">%s</a> %s',
                        __( 'Not the right time?', 'post-to-google-my-business' ),
                        mbp_fs()->get_upgrade_url(),
                        __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                        __( 'and schedule your posts to be automagically published at a later time.', 'post-to-google-my-business' )
                    ),
                    sprintf(
                        '%s <a target="_blank" href="%s">%s</a> %s',
                        __( 'Wonder how well your Google My Business post is doing?', 'post-to-google-my-business' ),
                        mbp_fs()->get_upgrade_url(),
                        __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                        __( 'to view post statistics and easily include Google Analytics UTM parameters.', 'post-to-google-my-business' )
                    ),
                    sprintf(
                        '%s <a target="_blank" href="%s">%s</a> %s',
                        __( 'Use Post to Google My Business for your pages, projects, WooCommerce products and more.', 'post-to-google-my-business' ),
                        mbp_fs()->get_upgrade_url(),
                        __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                        __( 'to enable Post to Google my Business for any post type.', 'post-to-google-my-business' )
                    ),
                    sprintf(
                        '%s <a target="_blank" href="%s">%s</a> %s',
                        __( 'Automatically repost your GMB posts a specific or unlimited amount of times.', 'post-to-google-my-business' ),
                        mbp_fs()->get_upgrade_url(),
                        __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                        __( 'to set custom intervals and specify the amount of reposts.', 'post-to-google-my-business' )
                    ),
                    sprintf(
                        '%s <a target="_blank" href="https://wordpress.org/plugins/post-to-google-my-business/">%s</a> %s',
                        __( 'I hope you enjoy using my Post to Google My Business plugin! Help spread the word with a', 'post-to-google-my-business' ),
                        __( '5-star rating on WordPress.org', 'post-to-google-my-business' ),
                        __( '. Many thanks! - Koen Reus, plugin developer', 'post-to-google-my-business' )
                    ),
                    sprintf(
                        '%s <a target="_blank" href="%s">%s</a> %s',
                        __( 'Create unique posts every time.', 'post-to-google-my-business' ),
                        mbp_fs()->get_upgrade_url(),
                        __( 'Upgrade to Premium', 'post-to-google-my-business' ),
                        __( 'to use spintax and %variables% in your post text.', 'post-to-google-my-business' )
                    ),
                ) );
                //mt_srand(date('dmY'));
                $motd = mt_rand( 0, count( $messages ) - 1 );
                return '<span class="description">' . $messages[$motd] . '</span><br />';
            }
            
            return false;
        }
        
        public function gutenberg_post_types_info()
        {
            if ( !$this->gutenberg_post_types_without_fields ) {
                return;
            }
            echo  __( '* This post type does not support "custom-fields". Custom post types with the block editor/Gutenberg enabled need to support "custom-fields" in order for auto-post to work properly.', 'post-to-google-my-business' ) ;
        }
        
        public function settings_field_post_types()
        {
            $query_args = array(
                'public' => true,
            );
            //Maybe add some additional filtering later
            $post_types = array();
            foreach ( get_post_types( $query_args, 'objects' ) as $type ) {
                $unsupported_gutenberg = false;
                
                if ( post_type_supports( $type->name, 'editor' ) && !post_type_supports( $type->name, 'custom-fields' ) ) {
                    $this->gutenberg_post_types_without_fields = true;
                    $unsupported_gutenberg = true;
                }
                
                $post_types[$type->name] = $type->label . (( $unsupported_gutenberg ? '*' : '' ));
            }
            return $post_types;
        }
    
    }
}