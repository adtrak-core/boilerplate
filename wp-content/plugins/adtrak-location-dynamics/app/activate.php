<?php 

/** @var  \Billy\Framework\Enqueue $enqueue */

// data to send in our API request
$api_params = array(
    'edd_action' => 'activate_license',
    'license'    => 'ADTRAKLOCATIONDYNAMICS',
    'item_name'  => urlencode( 'Location Dynamics' ), // the name of our product in EDD
    'url'        => home_url()
);
// Call the custom API.
$response = wp_remote_post( ADTK_HOME_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );