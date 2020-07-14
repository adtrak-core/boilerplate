<?php


namespace PGMB\Google;

use JsonSerializable;
use InvalidArgumentException;

class CallToAction extends AbstractGoogleJsonObject {
	private $actionTypes = [
		'ACTION_TYPE_UNSPECIFIED',
		'BOOK',
		'ORDER',
		'SHOP',
		'LEARN_MORE',
		'SIGN_UP',
		'GET_OFFER', //Deprecated
		'CALL'
	];


	public function __construct($actionType, $url) {
		$this->setActionType($actionType);
		$this->setUrl($url);
	}

	public function setActionType($actionType){
		if(!in_array($actionType, $this->actionTypes)){
			throw new InvalidArgumentException(__('Invalid call to action', 'post-to-google-my-business'));
		}
		$this->jsonOutput['actionType'] = $actionType;
	}

	public function setUrl($url){
		if($this->jsonOutput['actionType'] === 'CALL'){
			unset($this->jsonOutput['url']);
			return;
		} //CALL action doesnt use URL

		if(esc_url_raw($url) !== $url){
			throw new InvalidArgumentException(__('Invalid URL supplied for call to action', 'post-to-google-my-business'));
		}

		$this->jsonOutput['url'] = $url;
	}

	public static function fromArray( $array ) {
		return new self($array['actionType'], $array['url']);
	}
}
