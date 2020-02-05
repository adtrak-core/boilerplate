<?php


namespace PGMB\PostTypes;

use InvalidArgumentException;

abstract class AbstractPostType implements PostTypeItem {
	protected $parent_id = 0;
	protected $editing_id = 0;

	public function set_parent($parent_id){
		if(!is_numeric($parent_id)){ throw new InvalidArgumentException("Parent post ID is not numeric"); }
		$this->parent_id = intval($parent_id);
	}

	public function set_editing($post_id){
		if(!is_numeric($post_id)){ throw new InvalidArgumentException("Editing Post ID is not numeric"); }
		$this->editing_id = intval($post_id);
	}

	public function get_post_data() {
		return [
			'ID' => $this->editing_id,
			'post_parent' => $this->parent_id,
		];
	}


}
