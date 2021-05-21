<?php

class ShoppingApi {

    private $customerId;
    private $merchantId;
    private $apiDomain;
    private $token;

    public function __construct() {
        $this->customApiObj = new CustomApi();
        $this->customApiObj->getGoogleAnalyticDetail();

        $queries = new TVC_Queries();
        $this->apiDomain = TVC_API_CALL_URL;
        //$this->apiDomain = 'http://127.0.0.1:8000/api';
        $this->token = 'MTIzNA==';
        $this->merchantId = (isset($GLOBALS['tatvicData']['tvc_merchant'])) ? $GLOBALS['tatvicData']['tvc_merchant'] : "";
        $this->customerId = (isset($GLOBALS['tatvicData']['tvc_customer'])) ? $GLOBALS['tatvicData']['tvc_customer'] : "";
    }

    public function getCampaigns() {
        try {
            $url = $this->apiDomain . '/campaigns/list';

            $data = [
                'merchant_id' => $this->merchantId,
                'customer_id' => $this->customerId
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
            //print_r($request);
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

    public function getCategories($country_code) {
        try {
            $url = $this->apiDomain . '/products/categories';

            $data = [
                'customer_id' => $this->customerId,
                'country_code' => $country_code
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

    public function accountPerformance($date_range_type, $days = 0, $from_date = '', $to_date = '') {
        try {
            $days_diff = 0;
            if ($date_range_type == 2) {
                $days_diff = strtotime($to_date) - strtotime($from_date);
                $days_diff = abs(round($days_diff / 86400));
            }

            $url = $this->apiDomain . '/reports/account-performance';
            $data = [
                'customer_id' => $this->customerId,
                'graph_type' => ($date_range_type == 2 && $days_diff > 31) ? 'month' : 'day',
                'date_range_type' => $date_range_type,
                'days' => $days,
                'from_date' => $from_date,
                'to_date' => $to_date
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
            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
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

    public function campaignPerformance($date_range_type, $days = 0, $from_date = '', $to_date = '') {
        try {
            $url = $this->apiDomain . '/reports/campaign-performance';
            $days_diff = 0;
            if ($date_range_type == 2) {
                $days_diff = strtotime($to_date) - strtotime($from_date);
                $days_diff = abs(round($days_diff / 86400));
            }
            $data = [
                'customer_id' => $this->customerId,
                'graph_type' => ($date_range_type == 2 && $days_diff > 31) ? 'month' : 'day',
                'date_range_type' => $date_range_type,
                'days' => $days,
                'from_date' => $from_date,
                'to_date' => $to_date
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

            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
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

    public function productPerformance($campaign_id = '', $date_range_type='', $days = 0, $from_date = '', $to_date = '') {
        try {
            $url = $this->apiDomain . '/reports/product-performance';

            $data = [
                'merchant_id' => $this->merchantId,
                'customer_id' => $this->customerId,
                'campaign_id' => $campaign_id,
                'date_range_type' => $date_range_type,
                'days' => $days,
                'from_date' => $from_date,
                'to_date' => $to_date
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

            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
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

    public function productPartitionPerformance($campaign_id = '', $date_range_type='', $days = 0, $from_date = '', $to_date = '') {
        try {
            $url = $this->apiDomain . '/reports/product-partition-performance';

            $data = [
                'merchant_id' => $this->merchantId,
                'customer_id' => $this->customerId,
                'campaign_id' => $campaign_id,
                'date_range_type' => $date_range_type,
                'days' => $days,
                'from_date' => $from_date,
                'to_date' => $to_date
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

            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
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

    public function getCampaignDetails($campaign_id = '') {
        try {
            $url = $this->apiDomain . '/campaigns/detail';

            $data = [
                'merchant_id' => $this->merchantId,
                'customer_id' => $this->customerId,
                'campaign_id' => $campaign_id
            ];

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'body' => wp_json_encode($data)
            );

            /* echo "<pre>";
              print_r($args); */
            // Send remote request
            $request = wp_remote_post($url, $args);
            /* print_r($request); */

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));
            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
                $response_body->data->category_id = (isset($response_body->data->category_id)) ? $response_body->data->category_id : '0';
                $response_body->data->category_level = (isset($response_body->data->category_level)) ? $response_body->data->category_level : '0';
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

    public function createCampaign($campaign_name = '', $budget = 0, $target_country = 'US', $all_products = 0, $category_id = '', $category_level = '') {
        try {
            $url = $this->apiDomain . '/campaigns/create';
            $data = [
                'merchant_id' => $this->merchantId,
                'customer_id' => $this->customerId,
                'campaign_name' => $campaign_name,
                'budget' => $budget,
                'target_country' => $target_country,
                'all_products' => $all_products,
                'filter_by' => 'category',
                'filter_data' => ["id" => $category_id, "level" => $category_level]
            ];

            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'body' => wp_json_encode($data)
            );
            /* echo "<pre>"; */
            /* print_r($args); */
            // Send remote request
            $request = wp_remote_post($url, $args);
            /* print_r($request); */

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));
//            echo "<pre>";
//            print_r($response_message);
//            print_r($response_body);
//            exit;
            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
//                return new WP_REST_Response(
//                    array(
//                        'status' => $response_code,
//                        'message' => $response_message,
//                        'data' => $response_body->data
//                    )
//                );
                return $response_body;
            } else {
                //return new WP_Error($response_code, $response_message, $response_body);
                return $response_body;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateCampaign($campaign_name = '', $budget = 0, $campaign_id = '', $budget_id='', $target_country = '', $all_products = 0, $category_id = '', $category_level = '', $ad_group_id = '', $ad_group_resource_name = '') {
        try {
            $url = $this->apiDomain . '/campaigns/update';
            $data = [
                'merchant_id' => $this->merchantId,
                'customer_id' => $this->customerId,
                'campaign_id' => $campaign_id,
                'account_budget_id' => $budget_id,
                'campaign_name' => $campaign_name,
                'target_country' => $target_country,
                'budget' => $budget,
                'status' => 2, // ENABLE => 2, PAUSED => 3, REMOVED => 4
                'all_products' => $all_products,
                'ad_group_id' => $ad_group_id,
                'ad_group_resource_name' => $ad_group_resource_name,
                'filter_by' => 'category',
                'filter_data' => ["id" => $category_id, "level" => $category_level]
            ];
            $args = array(
                'headers' => array(
                    'Authorization' => "Bearer $this->token",
                    'Content-Type' => 'application/json'
                ),
                'method' => 'PATCH',
                'body' => wp_json_encode($data)
            );

            /* echo "<pre>";
              print_r($args); */
            // Send remote request
            $request = wp_remote_request($url, $args);
            /* print_r($request); */

            // Retrieve information
            $response_code = wp_remote_retrieve_response_code($request);
            $response_message = wp_remote_retrieve_response_message($request);
            $response_body = json_decode(wp_remote_retrieve_body($request));

            if (!is_wp_error($request) && (isset($response_body->error) && $response_body->error == '')) {
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

}
