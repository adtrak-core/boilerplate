<?php

/**
 * TVC Category Selector Element Class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Tatvic_Category_Selector_Element' ) ) :

	class Tatvic_Category_Selector_Element {

		/**
		 * Returns the code for a single row meant for the category mapping table.
		 *
		 * @param object    $category           object containing data of the active category like term_id and name
		 * @param string    $category_children  a string with the children of the active category
		 * @param string    $level_indicator    current active level
		 * @param string    $mode               defines if the category mapping row should contain a description (normal) or a catgory mapping (mapping) column
		 *
		 * @return string
		 */
		
		
		public static function category_mapping_row( $category, $category_children, $level_indicator, $mode, $ee_prod_mapped_cats) {
			
			$category_row_class = 'mapping' === $mode ? 'tvc-category-mapping-selector' : 'tvc-category-selector';
			$mode_column  = 'mapping' === $mode ? self::category_mapping_selector( 'catmap', $category->term_id, true, $ee_prod_mapped_cats ) : self::category_description_data_item( $category->term_id );
			//print_r($ee_prod_mapped_cats);
			
			// return '<tr id="category-' . $category->term_id . '"><th class="check-column" scope="row" id="shop-category-selector">
			// 	<input class="' . $category_row_class . '" data-children="' . $category_children . '" id="feed-selector-' . $category->term_id . '"
			// 	type="checkbox" value="' . $category->term_id . '" title="Select ' . $category->name . '">
			// 	</th><td id="shop-category" class="col30w">' .
			// 	$level_indicator . $category->name .
			// 	'</td><td class="field-header col55w"><div id="feed-category-' . $category->term_id . '"></div>' .
			// 	$mode_column . '</td>
			// 	<td class="category-count col10w">' . $category->category_count . '</td></tr>';
			return '<div class="row">
	                <div class="col-6 align-self-center">
	                    <div class="form-group" id="shop-category">
	                        <label class="form-label-control">' .$level_indicator . $category->name .'</label>
	                    </div>
	                </div>
	                <div class="col-6 align-self-center">
	                    <div class="form-group">
	                    	<div id="feed-category-' . $category->term_id . '"></div>' .$mode_column . '
						</div>
	                </div>
	            </div>';
		}

		/**
		 * Returns the code for a category input selector.
		 *
		 * @param string    $identifier     identifier for the selector
		 * @param string    $id             id of the selector
		 * @param boolean   $start_visible  should this selector start visible
		 *
		 * @return string
		 */
		public static function category_mapping_selector( $identifier, $id, $start_visible, $ee_prod_mapped_cats ) {
			$display         = $start_visible ? 'initial' : 'none';
			$ident           = '-1' !== $id ? $identifier . '-' . $id : $identifier;
			$category_levels = apply_filters( 'tvc_category_selector_level', 6 );
			if(isset($ee_prod_mapped_cats[$id]['id']) && isset($ee_prod_mapped_cats[$id]['name']) && $ee_prod_mapped_cats[$id]['id'] && $ee_prod_mapped_cats[$id]['name']){

				$cat_id = $ee_prod_mapped_cats[$id]['id'];
				$cat_name = $ee_prod_mapped_cats[$id]['name'];
				$html_code  = '<div id="category-selector-' . $ident . '" style="display:' . $display . '">
					<div id="selected-categories">
					<input type="hidden" name="category-'.$id.'" id="category-'.$id.'" value="'.$cat_id.'">
					<input type="hidden" name="category-name-'.$id.'" id="category-name-'.$id.'" value="'.$cat_name.'">
					</div>
					<label id="label-'.$ident.'_0">'.$cat_name.'</label><span class="change_prodct_feed_cat" data-cat-id="'.$id.'" data-id="'.$ident.'_0">Edit</span>
					<select class="form-control" style="display:none;" id="' . $ident . '_0" catId="'.$id.'" onchange="selectSubCategory(this)"></select>';

				for ( $i = 1; $i < $category_levels; $i ++ ) {
					$html_code .= '<select class="" id="' . $ident . '_' . $i . '" value="0" catId="'.$id.'" style="display:none;" onchange="selectSubCategory(this)"></select>';
				}
			}else{
				$html_code  = '<div id="category-selector-' . $ident . '" style="display:' . $display . '">
					<div id="selected-categories">
					<input type="hidden" name="category-'.$id.'" id="category-'.$id.'" value="">
					<input type="hidden" name="category-name-'.$id.'" id="category-name-'.$id.'" value="">
					</div>
					<select class="form-control" id="' . $ident . '_0" catId="'.$id.'" onchange="selectSubCategory(this)"></select>';

				for ( $i = 1; $i < $category_levels; $i ++ ) {
					$html_code .= '<select class="" id="' . $ident . '_' . $i . '" value="0" catId="'.$id.'" style="display:none;" onchange="selectSubCategory(this)"></select>';
				}

			}
			/*if (!class_exists('ShoppingApi')) {
	            require_once(__DIR__ . '/ShoppingApi.php');
	        }*/

	        

			$html_code .= '</div>';

			return $html_code;
		}

		/**
		 * Returns the code for the category description column.
		 *
		 * @param string    $category_id
		 *
		 * @return string
		 */
		private static function category_description_data_item( $category_id ) {
			$category_description = '' !== category_description( $category_id ) ? category_description( $category_id ) : '—';

			$html_code = '<span aria-hidden="true">' . $category_description . '</span>';

			return $html_code;
		}
	}

	// end of TVC_Category_Selector_Element class

endif;
