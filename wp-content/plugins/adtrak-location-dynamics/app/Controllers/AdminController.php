<?php namespace Adtrak\LocationDynamics\Controllers;

use Adtrak\LocationDynamics\View;
use Billy\Framework\Models\Option;
use Adtrak\LocationDynamics\Controllers\Admin\JSONController;

class AdminController
{
    protected $json;

    /**
     * Undocumented function
     */
    public function __construct()
    {
        $this->json = new JSONController();
    }

    /**
     * Setup menu items
     *
     * @return void
     */
    public function menu()
    {
        add_menu_page(
            'Location Dynamics',
            'Location Dynamics',
            'manage_options',
            'adtrak-location-dynamics',
            [$this, 'displayNumbers']
        );

        $this->json->menu();
    }

    /**
     * Display numbers and save
     *
     * @return void
     */
    public function displayNumbers()
    {
        # Check whether options row exists, if not create
        $dynamics = Option::firstOrNew(['option_name' => 'ald_numbers']);

        # Checks whether data has been posted
        if ($_POST) {
            $nums = [];
            foreach ($_POST['dynamics'] as $key => $num) {
                $num['label'] = stripslashes($num['label']);
                $num['calltag'] = stripslashes($num['calltag']);
                $num['location'] = str_replace(["'", '/', '\\'], "", $num['location']);
                $num['tracking'] = stripslashes($num['tracking']);
                $nums['dynamics'][$key] = $num;
            }
            $nums['insights-code'] = $_POST['insights-code'];
            $nums['default-tracking'] = $_POST['default-tracking'];
            $nums['tracking-type'] = $_POST['tracking-type'];
            $dynamics->option_value = serialize($nums);
            $dynamics->autoload = 'yes';

            # Save posted data
            try {
                $dynamics->save();
            } catch (Exception $e) {
            }
        }

        # Get data and unserialize from database
        $dynamics = unserialize($dynamics->option_value);

        if (!empty($dynamics['dynamics'])) {

            # Replace data keys with hypens to underscores
            $workedData = [];
            foreach ($dynamics as $key => $dynamic) {
                $nkey = str_replace("-", "_", $key);
                $workedData[$nkey] = $dynamic;
            }

            $dynamics = $workedData;

            $new = [];
            $i = 0;

            # Check if dynamics are set and change the key and add an id
            if (isset($dynamics['dynamics'])) {
                foreach ($dynamics['dynamics'] as $key => $dyn) {
                    $dyn['id'] = $i;
                    $new[] = $dyn;
                    $i++;
                }
            }

            $dynamics['dynamics'] = $new;
            if (!isset($dynamics['insights_code'])) {
                $dynamics['insights_code'] = "";
            } else {
                $dynamics['insights_code'] = str_replace("\\", "", $dynamics['insights_code']);
            }
            if (!isset($dynamics['default_tracking'])) {
                $dynamics['default_tracking'] = "";
            } else {
                $dynamics['default_tracking'] = str_replace("\\", "", $dynamics['default_tracking']);
            }
        }

        $uknumber = $dynamics['dynamics'][0];

        unset($dynamics['dynamics'][0]);

        # Show the view with numbers
        View::render('admin/dashboard.twig', [
            'uknumber' => $uknumber,
            'numbers' => $dynamics
        ]);
    }
}
