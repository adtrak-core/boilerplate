<?php


namespace PGMB\Google;


class PublishedLocalPost extends LocalPost {


	protected function setGoogleReadOnlyArgs($name, $createTime, $updateTime, $state, $searchUrl){
		$args = [
			'name' => $name,
			'createTime'    => $createTime,
			'updateTime'    => $updateTime,
			'state'         => $state,
			'searchUrl'     => $searchUrl,
		];
		$this->jsonOutput = array_merge($this->jsonOutput, $args);
	}

	public static function fromArray($googleArray){
		$googleArray = (array)$googleArray; //Todo: make sure its an array
		$instance = parent::fromArray($googleArray);
		$instance->setGoogleReadOnlyArgs(
			$googleArray['name'],
			$googleArray['createTime'],
			$googleArray['updateTime'],
			$googleArray['state'],
			$googleArray['searchUrl']
		);
		return $instance;
	}
}
