<?php
/**
 * Add no index to staging sites
 */
add_action('wp_head', function() {
    if (strpos($_SERVER['SERVER_NAME'],'adtrak.agency') !== false) {
        echo '<meta name="robots" content="noindex">';
        echo '<meta name="googlebot" content="noindex">';
    }
    if (strpos($_SERVER['SERVER_NAME'],'breeez.com') !== false) {
        echo '<meta name="robots" content="noindex">';
        echo '<meta name="googlebot" content="noindex">';
    }
});

?>
