<?php namespace Adtrak\LocationDynamics\Controllers;

use Adtrak\LocationDynamics\View;
use Billy\Framework\Models\Option;

class FrontController
{

	/**
	* Setup the actions
	*/
	public function __construct()
	{
		add_action('ld_single', [$this, 'ld_single'], 10, 2);
		add_action('ld_default', [$this, 'ld_default'], 10);
		add_action('ld_list', [$this, 'ld_list'], 10, 2);
		add_action('ld_mobile_top', [$this, 'ld_mobile_top'], 10, 1);
		add_action('ld_location', [$this, 'ld_location'], 10, 2);
	}

	/**
	* Create the shortcodes
	*/
	public function addShortcodes()
	{
		add_shortcode( 'ld_single', array( $this, 'ld_single_shortcode' ), 10 );
		add_shortcode( 'ld_default', array( $this, 'ld_default_shortcode' ), 10 );
		add_shortcode( 'ld_list', array( $this, 'ld_list_shortcode' ), 10 );
		add_shortcode( 'ld_location', array( $this, 'ld_location_shortcode' ), 10 );
	}

	/**
	* Code to show a number or dropdown button
	*/
	public function ld_mobile_top($text)
	{
		# Get dynamics data from database
		$dynamics = Option::where(['option_name' => 'ald_numbers'])->first();
		$dynamics = unserialize($dynamics->option_value);

		# List numbers by name
		$order = [];
		foreach ($dynamics['dynamics'] as $dynamic) {
			$order[$dynamic['location']] = $dynamic; 
		}

		# Check if a GET request is used or cookie
		if (isset($_GET['a']) && $_GET['a'] != 'uk') {
			# Check if a GET request is used 
			$loc = $_GET['a'];
			$type = 'ppc';
			echo $this->buildNumber($order, $loc, $type, false);
		} else if (isset($_COOKIE['area']) && $_COOKIE['area']) {
			# Check if a cookie is used and not a GET
			$loc = $_COOKIE['area'];
			$type = 'ppc';
			echo $this->buildNumber($order, $loc, $type, false);
		} else if (count($order) == 1) {
			# Check if there is only 1 number
			$loc = 'uk';
			$type = 'seo';
			echo $this->buildNumber($order, $loc, $type, false);
		} else {
			# Show button if none of the above is satisfied
			$loc = 'uk';
			$type = 'seo';
			echo "<a class='js-toggle-location-numbers'>" . $text . "</a>";
		}
	}

	/**
	* Get if GET is set and set a cookie for 30 days
	*/
	public function getCookie()
	{
		if (isset($_GET['a'])) {
			setcookie('area', $_GET['a'], time()+60*60*24*30);
		}
	}

	/**
	*	Gets and returns insights js code
	* 	@return mixed
	*/
	public function getInsightCode()
	{
		# Get dynamics data from database
		$dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

		# If plugin isnt setup, return
		if (!isset($dynamics['insights-code'])) {
			return;
		}

		$dynamics = unserialize($dynamics->option_value);

		# Remove backslasges and display code
		echo str_replace("\\", "", $dynamics['insights-code']);
	}

	/**
	* Passthrough for the ld_single shortcode
	*/
	public function ld_location_shortcode($atts)
	{   
		$this->ld_location();
	}

	/**
	* ld_single action which displays a single number for a given lcoation
	*/
	public function ld_location()
	{
		# Get dynamics data from database
		$dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

		# If plugin isnt setup show message
		if (!isset($dynamics)) {
			echo "No numbers set";
			return;
		}

		# If GET or cookie is set get location from that
		if (isset($_GET['a']) || isset($_COOKIE['area']))
		$loc = isset($_GET['a']) ? $_GET['a'] : $_COOKIE['area'];

		if(isset($loc)) echo ucfirst($loc);
		else echo "UK";
		
		return;
	}

	/**
	* Passthrough for the ld_single shortcode
	*/
	public function ld_single_shortcode($atts)
	{   
		if (!isset($atts['location'])) $atts['location'] = null;
		if (!isset($atts['calltag'])) $atts['calltag'] = null;
		$this->ld_single($atts['location'], $atts['calltag']);
	}

