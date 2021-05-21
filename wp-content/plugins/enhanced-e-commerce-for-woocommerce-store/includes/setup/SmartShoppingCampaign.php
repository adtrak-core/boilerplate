<?php

//require __DIR__ . '/../../vendor/autoload.php';

use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V4\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V4\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V4\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V4\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V4\Resources\Campaign\ShoppingSetting;
use Google\Ads\GoogleAds\V4\Resources\Campaign;
use Google\Ads\GoogleAds\V4\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V4\Enums\AdvertisingChannelSubTypeEnum\AdvertisingChannelSubType;
use Google\Ads\GoogleAds\V4\Services\CampaignOperation;
use Google\Ads\GoogleAds\V4\Common\MaximizeConversionValue;
use Google\Ads\GoogleAds\V4\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V4\Resources\AdGroup;
use Google\Ads\GoogleAds\V4\Enums\AdGroupStatusEnum\AdGroupStatus;
use Google\Ads\GoogleAds\V4\Enums\AdGroupTypeEnum\AdGroupType;
use Google\Ads\GoogleAds\V4\Services\AdGroupOperation;
use Google\Ads\GoogleAds\V4\Resources\AdGroupAd;
use Google\Ads\GoogleAds\V4\Resources\Ad;
use Google\Ads\GoogleAds\V4\Services\AdGroupAdOperation;
use Google\Ads\GoogleAds\V4\Common\ShoppingSmartAdInfo;
use Google\Ads\GoogleAds\V4\Resources\AdGroupCriterion;
use Google\Ads\GoogleAds\V4\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;
use Google\Ads\GoogleAds\V4\Common\ListingGroupInfo;
use Google\Ads\GoogleAds\V4\Services\AdGroupCriterionOperation;
use Google\Ads\GoogleAds\V4\Enums\ListingGroupTypeEnum\ListingGroupType;
use Google\Ads\GoogleAds\Lib\V4\GoogleAdsException;
use Google\ApiCore\ApiException;
use Google\Ads\GoogleAds\V4\Resources\ConversionAction;
use Google\Ads\GoogleAds\V4\Enums\ConversionActionCategoryEnum\ConversionActionCategory;
use Google\Ads\GoogleAds\V4\Enums\ConversionActionTypeEnum\ConversionActionType;
use Google\Ads\GoogleAds\V4\Enums\ConversionActionStatusEnum\ConversionActionStatus;
use Google\Ads\GoogleAds\V4\Services\ConversionActionOperation;
use Google\Ads\GoogleAds\Util\V4\ResourceNames;

class SmartShoppingCampaign
{
    private $customerId;
    private $merchantId;
    private $credentials;
    private $createDefaultListing;
    private $oAuth2Credential;
    private $googleAdsClient;
    private $budgetResourceName;

    public function __construct()
    {
        $queries = new TVC_Queries();

        $this->customerId = $queries->get_set_ads_account_id();
        // $this->customerId = TVC_CUSTOMER;
        $this->merchantId = $queries->get_set_merchant_id();
        // $this->merchantId = TVC_MERCHANT;
        $this->merchantId = (isset($GLOBALS['tatvicData']['tvc_merchant'])) ? $GLOBALS['tatvicData']['tvc_merchant']:"";
        $this->customerId = (isset($GLOBALS['tatvicData']['tvc_customer'])) ? $GLOBALS['tatvicData']['tvc_customer']:"";

        $this->createDefualtListing = true;

        $credentials_file = ENHANCAD_PLUGIN_DIR.'includes/setup/json/client-secrets.json';
        $str = file_get_contents($credentials_file);
        $this->credentials = $str ? json_decode($str, true) : [];

        // Generate a refreshable OAuth2 credential for authentication.
        $this->oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId($this->credentials['web']['client_id'])
            ->withClientSecret($this->credentials['web']['client_secret'])
            ->withRefreshToken($this->credentials['web']['manager_refresh_token'])
            ->build();

        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        $this->googleAdsClient = (new GoogleAdsClientBuilder())
            ->fromFile(ENHANCAD_PLUGIN_DIR.'/google_ads_php.ini')
            ->withOAuth2Credential($this->oAuth2Credential)
            ->build();

    }

