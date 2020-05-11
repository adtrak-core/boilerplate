<?php


namespace PGMB\Taxonomy;


class ListedTaxonomyField extends TaxonomyField {

	public function add_column($columns){
		$columns[$this->field_id] = $this->field_label;
		return $columns;
	}

	public function add_column_content($content, $column_name, $term_id){
		if($column_name !== $this->field_id){ return $content; }
		$term_id = absint($term_id);
		$value = get_term_meta($term_id, $this->field_id, true);


		switch($this->field_type){
			case "singlecheck":
				if($value){
					$content .= "<span class=\"dashicons dashicons-yes\"></span>";
				}else{
					$content .= "<span class=\"dashicons dashicons-no\"></span>";
				}
				break;
		}

		return $content;
	}

	public function sortable_column($sortable){
		$sortable[$this->field_id] = $this->field_id;
		return $sortable;
	}

	public static function init( $taxonomy, $field_id, $field_type, $field_label, $field_description, $field_options = [] ) {
		$instance = parent::init( $taxonomy, $field_id, $field_type, $field_label, $field_description, $field_options );

		add_filter("manage_edit-{$taxonomy}_columns", [$instance, 'add_column']);
		add_filter("manage_{$taxonomy}_custom_column", [$instance, 'add_column_content'], 10, 3);
		//add_filter("manage_edit-{$taxonomy}_sortable_columns", [$instance, 'sortable_column']);
		//Todo: fix sorting
		return $instance;
	}

}
