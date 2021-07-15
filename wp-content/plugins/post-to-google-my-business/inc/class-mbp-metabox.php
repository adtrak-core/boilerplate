<?php

use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTime ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeInterface ;
use  PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone ;
if ( !class_exists( 'MBP_Metabox' ) ) {
    class MBP_Metabox
    {
        protected  $settings ;
        protected  $plugin_version ;
        private  $enabled_post_types = array() ;
        public  $post_editor ;
        const  AJAX_CALLBACK_PREFIX = 'mbp_metabox' ;
        /**
         * @var mixed|null
         */
        private  $is_gutenberg_autopost ;
        public function __construct( $plugin_version, MBP_Admin_Page_Settings $settings, $enabled_post_types )
        {
            //Todo: remove add_actions from constructor, remove dependencies on plugin and settings
            $this->settings = $settings;
            $this->plugin_version = $plugin_version;
            $this->enabled_post_types = $enabled_post_types;
            //add_action('init', array(&$this, 'init'));
            //add_action('admin_init', array(&$this, 'admin_init'));
            $this->post_editor = new \PGMB\Components\PostEditor( true, [], 'mbp_form_fields' );
        }
        
        public function init()
        {
            add_action(
                'save_post',
                array( &$this, 'save_autopost_template' ),
                10,
                3
            );
            add_action( 'before_delete_post', array( &$this, 'delete_subposts' ) );
            add_action( 'admin_init', [ $this, 'admin_init' ] );
            $this->register_gutenberg_meta();
            add_action( 'post_submitbox_misc_actions', [ $this, 'auto_post_checkbox' ] );
            $this->post_editor->register_ajax_callbacks( self::AJAX_CALLBACK_PREFIX );
            //Ajax actions
            add_action( 'wp_ajax_mbp_new_post', array( &$this, 'ajax_create_post' ) );
            add_action( 'wp_ajax_mbp_load_post', array( &$this, 'ajax_load_post' ) );
            add_action( 'wp_ajax_mbp_delete_post', array( &$this, 'ajax_delete_post' ) );
            add_action( 'wp_ajax_mbp_edit_post', array( &$this, 'ajax_edit_post' ) );
            add_action( 'wp_ajax_mbp_load_autopost_template', array( &$this, 'ajax_load_autopost_template' ) );
            add_action( 'wp_ajax_mbp_get_post_rows', [ $this, 'ajax_get_post_rows' ] );
            add_action( 'wp_ajax_mbp_get_created_posts', [ $this, 'ajax_created_posts_list' ] );
        }
        
        public function get_gmb_subposts_for_post( $parent_post_id )
        {
            return get_children( [
                'post_parent' => $parent_post_id,
                'post_type'   => MBP_Post_Type_Google_Subposts::POST_TYPE,
            ] );
        }
        
        public function delete_subposts( $post_id )
        {
            foreach ( $this->get_gmb_subposts_for_post( $post_id ) as $subpost ) {
                wp_delete_post( $subpost->ID, true );
            }
        }
        
        public function admin_init()
        {
            add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_metabox_scripts' ) );
            add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_gutenberg_assets' ] );
        }
        
        public function catch_rest_request( $prepared_post, WP_REST_Request $request )
        {
            //			error_log(print_r($prepared_post, true));
            //$gutenberg_autopost          = $request->get_param("isGutenbergPost");
            $this->is_gutenberg_autopost = $request->get_param( "isGutenbergPost" );
            return $prepared_post;
        }
        
        public function register_gutenberg_meta()
        {
            foreach ( $this->enabled_post_types as $post_type ) {
                //Gutenberg will throw an error if custom-fields is not enabled for the post type
                if ( !post_type_supports( $post_type, 'custom-fields' ) ) {
                    continue;
                }
                add_filter(
                    "rest_pre_insert_{$post_type}",
                    [ $this, 'catch_rest_request' ],
                    10,
                    2
                );
                register_meta( $post_type, '_mbp_gutenberg_autopost', [
                    'show_in_rest'      => true,
                    'type'              => 'boolean',
                    'single'            => true,
                    'sanitize_callback' => 'rest_sanitize_boolean',
                    'auth_callback'     => function () {
                    return current_user_can( 'edit_posts' );
                },
                ] );
            }
        }
        
        public function enqueue_gutenberg_assets()
        {
            /*
             * Not sure why this method isn't used in enqueue_metabox_scripts(), take care...
             */
            $post_type = get_post_type();
            if ( !in_array( $post_type, $this->enabled_post_types ) || !post_type_supports( $post_type, 'custom-fields' ) ) {
                return;
            }
            wp_enqueue_script(
                'mbp-gutenberg',
                plugins_url( '../js/gutenberg.js', __FILE__ ),
                [
                'jquery',
                'react',
                'wp-components',
                'wp-data',
                'wp-edit-post',
                'wp-element',
                'wp-i18n',
                'wp-plugins'
            ],
                $this->plugin_version,
                true
            );
            wp_localize_script( 'mbp-gutenberg', 'mbp_localize_gutenberg', [
                'checked_by_default' => $this->settings->get_current_setting( 'invert', 'mbp_quick_post_settings', 'off' ) == 'on',
                'post_id'            => get_the_ID(),
                'post_nonce'         => wp_create_nonce( 'mbp_post_nonce' ),
            ] );
        }
        
        public function enqueue_metabox_scripts( $hook )
        {
            if ( !in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
                return;
            }
            $screen = get_current_screen();
            if ( !is_object( $screen ) || !in_array( $screen->post_type, $this->enabled_post_types ) ) {
                return;
            }
            wp_enqueue_style( 'jquery-ui', plugins_url( '../css/jquery-ui.min.css', __FILE__ ) );
            $metabox_path = '../js/metabox.js';
            wp_enqueue_media();
            add_thickbox();
            wp_enqueue_script(
                'mbp-metabox',
                plugins_url( $metabox_path, __FILE__ ),
                array(
                'jquery',
                'jquery-ui-core',
                'jquery-ui-datepicker',
                'jquery-ui-slider'
            ),
                $this->plugin_version,
                true
            );
            $localize_vars = array(
                'post_id'                    => get_the_ID(),
                'post_nonce'                 => wp_create_nonce( 'mbp_post_nonce' ),
                'publish_confirmation'       => __( "You're working on a Google My Business post, but it has not yet been published/scheduled. Press OK to publish/schedule it now, or Cancel to save it as a draft.", 'post-to-google-my-business' ),
                'please_wait'                => __( 'Please Wait...', 'post-to-google-my-business' ),
                'publish_button'             => __( 'Publish', 'post-to-google-my-business' ),
                'update_button'              => __( 'Update', 'post-to-google-my-business' ),
                'draft_button'               => __( 'Save draft', 'post-to-google-my-business' ),
                'schedule_post'              => __( 'Schedule post', 'post-to-google-my-business' ),
                'save_template'              => __( 'Save template', 'post-to-google-my-business' ),
                'AJAX_CALLBACK_PREFIX'       => self::AJAX_CALLBACK_PREFIX,
                'POST_EDITOR_DEFAULT_FIELDS' => \PGMB\FormFields::default_post_fields(),
            );
            wp_localize_script( 'mbp-metabox', 'mbp_localize_script', $localize_vars );
        }
        
        public function auto_post_checkbox()
        {
            if ( !in_array( get_post_type(), $this->enabled_post_types ) ) {
                return;
            }
            ?>
	        <div class="misc-pub-section misc-pub-section-last mbp-autopost-checkbox-container">
		        <label><input type="checkbox" id="mbp_create_post" value="1" name="mbp_create_post" <?php 
            checked( $this->get_autopost_checkbox_checked() );
            ?>/>
			        <?php 
            _e( 'Auto-post to GMB', 'post-to-google-my-business' );
            ?>
		        </label>
	        </div>
	        <?php 
        }
        
        public function add_meta_boxes()
        {
            foreach ( $this->enabled_post_types as $post_type ) {
                add_meta_box(
                    "my_business_post_{$post_type}_section",
                    __( 'Post to Google My Business', 'post-to-google-my-business' ),
                    [ $this, 'add_inner_meta_boxes' ],
                    $post_type
                );
            }
        }
        
        public function add_inner_meta_boxes( $post )
        {
            
            if ( $this->settings->get_current_setting( 'google_location', 'mbp_google_settings' ) ) {
                ob_start();
                require_once dirname( __FILE__ ) . '/../templates/metabox.php';
                $output = ob_get_contents();
                ob_end_clean();
                echo  apply_filters( 'mbp_metabox_template', $output, $post ) ;
            } else {
                echo  sprintf( '<a href="%s">', esc_url( admin_url( 'admin.php?page=' . $this->settings->get_settings_page() ) ) ) ;
                _e( 'Please configure Post to Google My Business first', 'post-to-google-my-business' );
                echo  '</a> ' ;
                _e( '(Connect, pick a default location and Save Changes)', 'post-to-google-my-business' );
            }
        
        }
        
        /**
         * Return whether the Autopost checkbox has to be checked on the form
         *
         * @return mixed HTML content
         */
        public function get_autopost_checkbox_checked()
        {
            $checked = false;
            $current_screen = get_current_screen();
            $isNewPost = $current_screen->action === 'add';
            
            if ( $isNewPost ) {
                $checked = $this->settings->get_current_setting( 'invert', 'mbp_quick_post_settings', 'off' ) == 'on';
            } elseif ( !$isNewPost ) {
                $hasAutoPosted = get_post_meta( get_the_ID(), 'mbp_autopost_created', true );
                $isCheckboxChecked = $this->is_autopost_checkbox_checked( get_the_ID() );
                $checked = $isCheckboxChecked && !$hasAutoPosted;
            }
            
            return $checked;
        }
        
        /**
         * Determine whether the auto-post features should be enabled on the metabox
         *
         * @return bool Autopost is enabled
         * @since 2.2.11
         */
        public function is_autopost_enabled()
        {
            return true;
        }
        
        /**
         * Check if the post was created from the editor or through an external source
         *
         * mbp_wp_post isn't set when created externally
         *
         * @return bool Post was created through the editor
         */
        public function is_wp_post_submission()
        {
            if ( isset( $_POST['mbp_wp_post'] ) ) {
                return true;
            }
            return false;
        }
        
        /**
         * Check whether the auto-post checkbox was checked
         *
         * @param $post_id
         *
         * @return bool Checkbox checked
         */
        public function is_autopost_checkbox_checked( $post_id )
        {
            if ( get_post_meta( $post_id, 'mbp_autopost_checked', true ) ) {
                return true;
            }
            return false;
        }
        
        /**
         * Check if the post was submitted through the editor and save the autopost checkbox value
         *
         * @param $post_id
         * @since 2.2.11
         */
        public function save_autopost_checkbox_value( $post_id )
        {
            $submitted = $this->is_wp_post_submission();
            if ( !$submitted ) {
                return;
            }
            $gutenberg_checkbox = get_post_meta( $post_id, "_mbp_gutenberg_autopost", true );
            $checked = $gutenberg_checkbox || isset( $_POST['mbp_create_post'] ) && $_POST['mbp_create_post'];
            update_post_meta( $post_id, 'mbp_autopost_checked', $checked );
        }
        
        /**
         * Check if the post has a term that has auto-post enabled
         *
         * @param $post_id
         *
         * @return bool Post has term that has auto-post enabled
         * @since 2.2.11
         */
        public function has_autopost_term( $post_id )
        {
            return false;
        }
        
        /**
         * Check if an autopost has to be created for this post
         *
         * @param $post_id
         *
         * @return bool Autopost should be created
         * @since 2.2.11
         */
        public function should_create_autopost( $post_id )
        {
            //Check if the post was submitted through the editor
            $savedThroughEditor = $this->is_wp_post_submission();
            //Check if the default behaviour is to post
            $checkedByDefault = $this->settings->get_current_setting( 'invert', 'mbp_quick_post_settings', 'off' ) == 'on';
            //Check if the checkbox was checked on the form
            $checkboxChecked = $this->is_autopost_checkbox_checked( $post_id );
            //Check if the post has been published before
            $alreadyPublished = get_post_meta( $post_id, 'mbp_autopost_created', true );
            //Check if the post has a term that has auto-post enabled
            $hasAutopostTerm = $this->has_autopost_term( $post_id );
            
            if ( $savedThroughEditor && ($checkboxChecked || $hasAutopostTerm && !$alreadyPublished) ) {
                //If the post was created through the editor, and if the checkbox was checked, or has a term with autopost enabled
                return true;
            } elseif ( !$savedThroughEditor && !$alreadyPublished && ($checkedByDefault || $hasAutopostTerm) ) {
                //Post was not created through the editor, and hasn't already been posted
                //Check if the checkbox is checked by default, or the post has a term with autopost enabled
                return true;
            }
            
            return false;
        }
        
        /**
         * @param $post_id
         * @param $post
         * @param $update
         *
         * @return bool Autopost successfully created
         */
        public function save_autopost_template( $post_id, $post, $update )
        {
            $this->save_autopost_checkbox_value( $post_id );
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || $post->post_status != 'publish' || !in_array( $post->post_type, $this->enabled_post_types ) || $this->is_gutenberg_autopost ) {
                return false;
            }
            if ( !$this->should_create_autopost( $post_id ) ) {
                return false;
            }
            $subpost = new \PGMB\PostTypes\SubPost();
            $subpost->set_parent( $post_id );
            $savedAutopostTemplate = get_post_meta( $post_id, '_mbp_autopost_template', true );
            
            if ( $savedAutopostTemplate ) {
                $subpost->set_form_fields( $savedAutopostTemplate );
            } else {
                $defaultAutoPostTemplate = $this->settings->get_current_setting( 'autopost_template', 'mbp_quick_post_settings', \PGMB\FormFields::default_autopost_fields() );
                if ( empty($defaultAutoPostTemplate) ) {
                    $defaultAutoPostTemplate = \PGMB\FormFields::default_autopost_fields();
                }
                $subpost->set_form_fields( $defaultAutoPostTemplate );
            }
            
            $subpost->set_autopost();
            
            if ( !($subpost = apply_filters( 'mbp_autopost_before_insert_subpost', $subpost )) ) {
                return false;
                //Filter to alter or cancel the autopost
            }
            
            try {
                $child_post_id = wp_insert_post( $subpost->get_post_data(), true );
            } catch ( Exception $e ) {
                error_log( $e->getMessage() );
            }
            update_post_meta( $post_id, 'mbp_autopost_created', true );
            update_post_meta( $post_id, '_mbp_gutenberg_autopost', false );
            return true;
        }
        
        /**
         * The Google My Business post types
         *
         * @return array
         */
        public function gmb_topic_types()
        {
            return array(
                'STANDARD' => array(
                'name'     => __( 'What\'s New', 'post-to-google-my-business' ),
                'dashicon' => 'dashicons-megaphone',
            ),
                'EVENT'    => array(
                'name'     => __( 'Event', 'post-to-google-my-business' ),
                'dashicon' => 'dashicons-calendar',
            ),
                'OFFER'    => array(
                'name'     => __( 'Offer', 'post-to-google-my-business' ),
                'dashicon' => 'dashicons-tag',
            ),
                'PRODUCT'  => array(
                'name'     => __( 'Product', 'post-to-google-my-business' ),
                'dashicon' => 'dashicons-cart',
            ),
                'ALERT'    => [
                'name'     => __( 'COVID-19 update', 'post-to-google-my-business' ),
                'dashicon' => 'dashicons-sos',
            ],
            );
        }
        
        /**
         * Sanitize the form fields
         *
         * @param array $fields - Array containing the form fields
         *
         * @param array $textarea_fields - Fields that should be sanitized as textarea
         *
         * @param array $ignored_fields
         *
         * @return array - Sanitized form fields
         */
        public function sanitize_form_fields( $fields, $textarea_fields = array(), $ignored_fields = array() )
        {
            foreach ( $fields as $name => $value ) {
                if ( in_array( $name, $ignored_fields ) ) {
                    continue;
                }
                
                if ( is_array( $value ) ) {
                    $fields[$name] = array_map( 'sanitize_text_field', $value );
                    continue;
                }
                
                
                if ( in_array( $name, $textarea_fields ) ) {
                    $fields[$name] = sanitize_textarea_field( $value );
                    continue;
                }
                
                $fields[$name] = sanitize_text_field( $value );
            }
            return $fields;
        }
        
        /**
         * @param $parent_post_id
         * @param $fields
         *
         * @throws Exception Fields did not validate
         */
        public function validate_form_fields( $parent_post_id, $fields )
        {
            $api = MBP_api::getInstance();
            $parsed_fields = new \PGMB\ParseFormFields( $fields );
            $default_location = $this->settings->get_current_setting( 'google_location', 'mbp_google_settings' );
            $parsed_fields->getLocalPost( $api, $parent_post_id, $default_location );
        }
        
        public function wp_time_format()
        {
            $date_format = get_option( 'date_format' );
            $time_format = get_option( 'time_format' );
            return "{$date_format} {$time_format}";
        }
        
        public function get_existing_posts( $parent_id )
        {
            $args = array(
                'post_parent' => $parent_id,
                'post_type'   => MBP_Post_Type_Google_Subposts::POST_TYPE,
            );
            $posts = get_children( $args );
            $types = $this->gmb_topic_types();
            
            if ( is_array( $posts ) ) {
                foreach ( $posts as $post_id => $post ) {
                    $form_fields = get_post_meta( $post_id, 'mbp_form_fields', true );
                    //$location = get_post_meta($post_id, 'mbp_location', true);
                    $publishDate = false;
                    $has_error = get_post_meta( $post_id, 'mbp_last_error', true );
                    $publish_date_timestamp = get_post_meta( $post_id, '_mbp_post_publish_date', true );
                    
                    if ( $publish_date_timestamp ) {
                        $publish_DateTime = new WpDateTime();
                        $publish_DateTime->setTimestamp( $publish_date_timestamp );
                        $publish_DateTime->setTimezone( WpDateTimeZone::getWpTimezone() );
                    } else {
                        //Backwards compatibility
                        try {
                            $parsed_form_fields = new \PGMB\ParseFormFields( $form_fields );
                            $publish_DateTime = $parsed_form_fields->getPublishDateTime();
                        } catch ( Exception $exception ) {
                            $has_error = true;
                        }
                        
                        if ( !isset( $publish_DateTime ) || !$publish_DateTime instanceof DateTime ) {
                            $publish_DateTime = new WpDateTime();
                            $publish_DateTime->setTimestamp( get_post_time( 'U', true, $post_id ) );
                            $publish_DateTime->setTimezone( WpDateTimeZone::getWpTimezone() );
                        }
                    
                    }
                    
                    echo  $this->create_table_row(
                        $post_id,
                        $types[$form_fields['mbp_topic_type']]['dashicon'],
                        $types[$form_fields['mbp_topic_type']]['name'],
                        get_post_time( 'U', true, $post_id ),
                        $publish_DateTime,
                        isset( $form_fields['mbp_repost'] ) && $form_fields['mbp_repost'],
                        $has_error
                    ) ;
                }
                echo  '<tr class="no-items' . (( count( $posts ) >= 1 ? ' hidden' : '' )) . '"><td class="colspanchange" colspan="3">' . __( 'No GMB posts found.', 'post-to-google-my-business' ) . '</td></tr>' ;
            }
        
        }
        
        public function create_table_row(
            $post_id,
            $dashicon,
            $topicType,
            $created,
            $publish_date,
            $repost = false,
            $has_error = false
        )
        {
            $status = get_post_status( $post_id );
            $working = false;
            //	        $batch_key = get_post_meta($post_id, '_mbp_post_batch_key', true);
            //
            //            if($batch_key && !empty(get_option($batch_key, false))){
            //	            $working = true;
            //            }
            //$working = get_post_meta($post_id, '_mbp_worker_busy', true);
            $show_post_list_button = false;
            $publish_output = '-';
            
            if ( $status !== 'draft' && $publish_date instanceof WpDateTimeInterface ) {
                $publish_output = '<span class="dashicons dashicons-clock"></span>';
                $now = new DateTime( 'now', WpDateTimeZone::getWpTimezone() );
                
                if ( $publish_date < $now ) {
                    $publish_output = '<span class="dashicons dashicons-admin-site"></span>';
                    $show_post_list_button = true;
                }
                
                $publish_output .= $publish_date->formatDate() . ' ' . $publish_date->formatTime();
            }
            
            $posts_have_error = !empty(get_post_meta( $post_id, 'mbp_errors', true ));
            if ( $posts_have_error ) {
                $has_error = true;
            }
            $table_row = '
            <tr data-postid="' . $post_id . '"  class="mbp-post ' . (( $has_error ? ' mbp-has-error' : '' )) . '">
                <td>
                    
                    ' . (( $repost ? '<span class="dashicons dashicons-controls-repeat" title="' . __( 'Repost enabled', 'post-to-google-my-business' ) . '"></span> ' : '' )) . '
                    <a href="#" class="row-title mbp-action" data-action="edit"><span class="dashicons ' . $dashicon . '"></span> ' . $topicType . '</a>' . (( $status == 'draft' ? ' [DRAFT]' : '' )) . '
                    ' . (( $working ? '[<span class="spinner is-active"></span> Working...]' : '' )) . '
                    <br />
                    <div class="row-actions">
                        <span class="list">
                        <a href="#" data-action="postlist" class="mbp-action">' . __( 'List created posts', 'post-to-google-my-business' ) . '
                        ' . (( $posts_have_error ? '<span class="dashicons dashicons-warning"></span> ' : '' )) . '</a> | </span>
                        <span class="edit"><a href="#" data-action="edit" class="mbp-action">' . __( 'Edit', 'post-to-google-my-business' ) . '</a> | </span>
                        <span class="duplicate"><a href="#" data-action="duplicate" class="mbp-action">' . __( 'Duplicate', 'post-to-google-my-business' ) . '</a> | </span>
                        <span class="trash"><a href="#" data-action="trash" class="submitdelete mbp-action">' . __( 'Delete', 'post-to-google-my-business' ) . '</a></span>
                    </div>			
                </td>
                <td>' . $publish_output . '</td>
                <td>' . (( $status !== 'draft' ? sprintf( _x( '%s ago', '%s = human-readable time difference', 'post-to-google-my-business' ), human_time_diff( $created ) ) : __( 'Draft', 'post-to-google-my-business' ) )) . '</td>
            </tr>';
            return apply_filters( 'mbp_create_table_row', $table_row, $post_id );
        }
        
        public function ajax_get_post_rows()
        {
            check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
            $parent_post_id = intval( $_POST['mbp_post_id'] );
            ob_start();
            $this->get_existing_posts( $parent_post_id );
            $rows = ob_get_contents();
            ob_end_clean();
            wp_send_json_success( [
                'rows' => $rows,
            ] );
        }
        
        /**
         * Handle AJAX post submission
         */
        public function ajax_create_post()
        {
            check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
            $parent_post_id = intval( $_POST['mbp_post_id'] );
            if ( !current_user_can( 'publish_posts', $parent_post_id ) ) {
                wp_send_json_error( array(
                    'error' => __( 'You do not have permission to publish posts', 'post-to-google-my-business' ),
                ) );
            }
            $editing = $child_post_id = ( isset( $_POST['mbp_editing'] ) && is_numeric( $_POST['mbp_editing'] ) ? intval( $_POST['mbp_editing'] ) : false );
            $draft = isset( $_POST['mbp_draft'] ) && json_decode( $_POST['mbp_draft'] );
            $data_mode = sanitize_text_field( $_POST['mbp_data_mode'] );
            //$form_fields = $this->sanitize_form_fields($_POST['mbp_form_fields'], ['mbp_post_text']);
            parse_str( $_POST['mbp_serialized_fieldset'], $parsed_fieldset );
            $form_fields = $this->sanitize_form_fields( $parsed_fieldset['mbp_form_fields'], [ 'mbp_post_text' ], [ 'mbp_button_url', 'mbp_offer_redeemlink', 'mbp_post_attachment' ] );
            $types = $this->gmb_topic_types();
            $json_args = [];
            switch ( $data_mode ) {
                case "save_draft":
                case "edit_post":
                case "create_post":
                    $subpost = new \PGMB\PostTypes\SubPost();
                    if ( $editing ) {
                        $subpost->set_editing( $child_post_id );
                    }
                    $subpost->set_parent( $parent_post_id );
                    $subpost->set_form_fields( $form_fields );
                    $subpost->set_draft( $draft );
                    try {
                        $this->validate_form_fields( $parent_post_id, $form_fields );
                        $child_post_id = wp_insert_post( $subpost->get_post_data(), true );
                    } catch ( Exception $e ) {
                        wp_send_json_error( array(
                            'error' => sprintf( __( 'Error creating post: %s', 'post-to-google-my-business' ), $e->getMessage() ),
                        ) );
                    }
                    $parsed_form_fields = new \PGMB\ParseFormFields( $form_fields );
                    
                    if ( $draft ) {
                        $scheduled_date = null;
                    } else {
                        $scheduled_date = $parsed_form_fields->getPublishDateTime();
                        if ( !$scheduled_date instanceof WpDateTimeInterface ) {
                            $scheduled_date = new WpDateTime( 'now', WpDateTimeZone::getWpTimezone() );
                        }
                    }
                    
                    $json_args = array(
                        'id'  => $child_post_id,
                        'row' => $this->create_table_row(
                        $child_post_id,
                        $types[$form_fields['mbp_topic_type']]['dashicon'],
                        $types[$form_fields['mbp_topic_type']]['name'],
                        get_post_time( 'U', true, $child_post_id ),
                        $scheduled_date,
                        isset( $form_fields['mbp_repost'] ) && $form_fields['mbp_repost']
                    ),
                    );
                    break;
                case "edit_template":
                    update_post_meta( $parent_post_id, '_mbp_autopost_template', $form_fields );
                    $json_args = [
                        'message' => __( 'Auto-post template successfully updated', 'post-to-google-my-business' ),
                    ];
                    break;
            }
            wp_send_json_success( $json_args );
        }
        
        public function ajax_load_post()
        {
            check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
            $post_id = (int) $_POST['mbp_post_id'];
            if ( !current_user_can( 'edit_posts', $post_id ) ) {
                wp_send_json( array(
                    'error' => __( 'You do not have permission to edit posts', 'post-to-google-my-business' ),
                ) );
            }
            $form_fields = get_post_meta( $post_id, 'mbp_form_fields', true );
            $has_error = get_post_meta( $post_id, 'mbp_last_error', true );
            
            if ( $form_fields && is_array( $form_fields ) ) {
                wp_send_json( array(
                    'success'   => true,
                    'post'      => array(
                    'form_fields' => $form_fields,
                    'post_status' => get_post_status( $post_id ),
                ),
                    'has_error' => $has_error,
                ) );
            } else {
                wp_send_json( array(
                    'error' => __( 'Post could not be loaded', 'post-to-google-my-business' ),
                ) );
            }
        
        }
        
        public function ajax_delete_post()
        {
            check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
            $post_id = (int) $_POST['mbp_post_id'];
            if ( !current_user_can( 'delete_posts', $post_id ) ) {
                wp_send_json( array(
                    'error' => __( 'You do not have permission to delete posts', 'post-to-google-my-business' ),
                ) );
            }
            wp_delete_post( $post_id );
            wp_send_json_success();
        }
        
        public function ajax_load_autopost_template()
        {
            check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
            if ( empty($_POST['mbp_post_id']) ) {
                wp_send_json_error( [
                    'error' => __( 'Invalid post ID', 'post-to-google-my-business' ),
                ] );
            }
            $post_id = intval( $_POST['mbp_post_id'] );
            if ( $fields = get_post_meta( $post_id, '_mbp_autopost_template', true ) ) {
                wp_send_json_success( [
                    'fields' => $fields,
                ] );
            }
            $template = $this->settings->get_current_setting( 'autopost_template', 'mbp_quick_post_settings', \PGMB\FormFields::default_autopost_fields() );
            if ( empty($template) ) {
                $template = \PGMB\FormFields::default_autopost_fields();
            }
            wp_send_json_success( [
                'fields' => $template,
            ] );
        }
        
        public function ajax_created_posts_list()
        {
            check_ajax_referer( 'mbp_post_nonce', 'mbp_post_nonce' );
            $post_id = intval( $_REQUEST['mbp_post_id'] );
            $fields = new \PGMB\ParseFormFields( get_post_meta( $post_id, 'mbp_form_fields', true ) );
            $api = MBP_api::getInstance();
            $created_posts = get_post_meta( $post_id, 'mbp_posts', true );
            $post_errors = get_post_meta( $post_id, 'mbp_errors', true );
            if ( !is_array( $created_posts ) || !is_array( $post_errors ) || empty($created_posts) && empty($post_errors) ) {
                wp_send_json_success( [
                    'table' => sprintf( '<tr><td colspan="2">%s</td></tr>', __( 'No posts found. The posting process may still be in progress, or no location was selected.', 'post-to-google-my-business' ) ),
                ] );
            }
            $locations = array_merge( $post_errors, $created_posts );
            $rows = '';
            foreach ( $locations as $location => $item ) {
                $location_info = $api->get_location( $location );
                
                if ( is_wp_error( $item ) ) {
                    $locationCell = $location_info->locationName;
                    $statusCell = $item->get_error_message();
                } else {
                    $locationCell = sprintf( "<a href='%s' target='_blank'>%s <span class=\"dashicons dashicons-external\"></span></a>", $item['searchUrl'], $location_info->locationName );
                    $statusCell = ( !empty($item['state']) ? $item['state'] : __( 'Unknown', 'post-to-google-my-business' ) );
                }
                
                $rows .= "\n                    <tr>\n                        <td>{$locationCell}</td>\n                        <td>{$statusCell}</td>\n                    </tr>\n                    ";
            }
            wp_send_json_success( [
                'table' => $rows,
            ] );
        }
    
    }
}