    public function getCustomerCurrency() {
        $customerServiceClient = $this->googleAdsClient->getCustomerServiceClient();
        $customer = $customerServiceClient->getCustomer(ResourceNames::forCustomer($this->customerId));
        $currencyCode = $customer->getCurrencyCode();
        return $currencyCode->getValue();
    }
    public function createConversionAction() {
        // Creates a conversion action.
        $conversionAction = new ConversionAction([
            'name' => 'Conversion Action #' . uniqid(),
            'category' => ConversionActionCategory::PBDEFAULT,
            'type' => ConversionActionType::WEBPAGE,
            'status' => ConversionActionStatus::ENABLED,
            'view_through_lookback_window_days' => 15,
//            'value_settings' => new ValueSettings([
//                'default_value' => 23.41,
//                'always_use_default_value' => true
//            ])
        ]);

        // Creates a conversion action operation.
        $conversionActionOperation = new ConversionActionOperation();
        $conversionActionOperation->setCreate($conversionAction);

        // Issues a mutate request to add the conversion action.
        $conversionActionServiceClient = $this->googleAdsClient->getConversionActionServiceClient();
        $response = $conversionActionServiceClient->mutateConversionActions(
            $this->customerId,
            [$conversionActionOperation]
        );

        // printf("Added %d conversion actions:%s", $response->getResults()->count(), PHP_EOL);

        foreach ($response->getResults() as $addedConversionAction) {
            return $addedConversionAction->getResourceName();
            /** @var ConversionAction $addedConversionAction */
            printf(
                "New conversion action added with resource name: '%s'%s",
                $addedConversionAction->getResourceName(),
                PHP_EOL
            );
        }
    }
    /**
     * Creates a new campaign budget for Smart Shopping ads in the specified client account.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the customer ID
     * @return string the resource name of the newly created budget
     */
    public function addCampaignBudget($campaign_name, $budget) {
        // Creates a campaign budget.
        $budget = new CampaignBudget([
            'name' => $campaign_name .'Budget #' . uniqid(),
            'delivery_method' => BudgetDeliveryMethod::STANDARD,
            // The budget is specified in the local currency of the account.
            // The amount should be specified in micros, where one million is equivalent to one
            // unit.
            'amount_micros' => $budget * 1000000,
            // Budgets for Smart Shopping campaigns cannot be shared.
            'explicitly_shared' => false
        ]);

        // Creates a campaign budget operation.
        $campaignBudgetOperation = new CampaignBudgetOperation();
        $campaignBudgetOperation->setCreate($budget);

        // Issues a mutate request.
        $campaignBudgetServiceClient = $this->googleAdsClient->getCampaignBudgetServiceClient();
        $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
            $this->customerId,
            [$campaignBudgetOperation]
        );

