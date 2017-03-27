<?php namespace Adtrak\LocationDynamics\Controllers\Admin;

use Adtrak\LocationDynamics\View;
use Billy\Framework\Models\Option;

class JSONController
{

	/**
	*
	*/
	public function __construct()
	{
	}

	/**
	*
	*/
	public function menu()
	{
		add_submenu_page (
			'adtrak-location-dynamics', 
			'JSON Upload',     
			'JSON Upload',    
			'manage_options',  
			'adtrak-location-dynamics-json',   
			[$this, 'jsonProcess']
		);
	}

	/**
	* Process the JSON file and redirect
	*/
	public function jsonProcess() 
	{
		# Check if a form has been submitted
		if($_POST) {
			# Remove the only hyphen
			$content = str_replace("google-adwords", "google_adwords", file_get_contents($_FILES['json']['tmp_name']));
			# Process JSON file
			$content = json_decode($content);

			# Create UK location
			$numbers = [
				'location' => 'uk',
				'calltag' => $content->locationdynamics->default->calltag,
				'seo' => $content->locationdynamics->default->source->organic->computer,
				'ppc' =>  $content->locationdynamics->default->source->google_adwords->computer
			];

			# Remove unused elements
			unset($content->locationdynamics->default);
			unset($content->locationdynamics->log);

			# Create array
			$locations = [];

			# Set up random
			$rand = rand(100, 999);

			# Post UK to the array
			$locations['dynamic' . $rand] = $numbers;

			# Loop through the locations and add to the database
			foreach($content->locationdynamics->locations as $location) {

				$number = [
					'location' => str_replace(" ", "-", strtolower($location->area)),
					'calltag' => $location->calltag,
					'seo' => $location->source->organic->computer,
					'ppc' =>  $location->source->google_adwords->computer
				];

				$rand = rand(100, 999);

				$locations['dynamic' . $rand] = $number;

			}

			# Add to new array under dynamics
			$nLocations['dynamics'] = $locations;

			# Check whether options row exists, if not create
			$dynamics = Option::firstOrNew(['option_name' => 'ald_numbers']);

			$dynamics->option_value = serialize($nLocations);
			$dynamics->autoload = 'yes';

			# Save posted data
			try {	
				$dynamics->save();
			} catch(Exception $e) {
			}

			# Redirect to the dashboard
			echo '<META HTTP-EQUIV="refresh" content="0;URL=admin.php?page=adtrak-location-dynamics">';
			echo '<script>window.location.href=admin.php?page=adtrak-location-dynamics</script>';
			die();
		}

		View::render('admin/upload.twig', []);

	}

}