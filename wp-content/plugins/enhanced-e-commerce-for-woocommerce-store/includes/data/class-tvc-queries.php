<?php

/**
 * TVC Queries Class.
 *
 * @package TVC Product Feed Manager/Data/Classes
 * @version 4.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVC_Queries' ) ) :

	/**
	 * WP Queries Class
	 */
	class TVC_Queries {

		private $_wpdb;

		/**
		 * @var string placeholder containing the wp table prefix
		 */
		private $_table_prefix;

		/**
		 * TVC_Queries Constructor
		 */
		public function __construct() {
			// get global WordPress database functions
			global $wpdb;

			// assign the global wpdb to a variable
			$this->_wpdb = &$wpdb;

			// assign the wp table prefix to a variable
			$this->_table_prefix = $this->_wpdb->prefix;
		}

		public function get_feeds_list() {
			// filter out all non product feeds when no special feed add-on plugin is active
			$feed_type_filter = ! apply_filters( 'tvc_special_feeds_add_on_active', false ) ? " WHERE p.feed_type_id = '1'" : '';

			return $this->_wpdb->get_results(
				"SELECT p.product_feed_id, p.title, p.url, p.updated, p.feed_type_id, p.products, s.status AS status, s.color AS color FROM {$this->_table_prefix}feedmanager_product_feed AS p
				INNER JOIN {$this->_table_prefix}feedmanager_feed_status AS s on p.status_id = s.status_id{$feed_type_filter}"
			);
		}

		public function get_all_feed_names() {
			return $this->_wpdb->get_results( "SELECT title FROM {$this->_table_prefix}feedmanager_product_feed" );
		}

		public function get_feed_row( $feed_id ) {
			return $this->_wpdb->get_row( "SELECT * FROM {$this->_table_prefix}feedmanager_product_feed WHERE product_feed_id = {$feed_id}" );
		}

		/**
		 * Get a list of all existing countries
		 *
		 * @return array|object|null of the query
		 */
		public function read_countries() {
			return $this->_wpdb->get_results( "SELECT name_short, name FROM {$this->_table_prefix}feedmanager_country ORDER BY name", ARRAY_A );
		}

		public function get_feedmanager_channel_table() {
			return $this->_wpdb->get_results( "SELECT * FROM {$this->_table_prefix}feedmanager_channel", ARRAY_A );
		}

		public function get_feedmanager_product_feed_table() {
			return $this->_wpdb->get_results( "SELECT * FROM {$this->_table_prefix}feedmanager_product_feed", ARRAY_A );
		}

		public function get_feedmanager_product_feedmeta_table() {
			return $this->_wpdb->get_results( "SELECT * FROM {$this->_table_prefix}feedmanager_product_feedmeta", ARRAY_A );
		}

		public function get_feed_type_id( $feed_id ) {
			return $this->_wpdb->get_var( "SELECT feed_type_id FROM {$this->_table_prefix}feedmanager_product_feed WHERE product_feed_id = '{$feed_id}'" );
		}

		public function read_channels() {
			$google = array( 'channel_id' => '1', 'name' => 'Google Merchant Centre', 'short' => 'google' );
			return array( $google );
		}
        public function get_set_merchant_id(){
            return $this->_wpdb->get_var( "SELECT * FROM {$this->_table_prefix}feedmanager_default_center WHERE status = 1" );
        }

        public function get_merchant_info(){
            return $this->_wpdb->get_row( "SELECT * FROM {$this->_table_prefix}feedmanager_default_center WHERE status = 1");
        }

        public function get_set_ads_account_id($merchantId = ''){
            if($merchantId == '') {
                return $this->_wpdb->get_var( "SELECT * FROM {$this->_table_prefix}adsmanager_default_center WHERE status = 1" );
            } else {
                return $this->_wpdb->get_var( "SELECT * FROM {$this->_table_prefix}adsmanager_default_center WHERE status = 1 AND merchant_id = {$merchantId}" );
            }

        }

        public function get_merchant_center_linked_flag($adsId = '') {
            return $this->_wpdb->get_row( "SELECT * FROM {$this->_table_prefix}adsmanager_default_center WHERE status = 1 AND merchant_center_linked = 1 AND account_id = {$adsId}" );
        }

        public function get_claim(){
            return $this->_wpdb->get_var( "SELECT * FROM {$this->_table_prefix}feedmanager_default_center WHERE claim = 1" );
        }

        /**
         * @param $channel_short_name
         * @param $channel_id
         * @param $channel_name
         * @return bool|int
         */
		public function register_a_channel( $channel_short_name, $channel_id, $channel_name ) {
			return $this->_wpdb->query(
				$this->_wpdb->prepare(
					"INSERT INTO {$this->_table_prefix}feedmanager_channel (channel_id, name, short) VALUES
					( %d, '%s', '%s' )",
					$channel_id,
					$channel_name,
					$channel_short_name
				)
			);
		}

        /**
         * @param $channel_short_name
         * @return string|null
         */
		public function get_channel_id( $channel_short_name ) {
			return $this->_wpdb->get_var( "SELECT channel_id FROM {$this->_table_prefix}feedmanager_channel WHERE short = '{$channel_short_name}'" );
		}

        /**
         * @param $channel_id
         * @return bool|string|null
         */
		public function get_channel_short_name_from_db( $channel_id ) {
			if ( 'undefined' !== $channel_id ) { // make sure the selected channel is installed
				return $this->_wpdb->get_var( "SELECT short FROM {$this->_table_prefix}feedmanager_channel WHERE channel_id = {$channel_id}" );
			} else {
				return false;
			}
		}

        /**
         * @param $channel_short
         * @return false|int
         */
		public function remove_channel_from_db( $channel_short ) {
			$main_table = $this->_table_prefix . 'feedmanager_channel';

			return $this->_wpdb->delete( $main_table, array( 'short' => $channel_short ) );
		}

        /**
         * @return bool|int
         */
		public function clean_channel_table() {
			return $this->_wpdb->query(
				$this->_wpdb->prepare(
					"DELETE FROM {$this->_table_prefix}feedmanager_channel WHERE channel_id = %d OR name = %s",
					0,
					''
				)
			);
		}

		public function read_active_schedule_data() {
			return $this->_wpdb->get_results( "SELECT product_feed_id, updated, schedule FROM {$this->_table_prefix}feedmanager_product_feed WHERE status_id=1", ARRAY_A );
		}

		public function read_failed_feeds() {
			return $this->_wpdb->get_results( "SELECT product_feed_id, updated, schedule FROM {$this->_table_prefix}feedmanager_product_feed WHERE status_id=5 OR status_id=6", ARRAY_A );
		}

		public function read_sources() {
			return $this->_wpdb->get_results( "SELECT source_id, name FROM {$this->_table_prefix}feedmanager_source ORDER BY name", ARRAY_A );
		}

		public function get_feeds_from_specific_channel( $channel_id ) {
			return $this->_wpdb->get_results( "SELECT product_feed_id FROM {$this->_table_prefix}feedmanager_product_feed WHERE channel_id = {$channel_id}", ARRAY_A );
		}

        /**
         * @param $feed_id
         * @return array|object|null
         */
		public function get_meta_parents( $feed_id ) {
			return $this->_wpdb->get_results( "SELECT ID FROM {$this->_table_prefix}posts WHERE post_parent = {$feed_id}", ARRAY_A );
		}

        /**
         * @param $feed_id
         * @return array|object|null
         */
		public function read_feed( $feed_id ) {
			$result = $this->_wpdb->get_results(
				"SELECT p.product_feed_id, p.source_id AS source, p.title, p.feed_title, p.feed_description, p.main_category, "
				. "p.url, p.include_variations, p.is_aggregator, p.status_id, p.base_status_id, p.updated, p.products, p.feed_type_id, p.schedule, c.name_short "
				. "AS country, m.channel_id AS channel, p.language "
				. "FROM {$this->_table_prefix}feedmanager_product_feed AS p "
				. "INNER JOIN {$this->_table_prefix}feedmanager_country AS c ON p.country_id = c.country_id "
				. "INNER JOIN {$this->_table_prefix}feedmanager_channel AS m ON p.channel_id = m.channel_id "
				. "WHERE p.product_feed_id = {$feed_id}",
				ARRAY_A
			);

			$category_mapping = $this->read_category_mapping( $feed_id );

			if ( isset( $category_mapping[0]['meta_value'] ) && $category_mapping[0]['meta_value'] !== '' ) {
				$result[0]['category_mapping'] = $category_mapping[0]['meta_value'];
			} else {
				$result[0]['category_mapping'] = '';
			}

			return $result;
		}

        /**
         * @param $feed_id
         * @return array|object|null
         */
		public function read_category_mapping( $feed_id ) {
			return $this->_wpdb->get_results( "SELECT meta_value FROM {$this->_table_prefix}feedmanager_product_feedmeta WHERE product_feed_id = {$feed_id} AND meta_key = 'category_mapping'", ARRAY_A );
		}

        /**
         * @param $feed_id
         * @return |null
         */
		public function get_feed_status_data( $feed_id ) {
			$status = $this->_wpdb->get_results(
				"SELECT product_feed_id, channel_id, title, url, status_id, products, feed_type_id "
				. "FROM {$this->_table_prefix}feedmanager_product_feed "
				. "WHERE product_feed_id = '{$feed_id}'",
				ARRAY_A
			);

			return $status ? $status[0] : null;
		}

		/**
		 * Returns the post ids that belong to the selected categories
		 *
		 * @param   string  $category_string    A string that contains the selected categories.
		 * @param   bool    $with_variation     True if product variations should be included in the feed. Default false.
		 *
		 * @return array
		 */
		public function get_post_ids( $category_string, $with_variation = false ) {
			// If the user has not selected a category, return an empty array.
			if ( empty( $category_string ) ) {
				return array();
			}

			$start_product_id = get_transient( 'tvc_start_product_id' ) ? get_transient( 'tvc_start_product_id' ) : -1;

			// Limit the number of products per query to 1000 to prevent a result that is to large to handle by the server.
			// When the limit is reached a next batch will be requested by the fill_the_background_queue function.
			// @since 2.11.0
			$product_query_limit = apply_filters( 'tvc_product_query_limit', 1000 );

			$products_query = "SELECT DISTINCT {$this->_table_prefix}posts.ID
				FROM {$this->_table_prefix}posts
				LEFT JOIN {$this->_table_prefix}term_relationships ON ({$this->_table_prefix}posts.ID = {$this->_table_prefix}term_relationships.object_id)
				LEFT JOIN {$this->_table_prefix}term_taxonomy ON ({$this->_table_prefix}term_relationships.term_taxonomy_id = {$this->_table_prefix}term_taxonomy.term_taxonomy_id)
				WHERE {$this->_table_prefix}posts.post_type = 'product' AND {$this->_table_prefix}posts.post_status = 'publish'
				AND {$this->_table_prefix}term_taxonomy.term_id IN ($category_string)
				AND {$this->_table_prefix}posts.ID > $start_product_id
				ORDER BY ID LIMIT $product_query_limit";

			// Get all main product ids (simple and variable, but not the variations).
			$main_products_ids = $this->_wpdb->get_col( $products_query );

			set_transient( 'tvc_start_product_id', end( $main_products_ids ), TVC_TRANSIENT_LIVE );

			// if variations should not be included return the main product ids
			if ( ! $with_variation || empty( $main_products_ids ) ) {
				return $main_products_ids;
			}

			// Put the main ids in a string so it can be attached to a query string.
			$main_products_ids_string = implode( ', ', $main_products_ids );

			$variation_products_query = "SELECT DISTINCT post_parent FROM {$this->_table_prefix}posts
				WHERE {$this->_table_prefix}posts.post_parent IN ( {$main_products_ids_string} )
				AND {$this->_table_prefix}posts.post_type = 'product_variation'
				AND {$this->_table_prefix}posts.post_status = 'publish'
				ORDER BY ID";

			// Get the ids of the variable products.
			$variation_products = $this->_wpdb->get_col( $variation_products_query );

			// If there are no variations, return the main product ids.
			if ( count( $variation_products ) < 1 ) {
				return $main_products_ids;
			}

			$variation_products_ids_string = implode( ', ', $variation_products );

			// Remove the main product ids of products that have a valid variable version from the list to keep only the ids of the simple products.
			$simple_products_ids = array_diff( $main_products_ids, $variation_products );

			$product_variations_query = "SELECT DISTINCT ID FROM {$this->_table_prefix}posts
				WHERE {$this->_table_prefix}posts.post_parent IN ( {$variation_products_ids_string} )
				AND {$this->_table_prefix}posts.post_type = 'product_variation'
				AND {$this->_table_prefix}posts.post_status = 'publish'
				ORDER BY ID";

			// Now get the variations.
			$product_variations_ids = $this->_wpdb->get_col( $product_variations_query );

			// Combine the variable product ids with the remaining simple product ids.
			$all_product_ids = array_merge( $simple_products_ids, $product_variations_ids );
			asort( $all_product_ids );

			return $all_product_ids;
		}

		/**
		 * Gets the required data from the main
		 *
		 * @param string $post_id
		 * @param string $column_string
		 *
		 * @return array|object|null
		 */
		public function read_post_data( $post_id, $column_string ) {
			$selecting_columns = $column_string ? ', ' . $column_string : '';

			$result = $this->_wpdb->get_results(
				"SELECT DISTINCT ID $selecting_columns
				FROM {$this->_table_prefix}posts
				WHERE ID = $post_id"
			);

			return $result ? $result[0] : null;
		}

		/**
		 * returns the lowest product id belonging to one or more categories
		 *
		 * @param string $category_string
		 *
		 * @return string
		 */
		public function get_lowest_product_id( $category_string ) {
			$main_table               = $this->_table_prefix . 'posts';
			$term_relationships_table = $this->_table_prefix . 'term_relationships';
			$term_taxonomy_table      = $this->_table_prefix . 'term_taxonomy';

			$result = $this->_wpdb->get_results(
				"SELECT MIN(ID) FROM $main_table
				LEFT JOIN $term_relationships_table ON ($main_table.ID = $term_relationships_table.object_id)
				LEFT JOIN $term_taxonomy_table ON ($term_relationships_table.term_taxonomy_id = $term_taxonomy_table.term_taxonomy_id)
				WHERE $main_table.post_type = 'product' AND $main_table.post_status = 'publish'
				AND $term_taxonomy_table.term_id IN ($category_string)",
				ARRAY_A
			);

			return $result ? $result[0]['MIN(ID)'] : 0;
		}


		/**
		 * returns the highest product id belonging to one or more categories
		 *
		 * @param string $category_string
		 *
		 * @return string
		 */
		public function get_highest_product_id( $category_string ) {
			$main_table               = $this->_table_prefix . 'posts';
			$term_relationships_table = $this->_table_prefix . 'term_relationships';
			$term_taxonomy_table      = $this->_table_prefix . 'term_taxonomy';

			$result = $this->_wpdb->get_results(
				"SELECT MAX(ID) FROM $main_table
				LEFT JOIN $term_relationships_table ON ($main_table.ID = $term_relationships_table.object_id)
				LEFT JOIN $term_taxonomy_table ON ($term_relationships_table.term_taxonomy_id = $term_taxonomy_table.term_taxonomy_id)
				WHERE $main_table.post_type = 'product' AND $main_table.post_status = 'publish'
				AND $term_taxonomy_table.term_id IN ($category_string)",
				ARRAY_A
			);

			return $result ? $result[0]['MAX(ID)'] : 0;
		}

        /**
         * @param $product_id
         * @param $parent_product_id
         * @param $record_ids
         * @param $meta_columns
         * @return array
         */
		public function read_meta_data( $product_id, $parent_product_id, $record_ids, $meta_columns ) {
			$data = array();

			foreach ( $meta_columns as $column ) {
				$taxonomy = get_taxonomy( $column );

				if ( $taxonomy ) {

					$taxonomy_value = TVC_Taxonomies::make_shop_taxonomies_string( $product_id, $taxonomy->name, ', ' );

					if ( ! $taxonomy_value ) {
						$taxonomy_value = TVC_Taxonomies::make_shop_taxonomies_string( $parent_product_id, $taxonomy->name, ', ' );
					}

					if ( $taxonomy_value ) {
						array_push( $data, $this->make_meta_object( $column, $taxonomy_value, $product_id ) );
					}
				}

				foreach ( $record_ids as $rec_id ) {
					$value = get_post_meta( $rec_id, $column, true );

					if ( $value || '0' === $value ) {
						array_push( $data, $this->make_meta_object( $column, $value, $rec_id ) );
						break;
					} else {
						$alt_val  = maybe_unserialize( get_post_meta( $rec_id, '_product_attributes', true ) );
						$col_name = str_replace( ' ', '-', strtolower( $column ) );
						if ( $alt_val && isset( $alt_val[ $col_name ] ) ) {
							array_push( $data, $this->make_meta_object( $column, $alt_val[ $col_name ]['value'], $rec_id ) );
						} elseif ( $alt_val && is_array( $alt_val ) ) {
							foreach ( $alt_val as $v ) {
								if ( $v['name'] === $column ) {
									array_push( $data, $this->make_meta_object( $column, $v['value'], $rec_id ) );
								}
							}
						}
					}
				}
			}

			$this->polish_data( $data, $product_id );

			return $data;
		}

        /**
         * @param $key
         * @param $value
         * @param $id
         * @return stdClass
         */
		private function make_meta_object( $key, $value, $id ) {
			$obj             = new stdClass();
			$obj->meta_key   = $key;
			$obj->meta_value = $value;
			$obj->post_id    = $id;

			return $obj;
		}

        /**
         * @param $data
         * @param $main_post_id
         */
		private function polish_data( &$data, $main_post_id ) {
			$site_url = get_option( 'siteurl' );

			foreach ( $data as $row ) {
				// make sure the _wp_attached_file data contains a valid url
				if ( '_wp_attached_file' === $row->meta_key ) {
					$row->meta_value = $this->get_post_thumbnail_url( $main_post_id, 'large' );

					// if the _wp_attached_file data is not a valid url than add the url data
					if ( ! filter_var( $row->meta_value, FILTER_VALIDATE_URL ) ) {
						$row->meta_value = $site_url . '/wp-content/uploads/' . $row->meta_value;
					}
				}

				// convert the time stamp format to a usable date time format for the feed
				if ( '_sale_price_dates_from' === $row->meta_key || '_sale_price_dates_to' === $row->meta_key ) {
					$row->meta_value = tvc_convert_price_date_to_feed_format( $row->meta_value );
				}
			}
		}

        /**
         * @param $feed_id
         * @return false|int
         */
		public function delete_feed( $feed_id ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->delete( $main_table, array( 'product_feed_id' => $feed_id ) );
		}

        /**
         * @param $feed_id
         * @return false|int
         */
		public function delete_meta( $feed_id ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feedmeta';

			return $this->_wpdb->delete( $main_table, array( 'product_feed_id' => $feed_id ) );
		}

		/**
		 * Gets the meta data from a specific feed. It does not include the category mapping and feed filter data.
		 *
		 * @param   string  $feed_id
		 *
		 * @return  array|bool|object|null
		 */
		public function read_metadata( $feed_id ) {
			if ( $feed_id ) {
				$main_table = $this->_table_prefix . 'feedmanager_product_feedmeta';

				return $this->_wpdb->get_results( "SELECT * FROM $main_table WHERE product_feed_id = $feed_id AND meta_key != 'category_mapping' AND meta_key != 'product_filter_query' ORDER BY meta_id", ARRAY_A );
			} else {
				return false;
			}
		}

		/**
		 * Fetches the Feed Filter data from a specific feed.
		 *
		 * @param   string  $feed_id
		 *
		 * @return  array|bool|object|null
		 */
		public function get_product_filter_query( $feed_id ) {
			if ( $feed_id ) {
				$main_table = $this->_table_prefix . 'feedmanager_product_feedmeta';

				return $this->_wpdb->get_results( "SELECT meta_value FROM $main_table WHERE product_feed_id = $feed_id AND meta_key = 'product_filter_query'", ARRAY_A );
			} else {
				tvc_write_log_file( sprintf( 'Function get_filter_query returned false on feed %s', $feed_id ) );

				return false;
			}
		}

		public function get_columns_from_post_table() {
			return $this->_wpdb->get_results( "SHOW COLUMNS FROM {$this->_table_prefix}posts" );
		}

		public function get_custom_product_attributes() {
			$main_table = $this->_table_prefix . 'woocommerce_attribute_taxonomies';

			return $this->_wpdb->get_results( "SELECT attribute_name, attribute_label FROM $main_table" );
		}

		public function get_custom_product_fields() {
			$keywords_array = explode( ',', get_option( 'tvc_third_party_attribute_keywords', '%wpmr%,%cpf%,%unit%,%bto%,%yoast%' ) );
			$main_table     = $this->_wpdb->postmeta;

			$query_string = "SELECT DISTINCT meta_key FROM $main_table WHERE meta_key NOT BETWEEN '_' AND '_z'";

			foreach ( $keywords_array as $keyword ) {
				$query_string .= " OR meta_key LIKE '" . trim( $keyword ) . "'";
			}

			$query_string .= ' ORDER BY meta_key';

			return $this->_wpdb->get_col( $query_string );
		}

		public function clear_feed_batch_options() {
			$this->_wpdb->query( "DELETE FROM {$this->_wpdb->options} WHERE option_name LIKE '%_batch_%'" );
		}

		/**
		 * @since 2.0.11
		 */
		public function clear_feed_batch_sitemeta() {
			$this->_wpdb->query( "DELETE FROM {$this->_wpdb->sitemeta} WHERE meta_key LIKE '%_batch_%'" );
		}

		public function get_own_variable_product_attributes( $variable_id ) {
			$keywords        = get_option( 'tvc_third_party_attribute_keywords', '%wpmr%,%cpf%,%unit%,%bto%,%yoast%' );
			$wpmr_attributes = array();

			if ( $keywords ) {
				$keywords_array     = explode( ',', $keywords );
				$main_table         = $this->_wpdb->postmeta;
				$query_where_string = count( $keywords_array ) > 0 ? "WHERE (meta_key LIKE '" . trim( $keywords_array[0] ) . "'" : '';

				for ( $i = 1; $i < count( $keywords_array ); $i ++ ) {
					$query_where_string .= " OR meta_key LIKE '" . trim( $keywords_array[ $i ] ) . "'";
				}

				$query_where_string .= count( $keywords_array ) > 0 ? ') AND ' : '';

				foreach ( $this->_wpdb->get_results( "SELECT meta_key, meta_value FROM $main_table $query_where_string (post_id = $variable_id)" ) as $key => $row ) {
					$wpmr_attributes[ $row->meta_key ] = $row->meta_value;
				}
			}

			return $wpmr_attributes;
		}

		public function get_all_product_attributes() {
			$main_table = $this->_wpdb->postmeta;

			return $this->_wpdb->get_results( "SELECT DISTINCT meta_value FROM $main_table WHERE meta_key = '_product_attributes'" );
		}

		public function get_current_feed_status( $feed_id ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->get_results( "SELECT status_id FROM $main_table WHERE product_feed_id = $feed_id" );
		}

		public function get_country_id( $country_code ) {
			$main_table = $this->_table_prefix . 'feedmanager_country';

			return $this->_wpdb->get_row( "SELECT country_id FROM $main_table WHERE name_short = '$country_code'" );
		}

		public function get_feed_ids_with_specific_status( $status_id ) {
			return $this->_wpdb->get_results( "SELECT product_feed_id FROM {$this->_table_prefix}feedmanager_product_feed WHERE status_id = '$status_id'" );
		}

		public function switch_feed_status( $feed_id, $new_status ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->update(
				$main_table,
				array(
					'status_id'      => $new_status,
					'base_status_id' => $new_status,
				),
				array( 'product_feed_id' => $feed_id )
			);
		}

		public function set_nr_feed_products( $feed_id, $nr_products ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->update( $main_table, array( 'products' => $nr_products ), array( 'product_feed_id' => $feed_id ) );
		}

		public function get_nr_feed_products( $feed_id ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->get_row( "SELECT products FROM $main_table WHERE product_feed_id = '$feed_id'" );
		}

		/**
		 * Updates a new feed in the product_feed table.
		 *
		 * @param (string) $feed_id
		 * @param (array) $feed_data
		 * @param (array) $data_types
		 *
		 * @return (int|false) nr of affected rows
		 * @since 1.0.0
		 *
		 */
		public function update_feed( $feed_id, $feed_data, $data_types ) {

			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->update(
				$main_table,
				$feed_data,
				array(
					'product_feed_id' => $feed_id,
				),
				$data_types,
				array(
					'%d',
				)
			);
		}

        /**
         * Update Feed update Meta Data
         * @param $feed_id
         * @param $feed_url
         * @param $nr_products
         * @return false|int
         */
		public function update_feed_update_data( $feed_id, $feed_url, $nr_products ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->update(
				$main_table,
				array(
					'updated'  => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
					'url'      => $feed_url,
					'products' => $nr_products,
				),
				array( 'product_feed_id' => $feed_id ),
				array( '%s', '%s', '%s' ),
				array( '%d' )
			);
		}

        /**
         * @param $feed_id
         * @return mixed
         */
		public function get_file_url_from_feed( $feed_id ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';
			$result     = $this->_wpdb->get_row( "SELECT url FROM $main_table WHERE product_feed_id = $feed_id", ARRAY_A );

			return $result['url'];
		}

		/**
		 * Sets the status id of a feed
		 *
		 * @param   string  $feed_id
		 * @param   int     $status
		 *
		 * @return bool
		 */
		public function update_feed_file_status( $feed_id, $status ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			return $this->_wpdb->update( $main_table, array( 'status_id' => $status ), array( 'product_feed_id' => $feed_id ), array( '%d' ), array( '%d' ) );
		}

        /**
         * @param $feed_id
         * @param $meta_data
         * @return false|int
         */
		public function update_meta_data( $feed_id, $meta_data ) {
			// TODO: Onderstaande proces is nog niet echt efficient. In plaats van het standaard verwijderen
			// van elke bestaande regel met het geselecteerde id nummer, zou ik eerst moeten kijken of
			// deze regel wel is gewijzigd. Indien een regel niet is gewijzigd zou ik hem ook niet moeten
			// verwijderen.

			$main_table = $this->_table_prefix . 'feedmanager_product_feedmeta';

			// first remove all meta data belonging to this feed except the product_filter_query
			//$this->_wpdb->delete( $main_data, array( 'product_feed_id' => $feed_id ) ); // could not use it because it can't work with !=
			$this->_wpdb->query( "DELETE FROM $main_table WHERE product_feed_id = $feed_id AND meta_key != 'product_filter_query'" );

			$counter = 0;

			for ( $i = 0; $i < count( $meta_data ); $i ++ ) {
				if ( ! empty( $meta_data[ $i ]->value ) && '{}' !== $meta_data[ $i ]->value ) {
					$result = $this->_wpdb->insert(
						$main_table,
						array(
							'product_feed_id' => $feed_id,
							'meta_key'        => $meta_data[ $i ]->key,
							'meta_value'      => $meta_data[ $i ]->value,
						),
						array(
							'%d',
							'%s',
							'%s',
						)
					);

					$counter += $result;
				}
			}

			return $counter;
		}

		/**
		 * Resets the status_id's of failed feeds.
		 *
		 * @since 2.7.0
		 */
		public function reset_all_feed_status() {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';
			$failed_ids = $this->_wpdb->get_results( "SELECT product_feed_id FROM $main_table WHERE status_id > '2' OR status_id = '0'", 'ARRAY_A' );

			foreach ( $failed_ids as $feed_id ) {
				$id          = $feed_id['product_feed_id'];
				$base_status = $this->_wpdb->get_var( "SELECT base_status_id FROM $main_table WHERE product_feed_id = '$id'" );
				$new_status  = '1' === $base_status || '2' === $base_status ? $base_status : '2';

				$this->_wpdb->update(
					$main_table,
					array(
						'status_id' => $new_status,
						'products'  => 0,
					),
					array(
						'product_feed_id' => $id,
					),
					array(
						'%s',
						'%d',
					)
				);
			}
		}

		public function store_feed_filter( $feed_id, $filter ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feedmeta';

			if ( $filter ) {
				$exists = $this->_wpdb->get_results( "SELECT meta_id FROM $main_table WHERE product_feed_id = $feed_id AND meta_key = 'product_filter_query'" );

				if ( $exists ) {
					$this->_wpdb->update(
						$main_table,
						array(
							'meta_value' => $filter,
						),
						array(
							'product_feed_id' => $feed_id,
							'meta_key'        => 'product_filter_query',
						),
						array(
							'%s',
						),
						array(
							'%d',
							'%s',
						)
					);
				} else {
					$this->_wpdb->insert(
						$main_table,
						array(
							'product_feed_id' => $feed_id,
							'meta_key'        => 'product_filter_query',
							'meta_value'      => $filter,
						),
						array(
							'%d',
							'%s',
							'%s',
						)
					);
				}
			} else {
				$this->_wpdb->query( "DELETE FROM $main_table WHERE product_feed_id = $feed_id AND meta_key = 'product_filter_query'" );
			}
		}

		public function insert_meta_data( $feed_id, $meta_data, $feed_filter_data, $category_mapping ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feedmeta';

			$counter = 0;

			for ( $i = 0; $i < count( $meta_data ); $i ++ ) {
				$result = $this->_wpdb->insert(
					$main_table,
					array(
						'product_feed_id' => $feed_id,
						'meta_key'        => $meta_data[ $i ]['meta_key'],
						'meta_value'      => $meta_data[ $i ]['meta_value'],
					),
					array(
						'%d',
						'%s',
						'%s',
					)
				);

				$counter += $result;
			}

			for ( $i = 0; $i < count( $feed_filter_data ); $i++ ) {
				$this->store_feed_filter( $feed_id, $feed_filter_data[ $i ]['meta_value'] );
			}

			$counter += $this->_wpdb->insert(
				$main_table,
				array(
					'product_feed_id' => $feed_id,
					'meta_key'        => 'category_mapping',
					'meta_value'      => $category_mapping[0]['meta_value'],
				),
				array(
					'%d',
					'%s',
					'%s',
				)
			);

			return $counter;
		}

		public function title_exists( $feed_title ) {
			$main_table = $this->_table_prefix . 'feedmanager_product_feed';
			$count      = $this->_wpdb->get_var( "SELECT COUNT(*) FROM $main_table WHERE title = '$feed_title'" );

			return $count > 0 ? true : false;
		}

		/**
		 * Inserts a new feed in the product_feed table and returns its new id.
		 *
		 * @param array $feed_data_to_store
		 * @param array $feed_types
		 *
		 * @return integer containing the id of the new feed
		 * @since 1.0.0
		 *
		 */
		public function create_feed( $feed_data_to_store, $feed_types ) {

			$main_table = $this->_table_prefix . 'feedmanager_product_feed';

			$this->_wpdb->insert(
				$main_table,
				$feed_data_to_store,
				$feed_types
			);

			return $this->_wpdb->insert_id;
		}

		public function getCountryID($feed_id){
            $countryID =  $this->_wpdb->get_results("SELECT country_id FROM {$this->_table_prefix}feedmanager_product_feed WHERE product_feed_id = ".$feed_id);

            return $countryID;
        }

        public function getScheduleTime($feed_id){
            $scheduleTime =  $this->_wpdb->get_results("SELECT schedule FROM {$this->_table_prefix}feedmanager_product_feed WHERE product_feed_id = ".$feed_id);

            return $scheduleTime;
        }

        public function getFeedData($feed_id){
            $feedData =  $this->_wpdb->get_results("SELECT title,feed_description,feed_title FROM {$this->_table_prefix}feedmanager_product_feed WHERE product_feed_id = ".$feed_id);

            return $feedData;
        }

        public function getCountryCode($countryID){
            $countryCode = $this->_wpdb->get_results("SELECT name_short FROM {$this->_table_prefix}feedmanager_country WHERE country_id =".$countryID[0]->country_id);
            return $countryCode;
        }

        public function insertGmcFeedId($feedID,$feed_id){
            $main_table = $this->_table_prefix . 'feedmanager_product_feed';
            return $this->_wpdb->update( $main_table, array( 'gmc_feed_id' => $feedID ), array( 'product_feed_id' => $feed_id ) );
		}

		public function getGmcFeedId($feed_id){
            $gmcFeedId = $this->_wpdb->get_results("SELECT gmc_feed_id FROM {$this->_table_prefix}feedmanager_product_feed WHERE product_feed_id =". $feed_id);
            return $gmcFeedId;
        }

        public function getFeedURL($feed_id){
            $main_table = $this->_table_prefix . 'feedmanager_product_feed';
            return $this->_wpdb->get_results("SELECT url,feed_title,status_id FROM $main_table WHERE product_feed_id =". $feed_id);
        }

		public function getTableColumns($table) {
            return $this->_wpdb->get_results("SELECT column_name as field FROM information_schema.columns WHERE table_name = '$table'");
        }

        public function getTableData($table = 'wp_postmeta', $columns = array()) {
            return $this->_wpdb->get_results("SELECT  DISTINCT " . implode(',', $columns) . " as field FROM $table");
        }

        public function getTablePostMeta($post_id) {
        	global $wpdb;
    		$tve_table_prefix = $wpdb->prefix;
            return $this->_wpdb->get_results("SELECT  meta_key,meta_value FROM ".$tve_table_prefix."postmeta where post_id=" . $post_id);
        }
	}
	// End of TVC_Queries class

endif;
