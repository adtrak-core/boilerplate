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
	 * add the content for the page.
	 * @since    1.0.0
	 */
	public function create_page()
	{
		$title = 'Privacy Policy';
		$post_content = '<h2>Cookies</h2><p>Cookies allow us to do multiple things to enhance and improve your browsing experience on our website.</p><p>We use cookies to track visitors to our website; these details are in no way personal details and will never be shared. Using these cookies we can improve the performance of our website for you, the user.</p><p>We also use cookies in order to geo-target specific users to make websites more personal.</p><p>By using the website of you consent to the usage of data captured by the use of cookies. If you wish to turn off cookies, please adjust your browser settings. Our website will continue to function without cookies.</p><h2>Personal identification information</h2><p>We may collect personal identification information from Users in a variety of ways, including, but not limited to, when Users visit our site, subscribe to the newsletter, fill out a form, and in connection with other activities, services, features or resources we make available on our Site. Users may be asked for, as appropriate, name, email address, phone number. Users may, however, visit our Site anonymously. We will collect personal identification information from Users only if they voluntarily submit such information to us. Users can always refuse to supply personally identification information, except that it may prevent them from engaging in certain Site related activities.</p><h2>How we protect your information</h2><p>We adopt appropriate data collection, storage and processing practices and security measures to protect against unauthorized access, alteration, disclosure or destruction of your personal information, username, password, transaction information and data stored on our Site.</p><h2>Sharing your personal information</h2><p>We do not sell, trade, or rent Users personal identification information to others. We may share generic aggregated demographic information not linked to any personal identification information regarding visitors and users with our business partners, trusted affiliates and advertisers for the purposes outlined above.We may use third party service providers to help us operate our business and the Site or administer activities on our behalf, such as sending out newsletters or surveys. We may share your information with these third parties for those limited purposes provided that you have given us your permission.</p><h2>Privacy Policy</h2><p>This is the web site of .</p><p>Our postal address is:</p><p>We can be reached via e-mail at or you can reach us by telephone at .</p><p>For each visitor to our Web page, our Web server automatically recognises no information regarding the domain or e-mail address.</p><p>We collect the e-mail addresses of those who communicate with us via e-mail, name and address, telephone number.</p><p>The information we collect is used for internal review.</p><p>If you do not want to receive e-mail from us in the future, please let us know by sending us e-mail at the above address.</p><p>If you supply us with your postal address on-line you will only receive the information for which you provided us your address.</p><p>Persons who supply us with their telephone numbers on-line will only receive telephone contact from us with information regarding enquiries placed on line.</p><p>With respect to Ad Servers: We do not partner with or have special relationships with any ad server companies.</p><p>From time to time, we may use customer information for new, unanticipated uses not previously disclosed in our privacy notice. If our information practices change at some time in the future we will post the policy changes to our Web site to notify you of these changes and we will use for these new purposes only data collected from the time of the policy change forward. If you are concerned about how your information is used, you should check back at our Web site periodically.</p><p>Customers may prevent their information from being used for purposes other than those for which it was originally collected by e-mailing us at the above address.</p><p>Upon request we provide site visitors with access to no information that we have collected and that we maintain about them.</p><p>Upon request we offer visitors the ability to have inaccuracies corrected in contact information.</p><p>Consumers can have this information corrected by sending us e-mail at the above address.</p><p>If you feel that this site is not following its stated information policy, you may contact us at the above addresses or phone number.</p>';

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
		if(! isset($_COOKIE['PrivacyPolicy']) || $_COOKIE['PrivacyPolicy'] == 'closed')
			wp_enqueue_style('adtrak-cookie', AC_PLUGIN_URL . 'assets/css/cookie-public.css', [], $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_public_scripts()
	{
		if(! isset($_COOKIE['PrivacyPolicy']) || $_COOKIE['PrivacyPolicy'] == 'closed')
			wp_enqueue_script('adtrak-cookie', AC_PLUGIN_URL . 'assets/js/min/cookie-public-min.js', [ 'jquery' ], $this->version, false);
	}
}
