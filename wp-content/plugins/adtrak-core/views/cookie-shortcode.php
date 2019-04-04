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
		<p>This website uses cookies to enhance your browsing experience... <a href="<?= site_url($url) ?>">more</a><span id="wp-notification-toggle">got it</span></p>
	</div>
</div>