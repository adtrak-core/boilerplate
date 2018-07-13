<?php namespace Adtrak\Forms\Controllers\Admin;

use Adtrak\Forms\View;
use Billy\Framework\Models\Option;

class OptionsController
{
    protected $id;
    protected $notice;

    /**
     * OptionsController constructor
     */
    public function __construct()
    {
        $this->id = $_GET['id'];
    }

    /**
     * Setup menu items
     *
     * @return void
     */
    public function menu()
    {
        add_submenu_page(
            'adtrak-forms',
            'Settings',
            'Settings',
            'manage_options',
            'adtrak-forms-options',
            [$this, 'showOptions']
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function showOptions()
    {
        # Check if data has been POST'd to Controller
        if ($_POST) {
            # Loop through each item POST'd and store it's key and value
            unset($_POST['submit']);

            $this->activateLicense();
            $analytics = filter_var($_POST['analytics'], FILTER_VALIDATE_BOOLEAN);
            update_option('apcf_old_analytics', $analytics);
            unset($_POST['analytics']);
            unset($_POST['license-key']);

            $option = Option::firstOrNew(['option_name' => 'apcf_options']);
            $option->option_value = serialize($_POST);
            $option->autoload = 'yes';

            try {
                $this->returnNotice('success', 'Options saved');
                $option->save();
            } catch (Exception $e) {
                $this->returnNotice('error', 'Could not save options');
            }
        }

        # Find all options
        $allOptions = Option::where('option_name', '=', 'apcf_options')->get();
        if (!empty($allOptions[0])) {
            $allOptions = unserialize($allOptions[0]->option_value);
            $licenseop = Option::where('option_name', '=', 'apcf_license')->first();

            $workedData = [];
            foreach ($allOptions as $key => $option) {
                $nkey = str_replace("-", "_", $key);
                $workedData[$nkey] = $option;
            }

            $allOptions = $workedData;

            @$license = $licenseop->option_value;
            $allOptions['license_key'] = $license;

            $licensevalidity = Option::where('option_name', '=', 'apcf_license_status')->get();
            $validity = $licensevalidity[0]->option_value;
            $allOptions['license_valid'] = $validity;
        }

        $analytics = filter_var(get_option('apcf_old_analytics'), FILTER_VALIDATE_BOOLEAN);
        View::render('admin/options.twig', [
            'options' => $allOptions,
            'analytics' => $analytics,
            'notice' => $this->notice
        ]);
    }

    /**
     * Create a notice array and store in the Controllers notice variable
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public function returnNotice($type, $message)
    {
        $notice = [];
        $notice['type'] = $type;
        $notice['message'] = $message;
        $this->notice = $notice;
    }

    /**
     * Check license and store
     *
     * @return void
     */
    private function activateLicense()
    {
        $this->sanitizeLicense($_REQUEST['license-key']);

        update_option('apcf_license', $_REQUEST['license-key']);
        
        // retrieve the license from the database
        $license = trim(get_option('apcf_license', ''));

        // data to send in our API request
        $api_params = [
            'edd_action' => 'activate_license',
            'license'    => $license,
            'item_name'  => urlencode('Contact Form'), // the name of our product in EDD
            'url'        => home_url()
        ];

        // Call the custom API.
        $response = wp_remote_post(ADTK_HOME_URL, ['timeout' => 15, 'sslverify' => false, 'body' => $api_params]);

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            $message = (is_wp_error($response) && !empty($response->get_error_message())) ? $response->get_error_message() : __('An error occurred, please try again.');
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));
            if (false === $license_data->success) {
                switch ($license_data->error) {
                    case 'expired':
                        $message = sprintf(
                            'Your license key expired on %s.',
                            date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;
                    case 'revoked':
                        $message = 'Your license key has been disabled.';
                        break;
                    case 'missing':
                        $message = 'Invalid license.';
                        break;
                    case 'invalid':
                    case 'site_inactive':
                        $message = 'Your license is not active for this URL.';
                        break;
                    case 'item_name_mismatch':
                        $message = sprintf('This appears to be an invalid license key for %s.', 'Contact Form');
                        break;
                    case 'no_activations_left':
                        $message = 'Your license key has reached its activation limit.';
                        break;
                    default:
                        $message = 'An error occurred, please try again.';
                        break;
                }
            }
        }

        update_option('apcf_license_status', $license_data->license);
        
        // Check if anything passed on a message constituting a failure
        if (!empty($message)) {
            echo $message;
        }
    }

    /**
     * Check new key against old
     *
     * @param string $new
     * @return void
     */
    private function sanitizeLicense($new)
    {
        $old = get_option('apcf_license');

        if ($old && $old != $new) {
            delete_option('apcf_license_status');
        }
    }
}
