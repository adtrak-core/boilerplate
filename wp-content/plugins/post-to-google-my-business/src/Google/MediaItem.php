<?php


namespace PGMB\Google;

use InvalidArgumentException;

class MediaItem extends AbstractGoogleJsonObject {
	private $mediaFormat;
	private $mediaFormats = [
		'MEDIA_FORMAT_UNSPECIFIED',
		'PHOTO',
		'VIDEO',
	];

	public function __construct($mediaFormat, $sourceUrl) {
		$this->setMediaFormat($mediaFormat);
		$this->setSourceUrl($sourceUrl);
	}

	public function setMediaFormat($mediaFormat){
		if(!in_array($mediaFormat, $this->mediaFormats)){
			throw new InvalidArgumentException(__('Invalid media format','post-to-google-my-business'));
		}
		if($mediaFormat === 'VIDEO'){
			throw new InvalidArgumentException(__('Creating posts with videos is currently not supported by Google','post-to-google-my-business'));
		}
		$this->jsonOutput['mediaFormat'] = $this->mediaFormat = $mediaFormat;
	}

	public function setSourceUrl($sourceUrl){
		if(esc_url_raw($sourceUrl) !== $sourceUrl){
			throw new InvalidArgumentException(__('Invalid media URL','post-to-google-my-business'));
		}
		if($this->mediaFormat === 'PHOTO'){
			$this->checkPhotoRequirements($sourceUrl);
		}elseif($this->mediaFormat === 'VIDEO'){
			$this->checkVideoRequirements($sourceUrl);
		}
		$this->jsonOutput['sourceUrl'] = $sourceUrl;
	}

	public function checkPhotoRequirements($photoUrl){
		//Todo: Test photo requirements
	}

	public function checkVideoRequirements($videoUrl){
		//Todo: Test photo requirements
	}

	public static function fromArray($array) {
		return new self($array['mediaFormat'], $array['sourceUrl']);
	}
}
