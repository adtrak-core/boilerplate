<?php


namespace PGMB\Google;

use InvalidArgumentException;

class LocalPostOffer extends AbstractGoogleJsonObject {
	public function __construct($couponCode = "", $redeemOnlineUrl = "", $termsConditions = "") {
		$this->setCouponCode($couponCode);
		$this->setRedeemOnlineUrl($redeemOnlineUrl);
		$this->setTermsConditions($termsConditions);
	}

	public function setCouponCode($couponCode){
		$this->jsonOutput['couponCode'] = $couponCode;
	}

	public function setRedeemOnlineUrl($redeemOnlineUrl){
		if($redeemOnlineUrl && esc_url_raw($redeemOnlineUrl) !== $redeemOnlineUrl){
			throw new InvalidArgumentException(__('Offer redeem online URL is invalid', 'post-to-google-my-business'));
		}
		$this->jsonOutput['redeemOnlineUrl'] = $redeemOnlineUrl;
	}

	public function setTermsConditions($termsConditions){
		$this->jsonOutput['termsConditions'] = $termsConditions;
	}

	public static function fromArray($array){
		return new self($array['couponCode'], $array['redeemOnlineUrl'], $array['termsConditions']);
	}
}
