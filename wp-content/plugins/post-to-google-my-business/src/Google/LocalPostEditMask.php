<?php


namespace PGMB\Google;


class LocalPostEditMask {
	private $oldPostFlat, $newPostFlat = [];
	private $mask;

	public function __construct(LocalPost $oldPost, LocalPost $newPost) {
		$this->walk($oldPost->getArray(), $this->oldPostFlat);
		$this->walk($newPost->getArray(), $this->newPostFlat);
		$this->mask = implode(',', array_keys(array_diff_assoc($this->newPostFlat, $this->oldPostFlat)));
	}

	private function walk($array, &$output, $parent = ''){
		foreach($array as $key => $value){
			if(is_array($value)){
				$this->walk($value, $output, "{$key}.");
				continue;
			}
			$output[$parent.$key] = $value;
		}
	}

	public function getMask(){
		return $this->mask;
	}
}
