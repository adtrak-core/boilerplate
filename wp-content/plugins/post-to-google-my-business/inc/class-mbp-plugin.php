<?php

use  PGMB\WeDevsSettingsAPI ;
class MBP_Plugin
{
    const  PLUGIN_VERSION = '2.2.26' ;
    protected  $settings_page ;
    private  $taxonomy_fields = array() ;
    private  $enabled_post_types = array() ;
    private  $enabled_taxonomies = array() ;
    private  $notification_manager ;
    private  $metabox ;
    public function init()
    {
        $connector = new MBP_connector( $this );
        $connector->init();
        $this->notification_manager = new \PGMB\Notifications\NotificationManager( "mbp" );
        $this->settings_page = new MBP_Admin_Page_Settings( self::PLUGIN_VERSION, $this->is_configured(), $this->notification_manager );
        $this->settings_page->init();
        $defaultLocation = $this->settings_page->get_current_setting( 'google_location', 'mbp_google_settings' );
        
        if ( $defaultLocation ) {
            $postPublishProcess = new \PGMB\BackgroundProcessing\PostPublishProcess();
            $GooglePostManager = \PGMB\BackgroundProcessing\GooglePostManager::init( $postPublishProcess, MBP_api::getInstance(), $defaultLocation );
        }
        
        $post_type_google_subposts = new MBP_Post_Type_Google_Subposts();
        $this->register_enabled_post_types();
        $this->metabox = new MBP_Metabox( self::PLUGIN_VERSION, $this->settings_page, $this->enabled_post_types );
        $this->metabox->init();
        add_action( 'admin_init', array( &$this, 'admin_init' ) );
        $this->show_welcome_message();
        add_action( 'admin_init', [ $this, 'show_review_notifications' ] );
        $this->do_upgrades();
    }
    
    public function register_post_type( $post_type )
    {
        if ( is_string( $post_type ) ) {
            $post_type = new $post_type();
        }
        if ( !$post_type instanceof \PGMB\PostTypes\PostTypeDefinition ) {
            throw new InvalidArgumentException( 'Post type definition must implement the PostTypeDefinition interface' );
        }
        //register_post_type($post_type::POST_TYPE, $post_type->post_type_data());
    }
    
    /**
     * Get an array of registered taxonomy fields so they can be referenced/removed outside of the plugin
     *
     * @return array Registered taxonomy fields
     *
     * @since 2.2.11
     */
    public function get_registered_taxonomy_fields()
    {
        return $this->taxonomy_fields;
    }
    
    public function admin_init()
    {
        add_action( 'admin_enqueue_scripts', array( &$this, 'load_admin_styles' ) );
        
        if ( is_admin() && ($error = get_transient( 'mbp_api_error' )) ) {
            MBP_Admin_Notices::getInstance()->error( $error, 'mbp_error' );
            delete_transient( 'mbp_api_error' );
        }
    
    }
    
    public function load_admin_styles()
    {
        wp_enqueue_style(
            'mbp_admin_styles',
            plugins_url( '../css/style.css', __FILE__ ),
            array(),
            self::PLUGIN_VERSION
        );
    }
    
    /**
     * Shows a welcome notification to the user
     *
     * @since 2.2.12
     */
    public function show_welcome_message()
    {
        $has_shown_welcome_message = get_option( 'mbp_welcome_message' );
        
        if ( !$this->is_configured() && !$has_shown_welcome_message ) {
            $current_user = wp_get_current_user();
            $welcome_message = PGMB\Notifications\BasicNotification::create(
                \MBP_Admin_Page_Settings::NOTIFICATION_SECTION,
                'welcome_message',
                esc_html__( 'Getting started with Post to Google My Business', 'post-to-google-my-business' ),
                nl2br( sprintf(
                esc_html__( "Hi %1\$s,\n\nThanks for installing Post to Google My Business! To get started, connect the plugin to your Google account on the Google settings tab.\n\nNeed help? Check out the %2\$s\n\n%3\$s" ),
                esc_html( $current_user->display_name ),
                sprintf( '<a target="_blank" href="%s">%s</a>', 'https://tycoonmedia.net/gmb-tutorial-video/', esc_html__( 'tutorial video', 'post-to-google-my-business' ) ),
                sprintf( '<strong>%s</strong><br /><i>%s</i>', 'Koen', esc_html__( 'Plugin Developer', 'post-to-google-my-business' ) )
            ) ),
                'img/koen.png',
                esc_html__( 'Developer profile photo', 'post-to-google-my-business' )
            );
            $this->notification_manager->add_notification( $welcome_message );
        }
        
        update_option( 'mbp_welcome_message', true );
    }
    
