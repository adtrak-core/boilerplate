<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * This class handles all the update-related stuff for extensions, including adding a license section to the license tab.
 * It accepts two args: Product Name and Version.
 *
 * @param $product_name string
 * @param $version string
 * @since 2.2.47
 * @return void
 */
class NF_Extension_Updater
{
    public $product_nice_name = '';
    public $product_name = '';
    public $version = '';
    public $store_url = 'https://ninjaforms.com/update-check/';
    public $file = '';
    public $author = '';
    public $error = '';
    private $_last_error;

    /**
     * Constructor function
     *
     * @since 2.2.47
     * @updated 3.0
     * @return void
     */
    public function __construct( $product_name, $version, $author, $file, $slug = '' )
    {
        $this->product_nice_name = $product_name;
        if ( $slug == '' ) {
            $this->product_name = strtolower( $product_name );
            $this->product_name = preg_replace( "/[^a-zA-Z]+/", "", $this->product_name );
        } else {
            $this->product_name = $slug;
        }

        $this->version = $version;
        $this->file = $file;
        $this->author = $author;

        $this->auto_update();
        
        add_filter( 'ninja_forms_settings_licenses_addons', array( $this, 'register' ) );
    }

    /**
     * Function that adds the license entry fields to the license tab.
     *
     * @updated 3.0
     * @param array $licenses
     * @return array $licenses
     */
    function register( $licenses ) {
        $licenses[] = $this;
        return $licenses;
    }

    /*
     *
     * Function that activates our license
     *
     * @since 2.2.47
     * @return void
     */
    function activate_license( $license_key ) {

        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license'   => $license_key,
            'item_name' => urlencode( $this->product_nice_name ), // the name of our product in EDD
            'url' => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        $this->maybe_debug( $response );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        if ( 'invalid' == $license_data->license ) {
            $error = '<span style="color: red;">' . esc_html__( 'Could not activate license. Please verify your license key', 'ninja-forms' ) . '</span>';
            
            if ( isset ( $_REQUEST[ 'nf_debug' ] ) && 1 == absint( $_REQUEST[ 'nf_debug' ] ) ) {
                // Add an error to our admin notice if nf_debug is turned on.
                add_filter( 'nf_admin_notices', array( $this, 'show_license_error_notice' ) );
                $this->_last_error = var_export( $license_data, true );
            }
          
            Ninja_Forms()->logger()->emergency( var_export( $license_data, true ) );

        } else {
            $error = '';
        }

        Ninja_Forms()->update_setting( $this->product_name . '_license', $license_key );
        Ninja_Forms()->update_setting( $this->product_name . '_license_error', $error );
        Ninja_Forms()->update_setting( $this->product_name . '_license_status', $license_data->license );
    }

    public function show_license_error_notice( $notices ) {
        $notices[ 'license_error' ] = array(
            'title' => esc_html__( 'License Activation Error', 'ninja-forms' ),
            'msg' => '<pre>' . $this->_last_error . '</pre>',
            'int' => 0,
            'ignore_spam' => true,
        );

        return $notices;
    }

    /*
     *
     * Function that deactivates our license if the user clicks the "Deactivate License" button.
     *
     * @since 2.2.47
     * @return void
     */

