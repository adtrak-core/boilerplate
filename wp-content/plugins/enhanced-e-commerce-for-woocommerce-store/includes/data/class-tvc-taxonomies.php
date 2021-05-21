<?php

/**
 * TVC Taxonomies Class.
 *
 * @package TVC Product Feed Manager/Data/Classes
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'TVC_Taxonomies' ) ) :

    /**
     * Taxonomies Class
     */
    class TVC_Taxonomies {

        /**
         * Generates a string of shop taxonomies (like a category string) in the correct order
         *
         * @param string $product_id
         * @param string $tax
         * @param string $separator
         *
         * @return string
         */
        public static function make_shop_taxonomies_string( $product_id, $tax = 'product_cat', $separator = ' > ' ) {
            $args = array(
                'taxonomy' => $tax,
                'orderby'  => 'parent',
                'order'    => 'DESC',
            );

            // get the post term ordered with the last child cat first
            $cats = wp_get_post_terms( $product_id, $tax, $args );

            $result = array();

            if ( count( $cats ) === 0 ) {
                return '';
            }

            // anonymous function to get the correct taxonomy string
            $cat_string = function ( $id ) use ( &$result, &$cat_string, $tax ) {
                // get the first term
                $term = get_term_by( 'id', $id, $tax, 'ARRAY_A' );

                // check if the term has a parent
                if ( $term['parent'] ) {
                    // start the anonymous function again with the parent id
                    $cat_string( $term['parent'] );
                }

                // add the terms name to the result
                $result[] = $term['name'];
            };

            // activate the anonymous function with the first categories term_id
            $cat_string( $cats[0]->term_id );

            return implode( $separator, $result );
        }

        /**
         * Generates a string with all selected categories
         *
         * @param string $post_id
         * @param string $separator
         *
         * @return string
         */
        public static function get_shop_categories( $post_id, $separator = ', ' ) {
            $return_string = '';

            $args = array(
                'taxonomy' => 'product_cat',
                'orderby'  => 'term_id',
            );

            $cats = wp_get_post_terms( $post_id, 'product_cat', $args );

            foreach ( $cats as $cat ) {
                $return_string .= $cat->name . $separator;
            }

            return rtrim( $return_string, $separator );
        }

        /**
         * Returns the product category that is selected as primary (only when Yoast plugin is installed)
         *
         * @param string $id
         *
         * @return bool
         */
        public static function get_yoast_primary_cat( $id ) {
            if ( ! is_plugin_active( 'wordpress-seo/wp-seo.php' ) && ! is_plugin_active_for_network( 'wordpress-seo/wp-seo.php' )
                && ! is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) && ! is_plugin_active_for_network( 'wordpress-seo-premium/wp-seo-premium.php' ) ) {
                return false; // return false if yoast plugin is inactive
            }

            $primary_cat_id = get_post_meta( $id, '_yoast_wpseo_primary_product_cat', true );

            if ( $primary_cat_id ) {
                $product_cat[0] = get_term( $primary_cat_id, 'product_cat' );
                if ( isset( $product_cat[0]->term_id ) ) {
                    return $product_cat;
                }
            } else {
                return false;
            }

            return false;
        }

        public static function get_shop_categories_list() {
            $args = array(
                'hide_empty'   => 1,
                'taxonomy'     => 'product_cat',
                'hierarchical' => 1,
                'orderby'      => 'name',
                'order'        => 'ASC',
                'exclude'      => apply_filters( 'tvc_category_mapping_exclude', array() ),
                'exclude_tree' => apply_filters( 'tvc_category_mapping_exclude_tree', array() ),
                'number'       => absint( apply_filters( 'tvc_category_mapping_max_categories', 0 ) ),
                'child_of'     => 0,
            );

            $args = apply_filters( 'tvc_category_mapping_args', $args );

            return self::get_cat_hierarchy( 0, $args );
        }

        private static function get_cat_hierarchy( $parent, $args ) {
            $cats = get_categories( $args );
            $ret  = new stdClass;

            foreach ( $cats as $cat ) {
                if ( $cat->parent == $parent ) {
                    $id                 = $cat->cat_ID;
                    $ret->$id           = $cat;
                    $ret->$id->children = self::get_cat_hierarchy( $id, $args );
                }
            }
            return $ret;
        }
    }
    // end of TVC_Taxonomies_Class

endif;
