<?php namespace Adtrak\LocationDynamics\Controllers;

use Adtrak\LocationDynamics\View;
use Billy\Framework\Models\Option;
use Adtrak\LocationDynamics\Controllers\Admin\JSONController;

class AdminController
{

	protected $json;

	/**
	*
	*/
	public function __construct()
	{
		$this->json = new JSONController();
	}

	/**
	*
	*/
	public function menu()
	{
		add_menu_page ( 
			'Location Dynamics',
			'Location Dynamics',
			'edit_posts',  
			'adtrak-location-dynamics',
			[$this, 'displayNumbers']
		);

		$this->json->menu();
	}

	/**
	* Display numbers and save
	* @return View
	*/
	public function displayNumbers()
	{
		# Check whether options row exists, if not create
		$dynamics = Option::firstOrNew(['option_name' => 'ald_numbers']);

		# Checks whether data has been posted
		if ($_POST) {
			$dynamics->option_value = serialize($_POST);
			$dynamics->autoload = 'yes';

			# Save posted data
			try {	
				$dynamics->save();
			} catch(Exception $e) {
			}

		}

		# Get data and unserialize from database
		$dynamics = unserialize($dynamics->option_value);

		if(!empty($dynamics['dynamics'])) {

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
			if(isset($dynamics['dynamics'])) {
				foreach($dynamics['dynamics'] as $key => $dyn) {
					$dyn['id'] = $i;
					$new[] = $dyn;
					$i++;
				}
			}

			$dynamics['dynamics'] = $new;
			if(!isset($dynamics['insights_code'])) $dynamics['insights_code'] = "";
			else $dynamics['insights_code'] = str_replace("\\", "", $dynamics['insights_code']);

		}

		// dd($dynamics);

		# Show the view with numbers
		View::render('admin/dashboard.twig', [
			'numbers' => $dynamics
		]);
	}

}