    function deactivate_license() {

        $license = Ninja_Forms()->get_setting( $this->product_name . '_license' );

        // data to send in our API request
        $api_params = array(
            'edd_action'=> 'deactivate_license',
            'license'   => $license,
            'item_name' => urlencode( $this->product_nice_name ), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        $this->maybe_debug( $response );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        Ninja_Forms()->update_setting( $this->product_name.'_license_error', '' );
        Ninja_Forms()->update_setting( $this->product_name.'_license_status', 'invalid' );
        Ninja_Forms()->update_setting( $this->product_name.'_license', '' );
    }

    /**
     * Function that runs all of our auto-update functionality
     *
     * @since 2.2.47
     * @updates 3.0
     * @return void
     */
    function auto_update() {

        $edd_updater = new EDD_SL_Plugin_Updater( $this->store_url, $this->file, array(
                'author'    => $this->author,  // author of this plugin
                'version'   => $this->version, // current version number
                'item_name' => $this->product_nice_name,  // name of this plugin
                'license'   => Ninja_Forms()->get_setting( $this->product_name.'_license' ),  // license key
            )
        );

        // Hook into the "after plugin row" display for this Ninja Forms add-on.
        add_action( 'after_plugin_row_' . plugin_basename( $this->file ), array( $this, 'maybe_prevent_update_notice' ), 9, 3 );
        // Filter that expects either a bool false to continue install or a WP_Error to prevent installation of an update.
        add_filter( 'upgrader_pre_install', array( $this, 'maybe_prevent_install' ), 10, 2 );
    } // function auto_update

    /**
     * Function that maybe prevents a plugin update from installing if the php version is not high enough.
     *         
     * @since  3.4.24       
     * @param  bool   $default   false
     * @param  array  $extra     array sent by the filter we're using.
     * @return bool/WP_ERROR     $default if we bail early, WP_ERROR if we don't.
     */
    public function maybe_prevent_install( $default, $extra )
    {
        // If the plugin being installed isn't this one, bail.
        $plugin = plugin_basename( $this->file );
        if( $plugin != $extra[ 'plugin' ] ) {
            return $default;
        }

        // Grab our WP Updates transient so that we can check the minimum PHP version
        $update_transient = get_option( '_site_transient_update_plugins' );
        // Check to see if we have a php_requires setting for our update.
        $php_requires = isset( $update_transient->response[ $plugin ]->php_requires ) ? $update_transient->response[ $plugin ]->php_requires : false;

        // If we don't have a php_requires setting or our php version meets the php_requires setting, bail.
        if( empty( $php_requires ) || version_compare( PHP_VERSION, $php_requires, '>=' ) ) {
            return $default;
        }

        return new WP_Error( 'php_minimum_version', sprintf( esc_html__( 'The new version requires at least PHP %s, and your PHP version is %s.', 'ninja-forms' ), $php_requires, PHP_VERSION ), esc_html__( 'Please contact your host to upgrade your site\'s PHP version.' ) );
    }

    /**
     * Check to see if this plugin update has a minimum PHP version.
     * If it does, make sure that we meet it.
     * If we don't meet it, then show the user an error message with a link to WordPress.org's minimum requirements page.
     * 
     * @since  3.4.24
     * @param  string  $plugin_file   plugin file for the row we're looking at
     * @param  array   $plugin_data   update data from the WordPress plugin update check
     * @param  string  $plugin_status is this plugin active, inactive, etc.
     * @return void
     */
    public function maybe_prevent_update_notice( $plugin_file, $plugin_data, $plugin_status )
    {
        $php_requires = isset( $plugin_data[ 'php_requires' ] ) ? $plugin_data[ 'php_requires' ] : '';
        
        // Return early if the current PHP version is equal to or greater than the PHP version required by the new version of this add-on.
        if ( version_compare( PHP_VERSION, $php_requires, '>=' ) ) {
            return false;
        }

        remove_action( 'after_plugin_row_' . plugin_basename( $this->file ), 'wp_plugin_update_row', 10 );
        
        echo '<tr class="update-error">
                <td colspan="5" style="background-color:#fef7f1;">
                    <div class="update-message notice inline notice-error notice-alt" style="margin: 10px 0 5px;">
                        <p>
                            ' . sprintf( esc_html__( 'An update is available for %s, however, you are not able to update at this time.', 'ninja-forms' ), 'Ninja Forms - ' . $this->product_nice_name ) . '
                            <br />
                            <strong>' . sprintf( esc_html__( 'The new version requires at least PHP %s, and your PHP version is %s.', 'ninja-forms' ), $php_requires, PHP_VERSION ) . '</strong>
                            <br />
                            ' . sprintf( esc_html__( 'Please contact your host to upgrade your site\'s PHP version. %sRead more about updating your PHP version and WordPress%s.' ), '<a href="https://wordpress.org/about/requirements/" target="_blank">', '</a>' ) . '
                        </p>
                    </div>
                </td>
            </tr>';
    }

    /**
     * Return whether or not this license is valid.
     *
     * @access public
     * @since 2.9
     * @return bool
     */
    public function is_valid() {
         return ( 'valid' == Ninja_Forms()->get_setting( $this->product_name.'_license_status' ) );
    }

    /**
     * Get any error messages for this license field.
     *
     * @access public
     * @since 2.9
     * @return string $error
     */
    public function get_error() {
        return Ninja_Forms()->get_setting( $this->product_name . '_license_error' );
    }

    private function maybe_debug( $data, $key = 'debug' )
    {
        if ( isset ( $_GET[ $key ] ) && 'true' == $_GET[ $key ] ) {
            echo '<pre>'; var_dump( $data ); echo '</pre>';
            die();
        }
    }

} // End Class NF_Extension_Updater