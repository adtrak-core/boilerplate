<?php
class CustomApi{
    private $apiDomain;
    private $token;
    protected $access_token;
    protected $refresh_token;
    public function __construct() {
        $this->apiDomain = TVC_API_CALL_URL;
        $this->token = 'MTIzNA==';
    }
    public function get_tvc_access_token(){
        if(!empty($this->access_token)){
            return $this->access_token;
        }else   if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
            $this->access_token = $_SESSION['access_token'];
            return $this->access_token;
        }else{
            $TVC_Admin_Helper = new TVC_Admin_Helper();
            $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
            $this->access_token = $google_detail['setting']->access_token;
            return $this->access_token;
        }
    }

    public function get_tvc_refresh_token(){
        if(!empty($this->refresh_token)){
            return $this->refresh_token;
        }else   if(isset($_SESSION['refresh_token']) && $_SESSION['refresh_token']){
            $this->refresh_token = $_SESSION['refresh_token'];
            return $this->refresh_token;
        }else{
            $TVC_Admin_Helper = new TVC_Admin_Helper();
            $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
            $this->refresh_token = $google_detail['setting']->refresh_token;
            return $this->refresh_token;
        }
    }
    public function is_allow_call_api(){
        $ee_options_data = unserialize(get_option('ee_options'));
        if(isset($ee_options_data['subscription_id'])) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getFeatureList() {
        try {

            // $this->header = array("Authorization: Bearer MTIzNA==", "content-type: application/json");
            // $this->curl_url = "http://127.0.0.1:8000/api/plans/feature-list";
            // $postData = json_encode(["plan_id" => 1]);
            // $ch = curl_init();
            // curl_setopt_array($ch, array(
            //     CURLOPT_URL => esc_url($this->curl_url),
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_TIMEOUT => 20,
            //     CURLOPT_HTTPHEADER => $this->header,
            //     CURLOPT_POSTFIELDS => $postData
            // ));
            // $this->response = curl_exec($ch);
            // $this->response = json_decode($this->response);
            // die;

            $url = $this->apiDomain . '/plans/feature-list';

            $data = [
                'plan_id' => 1,
            ];
            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'body' => wp_json_encode($data)
            );
            // Send remote request
            $request = wp_remote_post($url, $args);
            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));

            if ((isset($response_body->error) && $response_body->error == '')) {

                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getGoogleAnalyticDetail() {
        try {
            $url = $this->apiDomain . '/customer-subscriptions/subscription-detail';
            $header = array(
                "Authorization: Bearer ".$this->token,
                "content-type: application/json"
            );
            $ee_options_data = unserialize(get_option('ee_options'));
            if(isset($ee_options_data['subscription_id'])) {
                $ee_subscription_id = $ee_options_data['subscription_id'];
            } else {
                $ee_subscription_id = null;
            }
            $actual_link = get_site_url();
            $data = [
                'subscription_id' => $ee_subscription_id,
                'domain' => $actual_link
            ];
            $postData = json_encode($data);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 1000,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_POSTFIELDS => $postData
            ));
            $response = curl_exec($ch);
            $response_body = json_decode($response);         
            if((isset($response_body->error) && $response_body->error == '')) {
                if ($response_body->data) {
                    $store_raw_country = get_option('woocommerce_default_country');
                    // Split the country/state
                    $split_country = explode(":", $store_raw_country);

                    $GLOBALS['tatvicData']['tvc_customer'] = $response_body->data->google_ads_id;
                    $GLOBALS['tatvicData']['tvc_merchant'] = $response_body->data->google_merchant_center_id;
                    $GLOBALS['tatvicData']['tvc_account'] = $response_body->data->ua_analytic_account_id;
                    $GLOBALS['tatvicData']['tvc_subscription'] = $response_body->data->id;
                    $GLOBALS['tatvicData']['tvc_country'] = $split_country[0];
                    $GLOBALS['tatvicData']['tvc_gmc_id'] = $response_body->data->google_merchant_center_id;
                    $GLOBALS['tatvicData']['tvc_main_merchant_id'] = $response_body->data->merchant_id;
                    $GLOBALS['tatvicData']['tvc_site_url'] = $response_body->data->site_url;
                    $GLOBALS['tatvicData']['tvc_track_opt'] = $response_body->data->tracking_option;
                    $GLOBALS['tatvicData']['access_token'] = $response_body->data->access_token;
                    $GLOBALS['tatvicData']['refresh_token'] = $response_body->data->refresh_token;
                    $_SESSION['access_token'] = $response_body->data->access_token;
                    $_SESSION['refresh_token'] = $response_body->data->refresh_token;
                }
                $return = new \stdClass();
                if (isset($response_body->error) && $response_body->error == '') {
                    $return->error = false;
                    $return->data = $response_body->data;
                    $return->message = $response_body->message;
                    return $return;
                } else {
                    if (isset($response_body->data)) {
                        $return->error = false;
                        $return->data = $response_body->data;
                        $return->message = $response_body->message;
                    } else {
                        $return->error = true;
                        $return->data = [];
                        if(isset($response_body->errors->key[0])){
                            $return->message = $response_body->errors->key[0]; 
                        }else{
                            $return->message = "Please try after some time.";
                        }
                    }
                    return $return;
                }
                //return (object) array( 'status' => $response_code, 'message' => $response_message, 'data' => $response_body->data );
            } 
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateTrackingOption($postData) {
        try {
            $url = $this->apiDomain . '/customer-subscriptions/tracking-options';

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'method' => 'PATCH',
                'body' => wp_json_encode($postData)
            );

            // Send remote request
            $request = wp_remote_post($url, $args);

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));

            if ((isset($response_body->error) && $response_body->error == '')) {

                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function active_licence_Key($licence_key, $subscription_id) {
        try {
            $header = array(
                "Authorization: Bearer MTIzNA==",
                "content-type: application/json"
            );
            $curl_url = $this->apiDomain . "/licence/activation";
            $postData = [
                'key' => $licence_key,
                'domain' => get_site_url(),
                'subscription_id'=>$subscription_id
            ];
            $postData = json_encode($postData);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => esc_url($curl_url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 1000,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_POSTFIELDS => $postData
            ));
            $response = curl_exec($ch);
            $response = json_decode($response);
            $return = new \stdClass();
            if (isset($response->error) && $response->error == '') {
                $return->error = false;
                $return->data = $response->data;
                $return->message = $response->message;
                return $return;
            } else {
                if (isset($response->data)) {
                    $return->error = false;
                    $return->data = $response->data;
                    $return->message = $response->message;
                } else {
                    $return->error = true;
                    $return->data = [];
                    if(isset($response->errors->key[0])){
                        $return->message = $response->errors->key[0]; 
                    }else{
                        $return->message = "Check your entered licese key.";
                    }  

                }
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function verifyLicenceKey($licence_key, $subscription_id) {
        try {
            echo $url = $this->apiDomain . '/licence/verify-key';
            $data = [
                'key' => $licence_key,
                'domain' => get_site_url()
            ];
            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'method' => 'POST',
                'body' => wp_json_encode($data)
            );
            // Send remote request
            $request = wp_remote_post($url, $args);
            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));
            if ((isset($response_body->error) && $response_body->error == '')) {
                return new WP_REST_Response(array('status' => $response_code, 'message' => $response_message,'data' => $response_body->data ));
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function setGmcCategoryMapping($postData) {
        try {
            $url = $this->apiDomain . '/gmc/gmc-category-mapping';

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'method' => 'POST',
                'body' => wp_json_encode($postData)
            );

            // Send remote request
            $request = wp_remote_post($url, $args);

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));

            if ((isset($response_body->error) && $response_body->error == '')) {

                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function setGmcAttributeMapping($postData) {
        try {
            $url = $this->apiDomain . '/gmc/gmc-attribute-mapping';

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'method' => 'POST',
                'body' => wp_json_encode($postData)
            );

            // Send remote request
            $request = wp_remote_post($url, $args);

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));

            if ((isset($response_body->error) && $response_body->error == '')) {

                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSyncProductList($postData) {
        try { 
            $postData["maxResults"] = 100;
            $header = array(
                "Authorization: Bearer MTIzNA==",
                "content-type: application/json",
                "AccessToken:".$this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token())
            );
            $curl_url = $this->apiDomain . "/products/list";
            $postData = json_encode($postData);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => esc_url($curl_url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 1000,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_POSTFIELDS => $postData
            ));
            $response = curl_exec($ch);
            $response = json_decode($response);
            $return = new \stdClass();
            if (isset($response->error) && $response->error == '') {
                $return->status = 200;
                $return->data = $response->data;
                $return->message = $response->message;
                return $return;
            } else {
                if (isset($response->data)) {
                    $return->data = $response->data;
                    $return->message = $response->message;
                } else {
                    $return->data = [];
                    $return->message = '';
                }
                return $return;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function createCustomDimensions($postData) {
        try {

            $url = $this->apiDomain . '/google-analytics/dimensions/insert';

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json',
                    'AccessToken' => $this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token())
                ),
                'body' => wp_json_encode($postData)
            );
            // Send remote request
            $request = wp_remote_post($url, $args);
                        
            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));

            if ((isset($response_body->error) && $response_body->error == '')) {
                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                //return new WP_Error($response_code, $response_message, $response_body);
                return $response_body;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function createCustomMetrics($postData) {
        try {
            $url = $this->apiDomain . '/google-analytics/metrics/insert';

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json',
                    'AccessToken' => $this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token())
                ),
                'body' => wp_json_encode($postData)
            );

            // Send remote request
            
            $request = wp_remote_post($url, $args);
            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));

            if ((isset($response_body->error) && $response_body->error == '')) {
                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                //return new WP_Error($response_code, $response_message, $response_body);
                return $response_body;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSubscriptionDetail($id) {
        try {
            $url = $this->apiDomain . '/customer-subscriptions/subscription-detail';
            $actual_link = get_site_url();
            $data = [
                'subscription_id' => $id,
                'domain' => $actual_link
            ];
            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json',
                ),
                'body' => wp_json_encode($data)
            );

            // Send remote request
            $request = wp_remote_post($url, $args);

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));
            if ((isset($response_body->error) && $response_body->error == '')) {

                return new WP_REST_Response(
                    array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCampaignCurrencySymbol($postData) {
        try {
            $url = $this->apiDomain . '/campaigns/currency-symbol';

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json',
                //'AccessToken' => $this->generateAccessToken($_SESSION['access_token'], $_SESSION['access_token'])
                ),
                'body' => wp_json_encode($postData)
            );

            // Send remote request
            $request = wp_remote_post($url, $args);

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));
            if ((isset($response_body->error) && $response_body->error == '')) {

                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function generateAccessToken($access_token, $refresh_token) {
        
        $request = "https://www.googleapis.com/oauth2/v1/tokeninfo?"
                . "access_token=" . $access_token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        $result = json_decode($response);
        if (isset($result->error) && $result->error) {
            $credentials_file = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/client-secrets.json';
            $str = file_get_contents($credentials_file);
            $credentials = $str ? json_decode($str, true) : [];
            $url = 'https://www.googleapis.com/oauth2/v4/token';
            $header = array("content-type: application/json");
            $clientId = $credentials['web']['client_id'];
            $clientSecret = $credentials['web']['client_secret'];
            $refreshToken = $refresh_token;
            $data = [
                "grant_type" => 'refresh_token',
                "client_id" => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
            ];

            $postData = json_encode($data);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url, //esc_url($this->curl_url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_POSTFIELDS => $postData
            ));
            $response = curl_exec($ch);
            
            $response = json_decode($response);
            
            if(isset($response->access_token)){
                return $response->access_token; 
            }else{
                return $access_token;
            }
           
        } else {
            return $access_token;
        }
    }

    public function updateShowSetupTimeFlag($postData) {
        try {
            $url = $this->apiDomain . '/customer-subscriptions/update-setup-time';

            $data = [
                'subscription_id' => $postData['subscription_id'],
                'show_setup_time' => 0,
            ];
            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json',
                ),
                'body' => wp_json_encode($data)
            );
            // Send remote request
            $request = wp_remote_post($url, $args);

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));
            if ((isset($response_body->error) && $response_body->error == '')) {

                return new WP_REST_Response(
                        array(
                    'status' => $response_code,
                    'message' => $response_message,
                    'data' => $response_body->data
                        )
                );
            } else {
                return new WP_Error($response_code, $response_message, $response_body);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function siteVerificationToken($postData) {
        try {
            $url = $this->apiDomain . '/gmc/site-verification-token';
            $this->header = array("Authorization: Bearer MTIzNA==",
                "content-type: application/json",
                "AccessToken:" . $this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token())
            );

            $data = [
                'merchant_id' => $postData['merchant_id'],
                'website' => $postData['website_url'],
                'account_id' => $postData['account_id'],
                'method' => $postData['method']
            ];

            $this->curl_url = $url;

            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $this->curl_url, //esc_url($this->curl_url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTPHEADER => $this->header,
                CURLOPT_POSTFIELDS => $data
            ));
            $this->response = curl_exec($ch);
            $this->response = json_decode($this->response);
            return $this->response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function siteVerification($postData) {
        try {
            $url = $this->apiDomain . '/gmc/site-verification';
            $this->header = array("Authorization: Bearer MTIzNA==",
                "content-type: application/json",
               "AccessToken:" . $this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token())
            );

            $data = [
                'merchant_id' => $postData['merchant_id'],
                'website' => $postData['website_url'],
                'subscription_id' => $postData['subscription_id'],
                'account_id' => $postData['account_id'],
                'method' => $postData['method']
            ];

            $this->curl_url = $url;

            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $this->curl_url, //esc_url($this->curl_url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTPHEADER => $this->header,
                CURLOPT_POSTFIELDS => $data
            ));
            $this->response = curl_exec($ch);
            $this->response = json_decode($this->response);
            return $this->response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function claimWebsite($postData) {
        try {
            $url = $this->apiDomain . '/gmc/claim-website';
            $this->header = array("Authorization: Bearer MTIzNA==",
                "content-type: application/json",
                "AccessToken:" . $this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token())
            );
            $data = [
                'merchant_id' => $postData['merchant_id'],
                'account_id' => $postData['account_id'],
                'website' => $postData['website_url'],
                'access_token' => $this->generateAccessToken($this->get_tvc_access_token(), $this->get_tvc_refresh_token()),
                'subscription_id' => $postData['subscription_id'],
            ];

            $this->curl_url = $url;

            $data = json_encode($data);
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $this->curl_url, //esc_url($this->curl_url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTPHEADER => $this->header,
                CURLOPT_POSTFIELDS => $data
            ));
            $this->response = curl_exec($ch);
            $this->response = json_decode($this->response);
            return $this->response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}