	/**
	* ld_single action which displays a single number for a given lcoation
	*/
	public function ld_single($location = null, $calltag = false)
	{
		# Get dynamics data from database
		$dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

		$calltag = filter_var($calltag, FILTER_VALIDATE_BOOLEAN);

		# If plugin isnt setup show message
		if (!isset($dynamics)) {
			echo "No numbers set";
			return;
		}

		$dynamics = unserialize($dynamics->option_value);

		# If location isnt set show default
		if ($location == null) {
			$this->ld_default();
			return;
		}

		# If no numbers are set show message
		if (!isset($dynamics['dynamics'])) {
			echo "No numbers set";
			return;
		}

		# List numbers by name
		$order = [];
		foreach ($dynamics['dynamics'] as $dynamic) {
			$order[$dynamic['location']] = $dynamic; 
		}

		# If location doesnt exist
		if(!isset($order[$location])) {
			$this->ld_default();
			return;
		}

		# If calltag is requested, show calltag
		if ($calltag) echo $order[$location]['calltag'] . " ";

		# If GET or cookie is set get location from that
		if (isset($_GET['a']) || isset($_COOKIE['area']))
		$loc = isset($_GET['a']) ? $_GET['a'] : $_COOKIE['area'];

		# If PPC number is the same as this single show the PPC number else show the seo number
		if (isset($loc) && $loc == $location) echo "<span class='ld-phonenumber ".  $order[$location]['insights'] ."'>". $order[$location]['ppc'] . "</span>";
		else echo "<span class=ld-phonenumber'>". $order[$location]['seo'] . "</span>";
		
		return;
	}

	/**
	* Passthrough for the ld_default shortcode
	*/
	public function ld_default_shortcode()
	{   
		$this->ld_default();
	}

	/**
	* Function to work out and show the default number
	*/
	public function ld_default()
	{
		# Get dynamics data from database
		$dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

		# If plugin isnt setup show message
		if (!isset($dynamics)) {
			echo "No numbers set";
			return;
		}

		$dynamics = unserialize($dynamics->option_value);

		# If no numbers are set show message
		if (!isset($dynamics['dynamics'])) {
			echo "No numbers set";
			return;
		}

		# List numbers by name
		$order = [];
		foreach ($dynamics['dynamics'] as $dynamic) {
			$order[$dynamic['location']] = $dynamic; 
		}

		# If area is set to gen show the default uk number
		if ( (isset($_GET['a']) && $_GET['a'] == 'gen') || (isset($_COOKIE['area']) && $_COOKIE['area'] && $_COOKIE['area'] == 'gen') ) {
			$loc = 'uk';
			$type = 'ppc';
			echo $this->buildNumber($order, $loc, $type);
			return;
		}

		# Check if a GET request is used or cookie
		if (isset($_GET['a']) && $_GET['a'] != 'uk') {
			# Check if a GET request is used 
			$loc = $_GET['a'];
			$type = 'ppc';
			echo $this->buildNumber($order, $loc, $type, true);
		} else if (isset($_COOKIE['area']) && $_COOKIE['area']) {
			# Check if a cookie is used 
			$loc = $_COOKIE['area'];
			$type = 'ppc';
			echo $this->buildNumber($order, $loc, $type, true);
		} else {
			# Show SEO number if above is not satisfied
			$loc = 'uk';
			$type = 'seo';
			echo $this->buildNumber($order, $loc, $type, true);
		}

		// if ( ! ((isset($_GET['a'])) || (isset($_COOKIE['area']) && $_COOKIE['area']))) {
		// 	if($order['uk']['insights']) echo "<span class='ld-phonenumber'>" . $order['uk']['calltag'] . " <span class='". $order['uk']['insights'] ."'>" . $order['uk']['seo'] . "</span></span>";
		// 	else echo "<span class='ld-phonenumber'>" . $order['uk']['calltag'] . " " . $order['uk']['seo'] . "</span>";
		// } else {
		// 	$loc = isset($_GET['a']) ? $_GET['a'] : $_COOKIE['area'];
		// 	if($order['uk']['insights']) echo "<span class='ld-phonenumber'>" . $order[$loc]['calltag'] . " <span class='". $order[$loc]['insights'] ."'>" . $order[$loc]['ppc'] . "</span></span>";		
		// 	else echo "<span class='ld-phonenumber'>" . $order[$loc]['calltag'] . " " . $order[$loc]['ppc'] . "</span>";
		// }

		return;
	}

