<?php

$url = '/privacy-policy';

$cookiePage = get_page_by_title('Cookie Policy');
$privacyPage = get_page_by_title('Privacy Policy');

if($cookiePage != null) {
	$cookieStatus = get_post_status($cookiePage->ID);

	if($cookieStatus == 'publish') {
		$url = '/cookie-policy';
	}
}
?>
<div id="wp-notification" class="closed">
	<div class="wp-notification-container">
		<p>This website uses cookies to enhance your browsing experience...</p> 
		<div>
			<a href="<?= site_url($url) ?>/">More information</a>
			<span id="wp-notification-toggle">I understand</span>
		</div>
	</div>
</div>