    /**
     * Shows review notifications in the plugin dashboard
     *
     * @since 2.2.12
     */
    public function show_review_notifications()
    {
        $posts_count = wp_count_posts( \PGMB\PostTypes\SubPost::POST_TYPE )->publish;
        if ( !$posts_count == 7 || !$posts_count == 28 || !$posts_count == 100 ) {
            return;
        }
        $current_user = wp_get_current_user();
        $shown_review_notifications = get_option( 'mbp_review_notifications' );
        if ( !is_array( $shown_review_notifications ) ) {
            $shown_review_notifications = [
                '7_posts'   => false,
                '28_posts'  => false,
                '100_posts' => false,
            ];
        }
        $wordpress_repository_link = sprintf( '<a target="_blank" href="%s">%s</a>', 'https://wordpress.org/plugins/post-to-google-my-business/#reviews', esc_html__( 'WordPress Repository', 'post-to-google-my-business' ) );
        $plugin_developer = sprintf( '<strong>%s</strong><br /><i>%s</i>', 'Koen', esc_html__( 'Plugin Developer', 'post-to-google-my-business' ) );
        $you_deserve_it = sprintf( '<a target="_blank" href="%s"><strong>%s</strong></a>', 'https://wordpress.org/plugins/post-to-google-my-business/#reviews', esc_html__( 'Ok, you deserve it', 'post-to-google-my-business' ) );
        $maybe_later = sprintf( '<a class="mbp-notice-dismiss" href="#">%s</a>', esc_html__( 'Maybe later', 'post-to-google-my-business' ) );
        $already_did = sprintf( '<a class="mbp-notice-dismiss" href="#" data-ignore="true">%s</a>', esc_html__( 'I already did', 'post-to-google-my-business' ) );
        $user_display_name = esc_html( $current_user->display_name );
        $avatar = 'img/koen.png';
        $alt = esc_html__( 'Developer profile photo', 'post-to-google-my-business' );
        $identifier = 'review_notification';
        
        if ( $posts_count == 7 && !$shown_review_notifications['7_posts'] ) {
            $review_notification = PGMB\Notifications\BasicNotification::create(
                \MBP_Admin_Page_Settings::NOTIFICATION_SECTION,
                $identifier,
                esc_html__( '7 posts!', 'post-to-google-my-business' ),
                nl2br( sprintf(
                esc_html__( "Hey %1\$s,\n\nI noticed you've just created your 7th GMB post through my plugin, awesome!\n\nIf you like the plugin and have a moment to leave a 5-star rating on the %2\$s, that really helps spread the word and move the development forwards!\n\n%3\$s\n\n%4\$s\n%5\$s\n%6\$s" ),
                $user_display_name,
                $wordpress_repository_link,
                $plugin_developer,
                $you_deserve_it,
                $maybe_later,
                $already_did
            ) ),
                $avatar,
                $alt
            );
            $this->notification_manager->add_notification( $review_notification );
            $shown_review_notifications['7_posts'] = true;
        } elseif ( $posts_count == 28 && !$shown_review_notifications['28_posts'] ) {
            $review_notification = PGMB\Notifications\BasicNotification::create(
                \MBP_Admin_Page_Settings::NOTIFICATION_SECTION,
                $identifier,
                esc_html__( '28 posts! Awesome', 'post-to-google-my-business' ),
                nl2br( sprintf(
                esc_html__( "Hi %1\$s,\n\nI hope you find my GMB plugin useful! I noticed you've already created 28 GMB posts with it.\n\nIf you like the plugin and have a moment to leave a 5-star rating on the %2\$s, that really helps boost my motivation!\n\nThanks!\n\n%3\$s\n\n%4\$s\n%5\$s\n%6\$s" ),
                $user_display_name,
                $wordpress_repository_link,
                $plugin_developer,
                $you_deserve_it,
                $maybe_later,
                $already_did
            ) ),
                $avatar,
                $alt
            );
            $this->notification_manager->add_notification( $review_notification );
            $shown_review_notifications['28_posts'] = true;
        } elseif ( $posts_count == 100 && !$shown_review_notifications['100_posts'] ) {
            $review_notification = PGMB\Notifications\BasicNotification::create(
                \MBP_Admin_Page_Settings::NOTIFICATION_SECTION,
                $identifier,
                esc_html__( 'Wow! 100 posts!', 'post-to-google-my-business' ),
                nl2br( sprintf(
                esc_html__( "Hi %1\$s,\n\nYou've just published your 100th GMB post through the Post to Google My Business plugin, awesome!\n\nIf you find the plugin useful and have a moment to leave a 5-star rating on the %2\$s, you'd do me a BIG favour and it really motivates me to continue adding awesome features!\n\nDon't worry, this will be the last time I bother you about it.\n\nThanks!\n\n%3\$s\n\n%4\$s\n%5\$s\n%6\$s" ),
                $user_display_name,
                $wordpress_repository_link,
                $plugin_developer,
                $you_deserve_it,
                $maybe_later,
                $already_did
            ) ),
                $avatar,
                $alt
            );
            $this->notification_manager->add_notification( $review_notification );
            $shown_review_notifications['100_posts'] = true;
        }
        
        update_option( 'mbp_review_notifications', $shown_review_notifications );
    }
    
