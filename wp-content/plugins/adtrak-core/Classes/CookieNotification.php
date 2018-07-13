<?php
/**
 * Cookie notification plugin.
 *
 * @since      1.0.0
 * @package    AdtrakCore
 * @subpackage AdtrakCore/Classes
 * @author     Jack Whiting <jack.whiting@adtrak.co.uk>
 */

namespace AdtrakCore\Classes;

class CookieNotification
{
	public function __construct($version)
	{
		$this->version = $version;

		$this->create_page();
	}

	/**
	 * Register the shortcodes for the admin.
	 * @since    1.0.0
	 */
	public function register_shortcodes()
	{
		add_shortcode('cookie_notification', [$this, 'shortcode']);
	}

	/**
	 * Include the template for the shortcode notification.
	 * @since    1.0.0
	 */
	public function shortcode()
	{
		include_once AC_PLUGIN_PATH . 'views/cookie-shortcode.php';
	}

	/**
	 * Force policy notification.
	 */
	public function force_policy()
	{
		do_shortcode('[cookie_notification]');
	}


	/**
	 * add the content for the page.
	 * @since    1.0.0
	 */
	public function create_page()
	{
		$title = 'Privacy Policy';
		$post_content = '<h2>1. Cookies</h2><p>By using the website of you consent to the usage of data captured by the use of cookies. Cookies allow us to do multiple things to enhance and improve your browsing experience on our website. If you wish to turn off cookies, please adjust your browser settings. Our website will continue to function without cookies.</p><p>We use cookies to track visitors to our website; these details are in no way personal or identifiable details and will never be shared. Our cookies are for the sole purpose of improving the performance of our website for you, the user; this includes allowing us to geo-target our users, to make websites more personal and relevant to you.</p><p><b>Below are the third party tools we use:</b></p><h3>Google Analytics</h3><p>Page views, source and time spent on website are part of the user website activities information we can see with this cookie. This information cannot be tracked back to any individuals as it is displayed as depersonalised numbers; this is in order to help protect your privacy whilst using our website.</p><p>Using Google Analytics we can take account of which content is popular, helping us to provide you with reading and viewing materials which you will enjoy and find useful in the future.</p><p>We also use Google Analytics Remarketing cookies to display adverts on third party websites to our past site users, based on their past visits. The data we collect will only be used in accordance with our own privacy policy and <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/cookie-usage">Google\'s privacy policy</a>.</p><p>Should you not wish for your website visits to be recorded by Google Analytics, you are able to opt-out with the addition of a browser add-on: <a href="https://tools.google.com/dlpage/gaoptout/">Google Analytics Opt-out Browser Add-on</a></p><h3>Google Analytics Advertiser</h3><p>We use Google Analytics Advertiser Features, which helps us to better understand site visitors, via anonymised data. This can include collecting information from:</p><ul><li>Google Display Network Impression Reporting</li><li>DoubleClick Platform integrations</li><li>Google Analytics Demographics and Interest Reporting</li><li>Remarketing with Google Analytics</li></ul><p>This information is collected via Google advertising cookies and anonymous identifiers, in addition to data collected through the standard Google Analytics implementation. It allows us to understand what type of users visit the site, which then allows us to improve the website’s offerings for a better user experience.</p><h3>Google AdWords</h3><p>We use Google AdWords to see which pages led to our users submitting contact forms to us, which allows us to create a more effective marketing campaign, and make better use of our paid search budget.</p><h3>DoubleClick</h3><p>We use DoubleClick cookies and remarketing codes on our website to record user activity. The information we collect allows us to create targeted advertising in future work and across Google’s network of partners.</p><h3>Website Optimiser</h3><p>Our website optimiser uses cookies to remember your search history. The information collected is anonymous and not personally identifiable, and allows us to generate more relevant results for your searches in the future.</p><h3>Call Tracking</h3><p>We use Call Tracking to set dynamic phone numbers on our site. These help us identify how you found the website when you call us and allows us to identify the source that you used to find the website. It gives a better idea of our users’ requirements and lets us tailor our advertising methods in the future.&nbsp;If you phone us, your call may be recorded for training and quality purposes.</p><h3>Visitor Tracking</h3><p>We often record and monitor user’s behaviour around a website&nbsp;to analyse how we can improve its&nbsp;performance.</p><h2>2. Privacy Policy</h2><p>For each visitor to our web page, our web server will not hold any information regarding the domain or email address.</p><p>If you supply us with your postal address you will only receive the information for which you provided us your address.</p><p>Persons who supply us with their telephone numbers will only receive telephone contact from us with information regarding enquiries placed online.</p><p>If you do not want to receive emails from us in the future, please let us know by sending us an email.</p><h2>3. Personal Identification Information</h2><p>We may collect personal identification information from users in a variety of ways, including, but not limited to when users visit our site, subscribe to our newsletter or fill out a form, and in connection with other activities, services, features or resources we make available on our site. Users may be asked for, as appropriate: name, email address and phone number.</p><p>However, users may visit our site anonymously. We will only collect personal identification information from users if they voluntarily submit such information to us. Users can always refuse to supply personal identification information, except that it may prevent them from engaging in certain site related activities.</p><p>Your personal information may be used to inform any promotional information sent to you about third parties that are deemed to be relevant to your needs. Unless we have your permission, or unless required to do so by law, we will never sell, distribute or lease any personal information to a third party.</p><h2>4. How We Store &amp; Protect Your Information</h2><p>We adopt appropriate data collection, storage and processing practices and security measures to protect against unauthorised access, alteration, disclosure or destruction of your personal information, username, password, transaction information and data stored on our site.</p><p>Consumers can have any inaccurate contact information corrected by sending us an email.</p><h2>5. Sharing your Personal Information</h2><p>We do not sell, trade, or rent users’ personal identification information to others. We may share generic aggregated demographic information not linked to any personal identification information regarding visitors and users with our business partners, trusted affiliates and advertisers for the purposes outlined above.</p><p>We may use third party service providers to help us operate our business and the site or administer activities on our behalf, such as sending out newsletters or surveys. We may share your information with these third parties for those limited purposes provided that you have given us your permission.</p><p>With respect to Ad Servers: We do not partner with or have special relationships with any ad server companies.</p><h2>6. Variations to the Policy</h2><p>From time to time, we may use customer information for new, unanticipated uses that have not been previously disclosed in our privacy notice. If our information practices change at some time in the future we will post the policy changes to our website to notify you of these changes. For these new purposes, we will only use data collected from the time of the policy change forward. If you are concerned about how your information is being used, you should check back at our website periodically.</p><p>Customers may prevent their information from being used for purposes other than those for which it was originally collected by emailing.</p><p>If you feel that this site is not following its stated information policy, you may contact use by email or by phone.</p><p>Please <a href="http://support.google.com/analytics/answer/2700409?hl=en-GB">click here for more information&nbsp;regarding cookies and our use of&nbsp;them</a>.</p>';

		if (get_page_by_title($title) == null) {
			$post = [
				'ping_status' 	=>  'closed' ,
				'post_date' 	=> date('Y-m-d H:i:s'),
				'post_name' 	=> 'privacy-policy',
				'post_status' 	=> 'publish' ,
				'post_title' 	=> $title,
				'post_type' 	=> 'page',
				'post_content' 	=> $post_content
			];

			$post_id = wp_insert_post($post);
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_public_styles()
	{
		//if(! isset($_COOKIE['PrivacyPolicy']) || $_COOKIE['PrivacyPolicy'] == 'closed')
			wp_enqueue_style('adtrak-cookie', AC_PLUGIN_URL . 'assets/css/cookie-public.css', [], $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_public_scripts()
	{
		//if(! isset($_COOKIE['PrivacyPolicy']) || $_COOKIE['PrivacyPolicy'] == 'closed')
			wp_enqueue_script('adtrak-cookie', AC_PLUGIN_URL . 'assets/js/min/cookie-public-min.js', [ 'jquery' ], $this->version, false);
	}
}