	/**
	* Passthrough for the ld_list shortcode
	*/
	public function ld_list_shortcode($atts)
	{   
		if (!isset($atts['ppc'])) $atts['ppc'] = null;
		if (!isset($atts['type'])) $atts['type'] = null;
		$this->ld_list($atts['ppc'], $atts['type']);
	}

	/**
	* Function to show a number list excluding uk
	*/
	public function ld_list($ppc, $listType = null)
	{
		$ppc = filter_var($ppc, FILTER_VALIDATE_BOOLEAN);
		# Check if the listtype is set, if not set a default
		if (!$listType) $listType = 'dropdown';
		# Check if the ppc is set, if not set a default
		if (!isset($ppc)) $ppc = false;

		# Get dynamics data from database
		$dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

		# If plugin isnt setup show message
		if (!isset($dynamics)) {
			echo "No numbers set";
			return;
		}

		$dynamics = unserialize($dynamics->option_value);

		# If no numbers are set show message
		if (!isset($dynamics['dynamics'])) {
			echo "No numbers set";
			return;
		}

		# List numbers by name
		$order = [];
		foreach ($dynamics['dynamics'] as $dynamic) {
			$order[$dynamic['location']] = $dynamic; 
		}
		unset($order['uk']);
		unset($order['the-milky-way']);

		$listBuilder = "";

		# Create the dropdown list
		if ($listType == 'dropdown') {

			$listBuilder .= "<a href='#' class='ld-toggle'>Other Numbers</a>";

			$listBuilder .= "<div class='ld-list ld-dropdown'>";

			foreach ($order as $number) {
				$listBuilder .= "<div class='ld-location'>";
					$listBuilder .= "<div class='ld-area'>";
					$tag = ucwords(str_replace("-", " ", $number['location']));
					$listBuilder .= $tag;
					$listBuilder .= "</div>";

					if (isset($number['insights'])) $listBuilder .= "<div class='ld-number ". $number['insights'] ."'>";
					else $listBuilder .= "<div class='ld-number'>";
					$listBuilder .= $number['seo'];
					$listBuilder .= "</div>";
				$listBuilder .= "</div>";
			}

			$listBuilder .= "</div>";
		}

		# Create the inline list
		if ($listType == 'inline') {

			$listBuilder .= "<div class='ld-list'>";

			foreach ($order as $number) {
				$listBuilder .= "<div class='ld-location'>";
					$listBuilder .= "<div class='ld-area'>";
					$listBuilder .= ucfirst($number['location']);
					$listBuilder .= "</div>";

					$listBuilder .= "<div class='ld-number'>";
					$listBuilder .= $number['seo'];
					$listBuilder .= "</div>";
				$listBuilder .= "</div>";
			}

			$listBuilder .= "</div>";
		}

		# Check if the area or cookie is set
		$stop = false;
		if (! ((isset($_GET['a']) && $_GET['a'] != 'uk') || (isset($_COOKIE['area']) && $_COOKIE['area']))) {
			$stop = false;
		} else {
			$stop = true;
		}

		# Show the list if ppc is true
		if ($ppc) {
			echo $listBuilder;
		} else {
			# Show the list only is ppc is false and a cookie is not set
			if (!$stop) echo $listBuilder;
		}

		return;

	}

	/**
	* Function to build a number based on given information
	*/
	function buildNumber($numbers, $location, $type, $calltag) {
		if (isset($numbers[$location]['insights'])) 
			if ($calltag) 
				echo "<span class='ld-phonenumber'>" . $numbers[$location]['calltag'] . " <span class='". $numbers[$location]['insights'] ."'>" . $numbers[$location][$type] . "</span></span>";
			else
				echo "<span class='ld-phonenumber'><span class='". $numbers[$location]['insights'] ."'>" . $numbers[$location][$type] . "</span></span>";

		else
			if ($calltag) 
				echo "<span class='ld-phonenumber'>" . $numbers[$location]['calltag'] . " " . $numbers[$location][$type] . "</span>";
			else
				echo "<span class='ld-phonenumber'>" . $numbers[$location][$type] . "</span>";
	}

}