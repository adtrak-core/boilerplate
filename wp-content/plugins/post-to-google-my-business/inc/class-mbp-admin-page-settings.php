<?php

use  PGMB\WeDevsSettingsAPI ;
if ( !class_exists( 'MBP_Admin_Page_Settings' ) ) {
    class MBP_Admin_Page_Settings
    {
        const  SETTINGS_PAGE = 'post_to_google_my_business_settings' ;
        private  $settings_api ;
        protected  $plugin ;
        public function __construct( MBP_Plugin $plugin, WeDevsSettingsAPI $settings_api )
        {
            $this->settings_api = $settings_api;
            $this->plugin = $plugin;
        }
        
        public function init()
        {
            add_action( 'admin_init', array( &$this, 'admin_init' ) );
            add_action( 'admin_menu', array( &$this, 'add_menu' ) );
            add_action( 'wp_ajax_mbp_get_businesses', array( &$this, 'get_businesses_ajax' ) );
        }
        
        public function admin_init()
        {
            $this->settings_api->set_sections( $this->get_settings_sections() );
            $this->settings_api->set_fields( $this->get_settings_fields() );
            $this->settings_api->admin_init();
            add_action( 'wsa_form_top_mbp_google_settings', array( &$this, 'google_form_top' ) );
            add_action( 'wsa_form_top_mbp_quick_post_settings', array( &$this, 'quick_post_top' ) );
            add_action( 'wsa_form_bottom_mbp_debug_info', array( &$this, 'debug_info' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
            if ( !mbp_fs()->is_plan_or_trial( 'pro' ) ) {
                add_action( 'wsa_form_bottom_mbp_post_type_settings', array( &$this, 'post_type_bottom' ) );
            }
            //add_action('wsa_form_bottom_mbp_google_settings', array(&$this, 'google_form_bottom'));
        }
        
        public function enqueue_scripts( $hook )
        {
            /* selective loading of JS has issues when the plugin is translated
            				if(!in_array($hook, array(
            							'toplevel_page_post_to_google_my_business',
            							'post-to-gmb_page_post_to_google_my_business_settings'
            						)
            					)
            				){
            					return;
            				}
            
            				$screen = get_current_screen();
            				if(!is_object($screen)){
            					return;
            				}
            				*/
            wp_enqueue_script(
                'mbp-settings-page',
                plugins_url( '../js/admin.js', __FILE__ ),
                array( 'jquery' ),
                $this->plugin->version(),
                true
            );
            $localize_vars = [
                'refresh_locations' => __( 'Refresh locations', 'post-to-google-my-business' ),
                'please_wait'       => __( 'Please wait...', 'post-to-google-my-business' ),
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
                'mbp_quick_post_settings' => array(
                array(
                'name'              => 'template',
                'label'             => __( 'Quick post template', 'post-to-google-my-business' ),
                'desc'              => sprintf( __( 'The template for new Google posts when using quick post. Supports <a target="_blank" href="%s">variables</a> and <a target="_blank" href="%s">spintax</a> (premium only)', 'post-to-google-my-business' ), 'https://tycoonmedia.net/blog/using-the-quick-publish-feature/', 'https://tycoonmedia.net/blog/using-spintax/' ),
                'type'              => 'textarea',
                'sanitize_callback' => array( &$this, 'validate_quick_post_template' ),
                'default'           => __( 'New post: %post_title% - %post_content%', 'post-to-google-my-business' ),
            ),
                array(
                'name'    => 'cta',
                'label'   => __( 'Default call to action', 'post-to-google-my-business' ),
                'desc'    => __( 'The default button text', 'post-to-google-my-business' ),
                'type'    => 'select',
                'default' => 'LEARN_MORE',
                'options' => array(
                'NONE'       => __( 'No button', 'post-to-google-my-business' ),
                'BOOK'       => __( 'Book', 'post-to-google-my-business' ),
                'ORDER'      => __( 'Order', 'post-to-google-my-business' ),
                'SHOP'       => __( 'Shop', 'post-to-google-my-business' ),
                'LEARN_MORE' => __( 'Learn more', 'post-to-google-my-business' ),
                'SIGN_UP'    => __( 'Sign up', 'post-to-google-my-business' ),
                'GET_OFFER'  => __( 'Get offer', 'post-to-google-my-business' ),
                'CALL'       => __( 'Call Now', 'post-to-google-my-business' ),
            ),
            ),
                array(
                'name'  => 'invert',
                'label' => __( 'Post to GMB by default', 'post-to-google-my-business' ),
                'desc'  => __( 'The Auto-post checkbox will be checked by default, and your WordPress posts will be automatically published to GMB, unless you uncheck it.', 'post-to-google-my-business' ),
                'type'  => 'checkbox',
            ),
                array(
                'name'  => 'fetch_content_image',
                'label' => __( 'Fetch post image from content', 'post-to-google-my-business' ),
                'desc'  => __( 'Try to get an image from the post content (when no custom image is set). This has priority over the featured image.', 'post-to-google-my-business' ),
                'type'  => 'checkbox',
            ),
                array(
                'name'    => 'use_featured_image',
                'label'   => __( 'Use featured image', 'post-to-google-my-business' ),
                'desc'    => __( 'Use the Featured Image as GMB Post image (when no custom image is set)', 'post-to-google-my-business' ),
                'type'    => 'checkbox',
                'default' => 'on',
            )
            ),
            );
            return $fields;
        }
        
        public function settings_field_google_user( $args )
        {
            $value = esc_attr( $this->settings_api->get_option( $args['id'], $args['section'], $args['std'] ) );
            $size = ( isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular' );
            echo  sprintf(
                '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">',
                $size,
                $args['section'],
                $args['id']
            ) ;
            
            if ( $this->plugin->is_configured() ) {
                $api = MBP_api::getInstance();
                $accounts = $api->get_accounts();
                
                if ( $accounts && count( $accounts->accounts ) >= 1 ) {
                    echo  sprintf( '<option disabled selected value>%s</option>', esc_html__( 'Select a user or location group', 'post-to-google-my-business' ) ) ;
                    foreach ( $accounts->accounts as $account ) {
                        
                        if ( $account->name == $value ) {
                            $selected = true;
                        } else {
                            $selected = false;
                        }
                        
                        echo  sprintf(
                            '<option value="%s"%s>%s</option>',
                            $account->name,
                            ( $selected ? ' selected="selected"' : '' ),
                            $account->accountName
                        ) ;
                    }
                } else {
                    echo  sprintf( '<option disabled selected value>%s</option>', esc_html__( 'No user accounts found', 'post-to-google-my-business' ) ) ;
                }
            
            } else {
                echo  sprintf( '<option disabled selected value>%s</option>', esc_html__( 'Connect your Google account first.', 'post-to-google-my-business' ) ) ;
            }
            
            echo  '</select>' ;
        }
        
        public function settings_field_google_business( $args )
        {
            $value = $this->settings_api->get_option( $args['id'], $args['section'], $args['std'] );
            $name = sprintf( '%1$s[%2$s]', $args['section'], $args['id'] );
            //$user = $this->get_current_setting('google_user', 'mbp_google_settings');
            ?>
				<div class="mbp-info mbp-location-blocked-info">
					<strong><?php 
            _e( 'Location grayed out?', 'post-to-google-my-business' );
            ?></strong>
					<?php 
            _e( 'It means the location is blocked from using the LocalPostAPI, and can\'t be posted to using the plugin.', 'post-to-google-my-business' );
            ?>
					<a href="https://wordpress.org/plugins/post-to-google-my-business/#why%20is%2Fare%20my%20location(s)%20grayed%20out%3F" target="_blank"><?php 
            _e( 'Learn more...', 'post-to-google-my-business' );
            ?></a>
				</div>

				<?php 
            \PGMB\Components\BusinessSelector::draw(
                MBP_api::getInstance(),
                $name,
                $value,
                false,
                false
            );
            if ( $this->plugin->is_configured() ) {
                echo  '<br /><a class="button" href="#" id="refresh-api-cache">' . esc_html__( 'Refresh locations', 'post-to-google-my-business' ) . '</a>' ;
            }
            ?>
					<br /><br />
				<?php 
            echo  $this->plugin->message_of_the_day() ;
        }
        
        public function add_menu()
        {
            add_menu_page(
                __( 'Post to Google My Business settings', 'post-to-google-my-business' ),
                __( 'Post to GMB', 'post-to-google-my-business' ),
                'publish_posts',
                'post_to_google_my_business',
                array( &$this, 'admin_page' ),
                MBP_Plugin::dashicon()
            );
            add_submenu_page(
                'post_to_google_my_business',
                __( 'Post to Google My Business settings', 'post-to-google-my-business' ),
                __( 'Settings', 'post-to-google-my-business' ),
                'manage_options',
                $this::SETTINGS_PAGE,
                array( &$this, 'admin_page' )
            );
        }
        
        public function is_configured()
        {
            if ( $this->plugin->is_configured() ) {
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
        
        public function post_type_bottom()
        {
            echo  sprintf( __( 'Support for other post types is a <a href="%s">Pro feature</a>.', 'post-to-google-my-business' ), mbp_fs()->get_upgrade_url() ) ;
        }
        
        public function quick_post_top()
        {
            //echo __('Quick post allows you to create posts on Google My Business based on the template below.', 'post-to-google-my-business');
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
            $configured = $this->plugin->is_configured();
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
        
        public function get_businesses_ajax()
        {
            //$user = sanitize_text_field($_POST['user_id']);
            $refresh = ( $_POST['refresh'] == "true" ? true : false );
            //$selected = sanitize_text_field($_POST['selected']);
            //echo $this->plugin->business_selector('mbp_google_settings[google_location]', null, null, true);
            $default_location = $this->get_current_setting( 'google_location', 'mbp_google_settings' );
            $business_selector = new \PGMB\Components\BusinessSelector(
                MBP_api::getInstance(),
                'mbp_google_settings[google_location]',
                false,
                $default_location,
                false
            );
            $business_selector->flush_cache();
            echo  $business_selector->generate() ;
            wp_die();
        }
        
        public function validate_quick_post_template( $value )
        {
            
            if ( empty($value) ) {
                add_settings_error(
                    'template',
                    'mbp_quick_post_error',
                    'The quick post template can not be empty',
                    'error'
                );
                return 'New post: %post_title% - %post_content%';
            }
            
            return $value;
        }
    
    }
}