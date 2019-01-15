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
        add_action('ld_single', [$this, 'ld_single'], 10, 4);
        add_action('ld_default', [$this, 'ld_default'], 10, 2);
        add_action('ld_list', [$this, 'ld_list'], 10, 3);
        add_action('ld_mobile_top', [$this, 'ld_mobile_top'], 10, 1);
        add_action('ld_location', [$this, 'ld_location'], 10, 2);
    }

    /**
    * Create the shortcodes
    * @return void
    */
    public function addShortcodes()
    {
        add_shortcode('ld_single', array( $this, 'ld_single_shortcode' ), 10);
        add_shortcode('ld_default', array( $this, 'ld_default_shortcode' ), 10);
        add_shortcode('ld_list', array( $this, 'ld_list_shortcode' ), 10);
        add_shortcode('ld_location', array( $this, 'ld_location_shortcode' ), 10);
    }

    /**
     * Code to show a number or dropdown button
     *
     * @param string $text
     * @param boolean $linked
     * @return void
     */
    public function ld_mobile_top($text, $linked = true)
    {
        # Get dynamics data from database
        $dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

        # If plugin isnt setup show message
        if (!isset($dynamics)) {
            echo "No numbers set";
            return;
        }
        $dynamics = unserialize($dynamics->option_value);
        if (!isset($dynamics['dynamics'])) {
            echo "No numbers set";
            return;
        }

        # List numbers by name
        $order = [];
        foreach ($dynamics['dynamics'] as $dynamic) {
            $order[$dynamic['location']] = $dynamic;
        }

        # Check if a GET request is used or cookie
        if (isset($_GET['a'])) {
            # Check if a GET request is used
            $loc = $_GET['a'];
            if($loc == 'gen') {
                $loc = 'uk';
            }
            $type = 'ppc';
            echo $this->buildNumber($order, $loc, $type, false, $linked);
        } elseif (isset($_COOKIE['area']) && $_COOKIE['area']) {
            # Check if a cookie is used and not a GET
            $loc = $_COOKIE['area'];
            if($loc == 'gen') {
                $loc = 'uk';
            }
            $type = 'ppc';
            echo $this->buildNumber($order, $loc, $type, false, $linked);
        } elseif (count($order) == 1) {
            # Check if there is only 1 number
            $loc = 'uk';
            $type = 'seo';
            echo $this->buildNumber($order, $loc, $type, false, $linked);
        } else {
            # Show button if none of the above is satisfied
            $loc = 'uk';
            $type = 'seo';
            echo "<a class='js-toggle-location-numbers'>" . $text . "</a>";
        }
    }

    /**
     * Get if GET is set and set a cookie for 30 days
     *
     * @return void
     */
    public function getCookie()
    {
        if (isset($_GET['a'])) {
            setcookie('area', $_GET['a'], time()+60*60*24*30, '/');
        }
    }

    /**
    *	Gets and returns insights js code
    * 	@return void
    */
    public function getInsightCode()
    {
        # Get dynamics data from database
        $dynamics = Option::where(['option_name' => 'ald_numbers'])->first();
        $dynamics = unserialize($dynamics->option_value);

        # If plugin isnt setup, return
        if (!isset($dynamics['insights-code'])) {
            return;
        }

        # Remove backslasges and display code
        echo str_replace("\\", "", $dynamics['insights-code']);
    }

    /**
     * Passthrough for the ld_single shortcode
     *
     * @param array $atts
     * @return void
     */
    public function ld_location_shortcode($atts)
    {
        return $this->ld_location(true);
    }

    /**
     * ld_single action which displays a single number for a given lcoation
     *
     * @param boolean $short
     * @return void
     */
    public function ld_location($short = false)
    {
        ob_start();
        # Get dynamics data from database
        $dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

        # If plugin isnt setup show message
        if (!isset($dynamics)) {
            echo "No numbers set";
            return;
        }

        # If GET or cookie is set get location from that
        if (isset($_GET['a']) || isset($_COOKIE['area'])) {
            $loc = isset($_GET['a']) ? $_GET['a'] : $_COOKIE['area'];
        }

        if (isset($loc)) {
            echo ucfirst($loc);
        } else {
            echo "UK";
        }
         
        $content = ob_get_contents();
        ob_end_clean();
        if ($short) {
            return $content;
        } else {
            echo $content;
        }
    }

    /**
     * Passthrough for the ld_single shortcode
     *
     * @param array $atts
     * @return void
     */
    public function ld_single_shortcode($atts)
    {
        if (!isset($atts['location'])) {
            $atts['location'] = null;
        }
        if (!isset($atts['calltag'])) {
            $atts['calltag'] = null;
        }
        if (!isset($atts['linked'])) {
            $atts['linked'] = true;
        }
        return $this->ld_single($atts['location'], $atts['calltag'], filter_var($atts['linked'], FILTER_VALIDATE_BOOLEAN), true, true);
    }

    /**
     * ld_single action which displays a single number for a given lcoation
     *
     * @param string $location
     * @param boolean $calltag
     * @param boolean $linked
     * @param boolean $ppc
     * @param boolean $short
     * @return void
     */
    public function ld_single($location = null, $calltag = false, $linked = true, $ppc = true, $short = false)
    {
        ob_start();
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
            $this->ld_default($calltag);
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
        if (!isset($order[$location])) {
            $this->ld_default($calltag);
            return;
        }

        # If calltag is requested, show calltag
        if ($calltag) {
            echo $order[$location]['calltag'] . " ";
        }

        # If GET or cookie is set get location from that
        if (isset($_GET['a']) || isset($_COOKIE['area'])) {
            $loc = isset($_GET['a']) ? $_GET['a'] : $_COOKIE['area'];
        }

        # If PPC number is the same as this single show the PPC number else show the seo number
        if (isset($loc) && $loc == $location && $ppc == false) {
            echo "<span class='ld-phonenumber ".  $order[$location]['insights'] ."'><a href='tel:". str_replace(' ', '', $order[$location]['ppc']) ."'>". $order[$location]['ppc'] . "</a></span>";
        } elseif ($linked) {
            echo "<span class='ld-phonenumber'><a href='tel:". str_replace(' ', '', $order[$location]['seo']) ."'>". $order[$location]['seo'] . "</a></span>";
        } else {
            echo "<span class='ld-phonenumber'>". $order[$location]['seo'] . "</span>";
        }
        
        $content = ob_get_contents();
        ob_end_clean();
        if ($short) {
            return $content;
        } else {
            echo $content;
        }
    }

    /**
     * Passthrough for the ld_default shortcode
     *
     * @param array $atts
     * @return void
     */
    public function ld_default_shortcode($atts)
    {
        if (empty($atts)) {
            $atts = [];
        }
        if (!isset($atts['calltag'])) {
            $atts['calltag'] = false;
        }
        if (!isset($atts['linked'])) {
            $atts['linked'] = true;
        }
        return $this->ld_default(filter_var($atts['calltag'], FILTER_VALIDATE_BOOLEAN), filter_var($atts['linked'], FILTER_VALIDATE_BOOLEAN), true);
    }

    /**
     * Function to work out and show the default number
     *
     * @param boolean $calltag
     * @param boolean $linked
     * @param boolean $short
     * @return void
     */
    public function ld_default($calltag, $linked = true, $short = false)
    {
        ob_start();
        # Turn calltag into a boolean
        $calltag = filter_var($calltag, FILTER_VALIDATE_BOOLEAN);

        # Get dynamics data from database
        $dynamics = Option::where(['option_name' => 'ald_numbers'])->first();

        if ($calltag === '') {
            $calltag = true;
        }

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

        # Check if a GET request is used or cookie
        if ((isset($_GET['a']) && $_GET['a'] == 'gen') || (isset($_COOKIE['area']) && $_COOKIE['area'] && $_COOKIE['area'] == 'gen')) {
            $loc = 'uk';
            $type = 'ppc';
            echo $this->buildNumber($order, $loc, $type, $calltag, $linked);
        } else if (isset($_GET['a']) && !isset($order[$_GET['a']]['ppc'])) {
            $loc = 'uk';
            $type = 'seo';
            echo $this->buildNumber($order, $loc, $type, $calltag, $linked);
        } else if (isset($_GET['a'])) {
            # If location doesnt exist
            # Check if a GET request is used
            $loc = $_GET['a'];
            $type = 'ppc';
            echo $this->buildNumber($order, $loc, $type, $calltag, $linked);
        } elseif (isset($_COOKIE['area']) && $_COOKIE['area']) {
            # Check if a cookie is used
            $loc = $_COOKIE['area'];
            $type = 'ppc';
            echo $this->buildNumber($order, $loc, $type, $calltag, $linked);
        } else {
            # Show SEO number if above is not satisfied
            $loc = 'uk';
            $type = 'seo';
            echo $this->buildNumber($order, $loc, $type, $calltag, $linked);
        }
                
        $content = ob_get_contents();
        ob_end_clean();
        if ($short) {
            return $content;
        } else {
            echo $content;
        }
    }

    /**
     * Passthrough for the ld_list shortcode
     *
     * @param array $atts
     * @return void
     */
    public function ld_list_shortcode($atts)
    {
        if (!isset($atts['ppc'])) {
            $atts['ppc'] = null;
        }
        if (!isset($atts['type'])) {
            $atts['type'] = null;
        }
        if (!isset($atts['label'])) {
            $atts['label'] = null;
        }
        return $this->ld_list($atts['ppc'], $atts['type'], $atts['label'], true);
    }

    /**
     * Function to show a number list excluding uk
     *
     * @param boolean $ppc
     * @param string $listType
     * @param string $listLabel
     * @param boolean $short
     * @return void
     */
    public function ld_list($ppc, $listType = null, $listLabel = null, $short = false)
    {
        ob_start();
        $ppc = filter_var($ppc, FILTER_VALIDATE_BOOLEAN);
        # Check if the listtype is set, if not set a default
        if (!$listType) {
            $listType = 'dropdown';
        }
        # Check if the listlabel is set, if not set a default
        if (!$listLabel) {
            $listLabel = 'Other Numbers';
        }
        # Check if the ppc is set, if not set a default
        if (!isset($ppc)) {
            $ppc = false;
        }

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
            $listBuilder .= "<a href='#' class='ld-toggle'>". $listLabel ."</a>";

            $listBuilder .= "<div class='ld-list ld-dropdown'>";

            foreach ($order as $number) {
                if (empty($number['seo'])) {
                    continue;
                }
                $listBuilder .= "<div class='ld-location'>";
                $listBuilder .= "<div class='ld-area'>";
                if (isset($number['label']) && !empty($number['label'])) {
                    $tag = $number['label'];
                } else {
                    $tag = ucwords(str_replace("-", " ", $number['location']));
                }
                $listBuilder .= $tag;
                $listBuilder .= "</div>";

                if (!empty($number['insights'])) {
                    $listBuilder .= "<div class='ld-number ". $number['insights'] ."'>";
                } else {
                    $listBuilder .= "<div class='ld-number'><a href='tel:" . str_replace(' ', '', $number['seo']) . "'>";
                }
                $listBuilder .= $number['seo'];
                if (empty($number['insights'])) {
                    $listBuilder .= "</a>";
                }
                $listBuilder .= "</div>";
                $listBuilder .= "</div>";
            }

            $listBuilder .= "</div>";
        }

        # Create the inline list
        if ($listType == 'inline') {
            $listBuilder .= "<div class='ld-list'>";

            foreach ($order as $number) {
                if (empty($number['seo'])) {
                    continue;
                }
                $listBuilder .= "<div class='ld-location'>";
                $listBuilder .= "<div class='ld-area'>";
                if (isset($number['label']) && !empty($number['label'])) {
                    $tag = $number['label'];
                } else {
                    $tag = ucwords(str_replace("-", " ", $number['location']));
                }
                $listBuilder .= $tag;
                $listBuilder .= "</div>";

                $listBuilder .= "<div class='ld-number'><a href='tel:" .  str_replace(' ', '', $number['seo']) . "'>";
                $listBuilder .= $number['seo'];
                $listBuilder .= "</a></div>";
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
            if (!$stop) {
                echo $listBuilder;
            }
        }

        $content = ob_get_contents();
        ob_end_clean();
        if ($short) {
            return $content;
        } else {
            echo $content;
        }
    }

    /**
     * Function to build a number based on given information
     *
     * @param array $numbers
     * @param string $location
     * @param string $type
     * @param boolean $calltag
     * @param boolean $linked
     * @return void
     */
    public function buildNumber($numbers, $location, $type, $calltag, $linked = true)
    {
        if (!empty($numbers[$location]['insights'])) {
            if ($calltag) {
                echo "<span class='ld-phonenumber'>" . $numbers[$location]['calltag'] . "<span class='". $numbers[$location]['insights'] ."'>" . $numbers[$location][$type] . "</span></span>";
            } else {
                echo "<span class='ld-phonenumber'><span class='". $numbers[$location]['insights'] ."'>" . $numbers[$location][$type] . "</span></span>";
            }
        } elseif ($calltag) {
            if ($linked) {
                echo "<span class='ld-phonenumber'>" . $numbers[$location]['calltag'] . " <a href='tel:" .  str_replace(' ', '', $numbers[$location][$type]) . "'>" . $numbers[$location][$type] . "</a></span>";
            } else {
                echo "<span class='ld-phonenumber'>" . $numbers[$location]['calltag'] . " <span class='ld-number'>" . $numbers[$location][$type] . "</span></span>";
            }
        } else {
            if ($linked) {
                echo "<span class='ld-phonenumber'><a href='tel:" . str_replace(' ', '', $numbers[$location][$type]) . "'>" . $numbers[$location][$type] . "</a></span>";
            } else {
                echo "<span class='ld-phonenumber'>" . $numbers[$location][$type] . "</span>";
            }
        }
    }
}