    /**
     * Perform tasks upon plugin activation
     *
     * @param bool $network_wide Plugin is network activated
     */
    public static function activate( $network_wide )
    {
        
        if ( $network_wide ) {
            $site_ids = get_sites( [
                'fields'     => 'ids',
                'network_id' => get_current_network_id(),
            ] );
            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::activate_single_site();
                restore_current_blog();
            }
            return;
        }
        
        self::activate_single_site();
    }
    
    /**
     * Perform required activation actions on a site
     *
     * @since 2.2.11
     */
    public static function activate_single_site()
    {
        if ( !wp_next_scheduled( 'mbp_refresh_token' ) ) {
            wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'mbp_refresh_token' );
        }
    }
    
    /**
     * Perform tasks upon plugin deactivation
     *
     * @param bool $network_wide Plugin is network activated
     */
    public static function deactivate( $network_wide )
    {
        
        if ( $network_wide ) {
            $site_ids = get_sites( [
                'fields'     => 'ids',
                'network_id' => get_current_network_id(),
            ] );
            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::deactivate_single_site();
                restore_current_blog();
            }
            return;
        }
        
        self::deactivate_single_site();
    }
    
    /**
     * Perform deactivation actions on a single site
     *
     * @since 2.2.11
     */
    public static function deactivate_single_site()
    {
        wp_clear_scheduled_hook( 'mbp_refresh_token' );
        //Delete the queues for all scheduled posts
        wp_unschedule_hook( "mbp_scheduled_google_post" );
    }
    
    /**
     * Triggered when the plugin is uninstalled
     */
    public static function uninstall()
    {
        
        if ( is_multisite() ) {
            $site_ids = get_sites( [
                'fields' => 'ids',
            ] );
            foreach ( $site_ids as $site_id ) {
                switch_to_blog( $site_id );
                self::uninstall_single_site();
                restore_current_blog();
            }
            return;
        }
        
        self::uninstall_single_site();
    }
    
    /**
     * Perform required uninstall actions on a single site
     *
     * @since 2.2.11
     */
    public static function uninstall_single_site()
    {
        global  $wpdb ;
        $wpdb->query( 'DELETE FROM wp_options WHERE option_name LIKE "mbp_%"' );
    }
    
    /**
     * Triggered when a new site is added to the network
     *
     * @param int|WP_Site $blog_id_or_site
     *
     * @since 2.2.11
     */
    public static function insert_site( $blog_id_or_site )
    {
        
        if ( $blog_id_or_site instanceof WP_Site ) {
            $site_id = $blog_id_or_site->id;
        } elseif ( is_int( $blog_id_or_site ) ) {
            $site_id = $blog_id_or_site;
        } else {
            return;
        }
        
        switch_to_blog( $site_id );
        self::activate_single_site();
        restore_current_blog();
    }
    
    /**
     * Initialize upgrader
     *
     * If no version option is found, set it
     *
     * Upgrader always has to be initialized for the background process to work
     */
    public function do_upgrades()
    {
        $version = get_option( 'mbp_version' );
        
        if ( !$version ) {
            update_option( 'mbp_version', self::PLUGIN_VERSION );
            $version = self::PLUGIN_VERSION;
        }
        
        $upgrader = new PGMB\Upgrader\Upgrader( $version, self::PLUGIN_VERSION );
        $upgrader->init();
    }
    
    public function is_configured()
    {
        if ( get_option( 'mbp_api_key' ) ) {
            return true;
        }
        return false;
    }
    
    public static function dashicon()
    {
        return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pg0KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIEZpcmV3b3JrcyAxMCwgRXhwb3J0IFNWRyBFeHRlbnNpb24gYnkgQWFyb24gQmVhbGwgKGh0dHA6Ly9maXJld29ya3MuYWJlYWxsLmNvbSkgLiBWZXJzaW9uOiAwLjYuMSAgLS0+DQo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPg0KPHN2ZyBpZD0iZGFzaGljb24uZnctUGFnZSUyMDEiIHZpZXdCb3g9IjAgMCAyMDcgMjA3IiBzdHlsZT0iYmFja2dyb3VuZC1jb2xvcjojZmZmZmZmMDAiIHZlcnNpb249IjEuMSINCgl4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4bWw6c3BhY2U9InByZXNlcnZlIg0KCXg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMjA3cHgiIGhlaWdodD0iMjA3cHgiDQo+DQoJPGcgaWQ9IkxheWVyJTIwMSI+DQoJCTxwYXRoIGQ9Ik0gMTQ5Ljk5OTEgMTEyIEMgMTcwLjEyMzYgMTEyIDE4Ni40OTk0IDEyOC4zNzM0IDE4Ni41IDE0OC40OTkxIEMgMTg2LjUgMTY4LjYyNzIgMTcwLjEyMzYgMTg1IDE0OS45OTkxIDE4NSBDIDEyOS44NzQgMTg1IDExMy41IDE2OC42MjcyIDExMy41IDE0OC40OTkxIEMgMTEzLjUgMTI4LjM3MzQgMTI5Ljg3NCAxMTIgMTQ5Ljk5OTEgMTEyIFpNIDE1Ny4yMzUgOTMuMjkgQyAxNTEuOTAyMyAxMDIuNzQ5NiAxNDEuNTYgMTA4Ljg0MyAxMzAuNSAxMDguODQzIEMgMTIxLjMzMDQgMTA4Ljg0MyAxMDkuNjM5MSAxMDQuNDI5MyAxMDMuNTIgOTMuMDk1IEMgOTcuNjYxMSAxMDMuNzAyIDg3LjI2NzcgMTA4Ljg0MyA3Ni44NzUgMTA4Ljg0MyBDIDY1LjU5ODQgMTA4Ljg0MyA1NS42ODIgMTAyLjQ5NjYgNTAuNDA1IDkzLjMxNSBDIDQ0LjM5OTQgMTA0LjExMDkgMzMuNDA1NyAxMDguNzg5IDIzLjYyNSAxMDguNzg5IEMgMTkuNTUxIDEwOC43ODkgMTUuNDQwOSAxMDguMTA1NyAxMS41NCAxMDYuNTEgTCAxMS41NDIgMTgwLjYyMjEgQyAxMS41NDIgMTg4LjA2MjUgMTcuNjI5OSAxOTQuMTUwNCAyNS4wNzAzIDE5NC4xNTA0IEwgMTgyLjYwNDUgMTk0LjE1MDQgQyAxOTAuMDQ1NCAxOTQuMTUwNCAxOTYuMTMyOCAxODguMDYyNSAxOTYuMTMyOCAxODAuNjIyMSBMIDE5Ni4xMzUgMTA2LjIyIEMgMTkyLjI5MjQgMTA3LjkzMzcgMTg4LjA4MzMgMTA4Ljg3NSAxODMuNzUgMTA4Ljg3NSBDIDE3NC40OTk3IDEwOC44NzUgMTYzLjgyMzMgMTA0LjU5OCAxNTcuMjM1IDkzLjI5IFpNIDE0OS45OTkxIDE4My4zMjY5IEMgMTY5LjIwMDcgMTgzLjMyNjkgMTg0LjgyMjkgMTY3LjcwMzEgMTg0LjgyMjkgMTQ4LjQ5OTEgQyAxODQuODIyOSAxMjkuMjk2MyAxNjkuMjAwNyAxMTMuNjczNiAxNDkuOTk5MSAxMTMuNjczNiBDIDEzMC43OTYzIDExMy42NzM2IDExNS4xNzM2IDEyOS4yOTYzIDExNS4xNzM2IDE0OC40OTkxIEMgMTE1LjE3MzYgMTY3LjcwMzEgMTMwLjc5NjkgMTgzLjMyNjkgMTQ5Ljk5OTEgMTgzLjMyNjkgWk0gMTc3LjQ3MzIgMTMzLjQ3NjQgQyAxNzkuOTE4OSAxMzcuOTM2NiAxODEuMzA5NSAxNDMuMDU1OCAxODEuMzA4OSAxNDguNDk5NyBDIDE4MS4zMDg5IDE2MC4wNTEyIDE3NS4wNDc4IDE3MC4xMzY0IDE2NS43MzkyIDE3NS41NjQ4IEwgMTc1LjMwMzQgMTQ3LjkxMTYgQyAxNzcuMDkwNyAxNDMuNDQ1NSAxNzcuNjg0MSAxMzkuODczNiAxNzcuNjg0MSAxMzYuNjk2MiBDIDE3Ny42ODQxIDEzNS41NDQ1IDE3Ny42MDc5IDEzNC40NzM4IDE3Ny40NzMyIDEzMy40NzY0IFpNIDE1MC41NDg1IDE1MS4yMzggTCAxNjAuMTc0IDE3Ny42MDY2IEMgMTYwLjIzNjUgMTc3Ljc2MTYgMTYwLjMxMjcgMTc3LjkwMzkgMTYwLjM5NjggMTc4LjAzOCBDIDE1Ny4xNDIgMTc5LjE4MjUgMTUzLjY0NTMgMTc5LjgxMyAxNDkuOTk5MSAxNzkuODEzIEMgMTQ2LjkyNTQgMTc5LjgxMyAxNDMuOTU5IDE3OS4zNjE4IDE0MS4xNTQxIDE3OC41MzczIEwgMTUwLjU0ODUgMTUxLjIzOCBaTSAxNzEuMTM2NSAxNDYuOTE5IEMgMTcxLjEzNjUgMTQ5LjU5OTUgMTcwLjEwNjMgMTUyLjcxMDMgMTY4Ljc1MjcgMTU3LjA0MTIgTCAxNjUuNjI5NiAxNjcuNDc3OSBMIDE1NC4zMTQ0IDEzMy44MTg1IEMgMTU2LjE5ODIgMTMzLjcxOTUgMTU3Ljg5ODEgMTMzLjUxOTkgMTU3Ljg5ODEgMTMzLjUxOTkgQyAxNTkuNTg0NyAxMzMuMzIwMyAxNTkuMzg2NCAxMzAuODQxMiAxNTcuNjk3MyAxMzAuOTQwNyBDIDE1Ny42OTczIDEzMC45NDA3IDE1Mi42MjcxIDEzMS4zMzgxIDE0OS4zNTI3IDEzMS4zMzgxIEMgMTQ2LjI3NiAxMzEuMzM4MSAxNDEuMTA1MiAxMzAuOTQwNyAxNDEuMTA1MiAxMzAuOTQwNyBDIDEzOS40MTc5IDEzMC44NDEyIDEzOS4yMjAxIDEzMy40MjEgMTQwLjkwNzQgMTMzLjUxOTkgQyAxNDAuOTA3NCAxMzMuNTE5OSAxNDIuNTA0NyAxMzMuNzE5NSAxNDQuMTkwOCAxMzMuODE4NSBMIDE0OS4wNjkxIDE0Ny4xODQ4IEwgMTQyLjIxNjkgMTY3LjczNiBMIDEzMC44MTQ4IDEzMy44MTk2IEMgMTMyLjcwMjIgMTMzLjcyMDcgMTM0LjM5ODQgMTMzLjUyMTEgMTM0LjM5ODQgMTMzLjUyMTEgQyAxMzYuMDg0NSAxMzMuMzIxNiAxMzUuODg1NSAxMzAuODQyNCAxMzQuMTk3NiAxMzAuOTQxOSBDIDEzNC4xOTc2IDEzMC45NDE5IDEyOS4xMjgxIDEzMS4zMzkzIDEyNS44NTMgMTMxLjMzOTMgQyAxMjUuMjY1IDEzMS4zMzkzIDEyNC41NzI3IDEzMS4zMjM4IDEyMy44MzgxIDEzMS4zMDE4IEMgMTI5LjQzNjcgMTIyLjgwMDggMTM5LjA2MDQgMTE3LjE4ODMgMTQ5Ljk5OTEgMTE3LjE4ODMgQyAxNTguMTUwNiAxMTcuMTg4MyAxNjUuNTcyNCAxMjAuMzA0NCAxNzEuMTQyOSAxMjUuNDA4IEMgMTcxLjAwNzggMTI1LjQwMDMgMTcwLjg3NjEgMTI1LjM4MyAxNzAuNzM3MiAxMjUuMzgzIEMgMTY3LjY2MjQgMTI1LjM4MyAxNjUuNDc5NCAxMjguMDYyNCAxNjUuNDc5NCAxMzAuOTQwNyBDIDE2NS40Nzk0IDEzMy41MTk5IDE2Ni45NjcxIDEzNS43MDQyIDE2OC41NTQzIDEzOC4yODM0IEMgMTY5Ljc0NjUgMTQwLjM2OTMgMTcxLjEzNjUgMTQzLjA0OTMgMTcxLjEzNjUgMTQ2LjkxOSBaTSAxMTguNjg4MSAxNDguNDk5MSBDIDExOC42ODgxIDE0My45NTk2IDExOS42NjE2IDEzOS42NTAyIDEyMS4zOTg5IDEzNS43NTYgTCAxMzYuMzM0NyAxNzYuNjc5NiBDIDEyNS44OTA2IDE3MS42MDM5IDExOC42ODgxIDE2MC44OTMxIDExOC42ODgxIDE0OC40OTkxIFpNIDE2MS4zNzUgNzcuMDU1IEMgMTYxLjcyMDIgODAuMDc4NCAxNjEuNjQxMSA4My40NTQzIDE2MyA4Ni42MjUgQyAxNjcuNSA5Ni44NzUgMTc2IDEwMC42MjUgMTgzLjc1IDEwMC42MjUgQyAxODguMDkzNCAxMDAuNjI1IDE5Mi40MzkgOTkuMzE5NSAxOTYuMTM1IDk2Ljk3IEMgMjAyLjAxMjEgOTMuMjMzOSAyMDYuMjUgODYuODU2IDIwNi4yNSA3OC44NzUgQyAyMDYuMjUgNzUuMTI1IDE5My43NSAzMC42MjUgMTkyLjUgMjQuMTI1IEMgMTkxLjc1IDIwLjEyNSAxODkuMjUgMTMuMzc1IDE4My41IDEzLjM3NSBMIDE1My4yNSAxMy4zNzUgTCAxNTMuMjUgMTQuODc1IEMgMTUzLjI1IDE1LjM3NSAxNTYuNzUgNDAuNjI1IDE1OC4yNSA1My4zNzUgQyAxNTkuMjUgNjAuNjI1IDE2MC41IDY4LjM3NSAxNjEuMjUgNzYuMTI1IEMgMTYxLjI5NjkgNzYuNDMgMTYxLjMzOTIgNzYuNzQxOCAxNjEuMzc1IDc3LjA1NSBaTSAxMDggNzcuMDU1IEwgMTA4IDc5LjM0MyBDIDEwOCA3OS44NDMgMTA4LjI1IDgyLjg0MyAxMDguNzUgODQuMzQzIEMgMTEyLjI1IDk2LjA5MyAxMjIgMTAwLjU5MyAxMzAuNSAxMDAuNTkzIEMgMTQxLjI1IDEwMC41OTMgMTUyLjI1IDkyLjU5MyAxNTMgNzguNTkzIEwgMTUyLjggNzcuMDU1IEwgMTQ0LjUgMTMuMzQzIEwgMTA4IDEzLjM0MyBMIDEwOCA3Ny4wNTUgWk0gNTQuNjI1IDc3LjA1NSBMIDU0LjYyNSA4MC4zNDMgQyA1NC42MjUgODAuODQzIDU1LjM3NSA4NC44NDMgNTYuMTI1IDg2Ljg0MyBDIDYwLjg3NSA5Ny4wOTMgNjkuMzc1IDEwMC41OTMgNzcuMTI1IDEwMC41OTMgQyA4Ni42MjUgMTAwLjU5MyA5Ni44NzUgOTQuMzQzIDk5LjM3NSA4MS4wOTMgTCA5OS4zNzUgNzcuMDU1IEwgOTkuMzc1IDEzLjM0MyBMIDYyLjg3NSAxMy4zNDMgTCA1NC42MjUgNzYuMzQzIEwgNTQuNjI1IDc3LjA1NSBaTSAxMS41NCA5Ny4wNSBDIDE1LjEzMzggOTkuMjcwOSAxOS4zMzAzIDEwMC41MzkgMjMuNjI1IDEwMC41MzkgQyAzMy42MjUgMTAwLjUzOSA0NC4zNzUgOTQuMDM5IDQ2LjM3NSA3OS43ODkgTCA0Ni4zNzUgNzguMzQzIEwgNDYuMzc1IDc3LjA1NSBMIDQ2LjM3NSA3NS4wMzkgTCA1NC4zNzUgMTMuMjg5IEwgMjMuODc1IDEzLjI4OSBDIDIyLjEyNSAxMy4yODkgMTkuMTI1IDE0LjUzOSAxNy42MjUgMTYuNzg5IEMgMTYuODc1IDE3LjUzOSAxNi4zNzUgMTguNTM5IDE2LjEyNSAxOS41MzkgQyAxMy42MjUgMjguMjg5IDExLjM3NSAzNy41MzkgOS4xMjUgNDYuNTM5IEMgOC4zNzUgNDguNzg5IDcuNjI1IDUxLjc4OSA3LjEyNSA1NC4yODkgQyA2LjEyNSA1OC4yODkgNS4xMjUgNjIuNzg5IDMuODc1IDY2Ljc4OSBDIDIuODc1IDcwLjc4OSAwLjg3NSA3Ny43ODkgMC44NzUgNzguNTM5IEMgMC44NzUgODYuNDY5MiA1LjMwMjYgOTMuMTk0NCAxMS41NCA5Ny4wNSBaIiBmaWxsPSIjOWVhM2E4Ii8+DQoJPC9nPg0KPC9zdmc+';
    }
    
    public function version()
    {
        return $this::PLUGIN_VERSION;
    }
    
    private function register_enabled_post_types()
    {
        $this->enabled_post_types = array_values( $this->settings_page->get_current_setting( 'post_types', 'mbp_post_type_settings', array( 'post' ) ) );
        $this->enabled_post_types = apply_filters( 'mbp_post_types', $this->enabled_post_types );
    }

}