        /** @var CampaignBudget $addedBudget */
        $addedBudget = $response->getResults()[0];
//        printf(
//            "Added a budget with resource name '%s'.%s",
//            $addedBudget->getResourceName(),
//            PHP_EOL
//        );
        $this->budgetResourceName = $addedBudget->getResourceName();
        return $addedBudget->getResourceName();
    }

    /**
     * Creates a new shopping campaign for Smart Shopping ads in the specified client account.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the customer ID
     * @param string $budgetResourceName the resource name of budget for a new campaign
     * @param int $merchantCenterAccountId the Merchant Center account ID
     * @return string the resource name of the newly created campaign
     */

    public function addSmartShoppingCampaign($campaign_name, $budgetResource, $salesCountry) {

        if($salesCountry == '') {
            $salesCountry = 'US';
        }
        // Configures the shopping settings for Smart Shopping campaigns.
        $shoppingSettings = new ShoppingSetting([
            // Sets the sales country of products to include in the campaign.
            // Only products from Merchant Center targeting this country will appear in the
            // campaign.
            'sales_country' => $salesCountry,
            'merchant_id' => $this->merchantId
        ]);

        // Creates the campaign.
        $campaign = new Campaign([
            'name' => $campaign_name .'Campaign #' . uniqid(),
            // Configures settings related to shopping campaigns including
            // advertising channel type, advertising sub-type and shopping setting.
            'advertising_channel_type' => AdvertisingChannelType::SHOPPING,
            'advertising_channel_sub_type' => AdvertisingChannelSubType::SHOPPING_SMART_ADS,
            'shopping_setting' => $shoppingSettings,
            // Recommendation: Set the campaign to PAUSED when creating it to prevent
            // the ads from immediately serving. Set to ENABLED once you've added
            // targeting and the ads are ready to serve.
            'status' => CampaignStatus::PAUSED,
            // 'bidding_strategy_type' => BiddingStrategyType::TARGET_CPA,
            // Bidding strategy must be set directly on the campaign.
            // Setting a portfolio bidding strategy by resource name is not supported.
            // Maximize conversion value is the only strategy supported for Smart Shopping
            // campaigns.
            // An optional ROAS (Return on Advertising Spend) can be set for
            // MaximizeConversionValue.
            // The ROAS value must be specified as a ratio in the API. It is calculated by dividing
            // "total value" by "total spend".
            // For more information on maximize conversion value, see the support article:
            // http://support.google.com/google-ads/answer/7684216.
            'maximize_conversion_value' => new MaximizeConversionValue(['target_roas' => 3.5]),
            // Sets the budget.
            'campaign_budget' => $budgetResource
        ]);

        // Creates a campaign operation.
        $campaignOperation = new CampaignOperation();
        $campaignOperation->setCreate($campaign);

        // Issues a mutate request to add the campaign.
        $campaignServiceClient = $this->googleAdsClient->getCampaignServiceClient();
        $response = $campaignServiceClient->mutateCampaigns($this->customerId, [$campaignOperation]);

        /** @var Campaign $addedCampaign */
        $addedCampaign = $response->getResults()[0];
        $addedCampaignResourceName = $addedCampaign->getResourceName();
//        printf(
//            "Added a Smart Shopping campaign with resource name: '%s'.%s",
//            $addedCampaignResourceName,
//            PHP_EOL
//        );

        return $addedCampaignResourceName;
    }

    /**
     * Creates a new ad group in the specified Smart Shopping campaign.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the customer ID
     * @param string $campaignResourceName the resource name of the campaign that
     *     the new ad group will belong to
     * @return string the resource name of the newly created ad group
     */
    private function addSmartShoppingAdGroup($campaignResourceName) {
        // Creates an ad group.
        $adGroup = new AdGroup([
            'name' => $campaignResourceName .'Ad Group #' . uniqid(),
            'campaign' => $campaignResourceName,
            // Sets the ad group type to SHOPPING_SMART_ADS. This cannot be set to other types.
            'type' => AdGroupType::SHOPPING_SMART_ADS,
            'status' => AdGroupStatus::ENABLED
        ]);

        // Creates an ad group operation.
        $adGroupOperation = new AdGroupOperation();
        $adGroupOperation->setCreate($adGroup);

        // Issues a mutate request to add the ad group.
        $adGroupServiceClient = $this->googleAdsClient->getAdGroupServiceClient();
        $response = $adGroupServiceClient->mutateAdGroups($this->customerId, [$adGroupOperation]);

        /** @var AdGroup $addedAdGroup */
        $addedAdGroup = $response->getResults()[0];
        $addedAdGroupResourceName = $addedAdGroup->getResourceName();
//        printf(
//            "Added a Smart Shopping ad group with resource name: '%s'.%s",
//            $addedAdGroupResourceName,
//            PHP_EOL
//        );

        return $addedAdGroupResourceName;
    }

    /**
     * Creates a new ad group ad in the specified Smart Shopping ad group.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the customer ID
     * @param string $adGroupResourceName the resource name of the ad group that
     *     the new ad group ad will belong to
     */
    private function addSmartShoppingAdGroupAd($adGroupResourceName) {
        // Creates a new ad group ad.
        $adGroupAd = new AdGroupAd([
            // Sets a new Smart Shopping ad.
            'ad' => new Ad(['shopping_smart_ad' => new ShoppingSmartAdInfo()]),
            // Sets the ad group.
            'ad_group' => $adGroupResourceName
        ]);

        // Creates an ad group ad operation.
        $adGroupAdOperation = new AdGroupAdOperation();
        $adGroupAdOperation->setCreate($adGroupAd);

        // Issues a mutate request to add the ad group ad.
        $adGroupAdServiceClient = $this->googleAdsClient->getAdGroupAdServiceClient();
        $response = $adGroupAdServiceClient->mutateAdGroupAds($this->customerId, [$adGroupAdOperation]);

        /** @var AdGroupAd $addedAdGroupAd */
        $addedAdGroupAd = $response->getResults()[0];
//        printf(
//            "Added a Smart Shopping ad group ad with resource name: '%s'.%s",
//            $addedAdGroupAd->getResourceName(),
//            PHP_EOL
//        );
    }

    /**
     * Creates a new Shopping listing group for the specified ad group. This is known as a "product
     * group" in the Google Ads user interface. The listing group will be added to the ad group
     * using an "ad group criterion". For more information on listing groups see the Google Ads
     * API Shopping guide: https://developers.google.com/google-ads/api/docs/shopping-ads/overview.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the customer ID
     * @param string $adGroupResourceName the resource name of the ad group that
     *     the new listing group will belong to
     */
    private function addShoppingListingGroup($adGroupResourceName) {
        // Creates a new ad group criterion. This will contain a listing group.
        // This will be the listing group for 'All products' and will contain a single root node.
        $adGroupCriterion = new AdGroupCriterion([
            'ad_group' => $adGroupResourceName,
            'status' => AdGroupAdStatus::ENABLED,
            // Creates a new listing group. This will be the top-level "root" node.
            // Sets the type of the listing group to be a biddable unit.
            'listing_group' => new ListingGroupInfo(['type' => ListingGroupType::UNIT])
            // Note: Listing groups do not require bids for Smart Shopping campaigns.
        ]);

        // Creates an ad group criterion operation.
        $adGroupCriterionOperation = new AdGroupCriterionOperation();
        $adGroupCriterionOperation->setCreate($adGroupCriterion);

        // Issues a mutate request to add the ad group criterion.
        $adGroupCriterionServiceClient = $this->googleAdsClient->getAdGroupCriterionServiceClient();
        $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria(
            $this->customerId,
            [$adGroupCriterionOperation]
        );

        /** @var AdGroupCriterion $addedAdGroupCriterion */
        $addedAdGroupCriterion = $response->getResults()[0];
//        printf(
//            "Added an ad group criterion containing a listing group with resource name: '%s'.%s",
//            $addedAdGroupCriterion->getResourceName(),
//            PHP_EOL
//        );
    }

    /**
     * @return array
     * Get woocommerce default set country
     */
    public function woo_country(){
        // The country/state
        $store_raw_country = get_option( 'woocommerce_default_country' );
        // Split the country/state
        $split_country = explode( ":", $store_raw_country );
        return $split_country;
    }

    public function createSmartShoppingCampaign($campaign_name = '', $budget = '', $salesCountry = '') {

        try {
            if((isset($_COOKIE['add_conversions']) && $_COOKIE['add_conversions'] == 1) || !isset($_COOKIE['add_conversions'])) {
                self::createConversionAction();
            }

            // Creates a budget to be used by the campaign that will be created below.
            $budgetResourceName = self::addCampaignBudget($campaign_name, $budget);

            // Creates a Smart Shopping campaign.
            $campaignResourceName = self::addSmartShoppingCampaign($campaign_name, $budgetResourceName, $salesCountry);

            // Creates a Smart Shopping ad group.
            $adGroupResourceName = self::addSmartShoppingAdGroup($campaignResourceName);

            // Creates a Smart Shopping ad group ad.
            self::addSmartShoppingAdGroupAd($adGroupResourceName);

            if ($this->createDefaultListing) {
                // A product group is a subset of inventory. Listing groups are the equivalent
                // of product groups in the API and allow you to bid on the chosen group or
                // exclude a group from bidding.
                // This method creates an ad group criterion containing a listing group.
                self::addShoppingListingGroup($adGroupResourceName);
            }

            return $campaignResourceName;

        } catch (GoogleAdsException $googleAdsException) {
            printf(
                "Request with ID '%s' has failed.%sGoogle Ads failure details:%s",
                $googleAdsException->getRequestId(),
                PHP_EOL,
                PHP_EOL
            );
            foreach ($googleAdsException->getGoogleAdsFailure()->getErrors() as $error) {
                /** @var GoogleAdsError $error */
                $response = [];
                $response['error'] = $error->getMessage();
                return $response;
//                printf(
//                    "\t%s: %s%s",
//                    $error->getErrorCode()->getErrorCode(),
//                    $error->getMessage(),
//                    PHP_EOL
//                );
            }
            exit(1);
        } catch (ApiException $apiException) {
            $response = [];
            $response['error'] = $apiException->getMessage();
            return $response;
//            printf(
//                "ApiException was thrown with message '%s'.%s",
//                $apiException->getMessage(),
//                PHP_EOL
//            );
            exit(1);
        }



    }
}
