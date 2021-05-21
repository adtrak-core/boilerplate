<?php
/**
 * TVC Product Feed File Class.
 *
 * @package TVC Product Feed Manager/Data/Classes
 * @version 1.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TVC_File' ) ) :

	/**
	 * The File Class
	 */
	class TVC_File {

		private $_queries;

		public function __construct() {
			$this->_queries = new TVC_Queries();
		}

		/**
		 * Reads the correct categories from a channel specific taxonomy text file
		 *
		 * @param int $channel_id
		 * @param string $search_level
		 * @param string $parent_category
		 * @param string $language_code
		 *
		 * @return array|string containing the categories
		 */
		public function get_categories_for_list( $channel_id, $search_level, $parent_category, $language_code ) {
			$channel_class = new TVC_Channel();

			$last_cat     = '';
			$categories   = array();
			$channel_name = $channel_class->get_channel_short_name( $channel_id );

			$path = TVC_CHANNEL_DATA_DIR . "/$channel_name/taxonomy.$language_code.txt";

			if ( file_exists( $path ) ) {
				$file = fopen( $path, 'r' )
				or die( esc_html__( 'Unable to open the file containing the categories', 'tvc-product-feed-manager' ) );

				// step over the first lines that do not contain categories
				while ( strpos( fgets( $file ), '#' ) ) {
					continue;
				}

				// step through all the lines in the file
				while ( ! feof( $file ) ) {
					// get the line
					$line = trim( fgets( $file ) );

					// split the line into pieces using the > separator
					$category_line_array = explode( '>', $line );

					if ( 0 === $search_level ) {
						if ( trim( $category_line_array [ $search_level ] ) !== $last_cat ) {
							array_push( $categories, trim( $category_line_array [ $search_level ] ) );
							$last_cat = trim( $category_line_array [ $search_level ] );
						}
					} elseif ( count( $category_line_array ) > $search_level && $search_level > 0 && trim( $category_line_array [ $search_level - 1 ] ) === trim( $parent_category ) ) {
						if ( trim( $category_line_array [ $search_level ] ) !== $last_cat ) {
							array_push( $categories, trim( $category_line_array [ $search_level ] ) );
							$last_cat = trim( $category_line_array [ $search_level ] );
						}
					}
				}

				// don't forget to free the resources
				fclose( $file );
			}

			return $categories;
		}

		public function get_output_fields_for_specific_channel( $channel ) {
			$fields = array();

			$path = TVC_CHANNEL_DATA_DIR . "/$channel/$channel.txt";

			if ( file_exists( $path ) ) {
				$file = fopen( $path, 'r' )
				or die( esc_html__( 'Unable to open the file containing the categories', 'tvc-product-feed-manager' ) );

				// step through all the lines in the file
				while ( ! feof( $file ) ) {
					$field_object = new stdClass();

					// get the line
					$line = fgetcsv( $file, 0, "\t" );

					if ( is_array( $line ) && ! empty( $line[0] ) ) {
						$field_object->field_id    = $line[0];
						$field_object->category_id = $line[1];
						$field_object->field_label = $line[2];

						array_push( $fields, $field_object );
					}
				}
			}

			return $fields;
		}

		/**
		 * Check the standard backup folder and return the .sql file names in it
		 *
		 * @return array
		 */
		public function make_list_of_active_backups() {
			$backups = array();

			$path = TVC_BACKUP_DIR;

			foreach ( glob( $path . '/*.sql' ) as $file ) {
				$feed = fopen( $file, 'r' );
				$line = fgets( $feed );
				fclose( $feed );

				$file_name   = str_replace( TVC_BACKUP_DIR . '/', '', $file );
				$date_string = strtok( $line, '#' );
				$file_date   = strlen( $date_string ) < 15 ? date( 'Y-m-d H:i:s', $date_string ) : 'unknown';

				array_push( $backups, $file_name . '&&' . $file_date );
			}

			return $backups;
		}

		public function write_full_backup_file( $backup_file, $backup_string ) {
			if ( is_writable( TVC_BACKUP_DIR ) ) {
				$feed = fopen( $backup_file, 'w' );
			} else {
				/* translators: %s: Folder that holds the backup files */
				return sprintf( esc_html__( '1432 - %s is not a writable folder. Make sure you have admin rights to this folder.', 'tvc-product-feed-manager' ), TVC_BACKUP_DIR );
			}

			if ( false !== $feed ) {
				fwrite( $feed, $backup_string );
				fclose( $feed );

				return true;
			} else {
				/* translators: %s: Selected backup file */
				return sprintf( esc_html__( '1433 - Could not write the %s file.', 'tvc-product-feed-manager' ), $backup_file );
			}
		}

		public function get_installed_channels_from_file() {
			$active_channels = array();

			if ( file_exists( TVC_CHANNEL_DATA_DIR ) ) {
				$dir_iterator = new RecursiveDirectoryIterator( TVC_CHANNEL_DATA_DIR );
				$iterator     = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::SELF_FIRST );

				foreach ( $iterator as $folder ) {
					if ( $folder->isDir() && $folder->getFilename() !== '.' & $folder->getFilename() !== '..' ) {
						array_push( $active_channels, $folder->getBaseName() );
					}
				}
			}

			return $active_channels;
		}

		/**
		 * Takes the installed channel .zip file and unzips it in the channels folder
		 *
		 * @param string $channel_name
		 *
		 * @return bool false if installing the channel failed
		 */
		public function unzip_channel_file( $channel_name ) {
			if ( ! file_exists( TVC_CHANNEL_DATA_DIR ) ) {
				TVC_Folders::make_channels_support_folder();
			}

			WP_Filesystem();

			$zip_file         = TVC_CHANNEL_DATA_DIR . '/' . $channel_name . '.zip';
			$destination_path = TVC_CHANNEL_DATA_DIR . '/';

			if ( ! file_exists( $zip_file ) ) {
				tvc_write_log_file( sprintf( 'Failed installing the Channel %s. Could not download the .zip file from the server.', $channel_name ) );

				return false;
			}

			$unzip_result = unzip_file( $zip_file, $destination_path );

			if ( is_wp_error( $unzip_result ) ) {
				tvc_handle_wp_errors_response( $unzip_result, sprintf( 'The installation of channel %s failed. Unable to unpack the channel file in folder %s.', $channel_name, TVC_CHANNEL_DATA_DIR ) );
			}

			unlink( $zip_file ); // clean up the zip file

			return true;
		}

        /**
         * @param $channel_short_name
         */
		public function delete_channel_source_files( $channel_short_name ) {
			$channel_folder = TVC_CHANNEL_DATA_DIR . '/' . $channel_short_name;

			if ( file_exists( $channel_folder ) && is_dir( $channel_folder ) ) {
				// remove the channel definition files
				TVC_Folders::delete_folder( $channel_folder );
			}

			if ( 'google' === $channel_short_name ) {
				$free_version_google_folder = ENHANCAD_PLUGIN_DIR . 'includes/application/google';

				if ( file_exists( $free_version_google_folder ) && is_dir( $free_version_google_folder ) ) {
					TVC_Folders::delete_folder( $free_version_google_folder );
				}
			}
		}

        /**
         * @param $channel_id
         */
		public function delete_channel_feed_files( $channel_id ) {
			$feeds = $this->_queries->get_feeds_from_specific_channel( $channel_id );

			foreach ( $feeds as $feed_id ) {
				$file_url  = $this->_queries->get_file_url_from_feed( $feed_id['product_feed_id'] );
				$file_name = basename( $file_url );
				$file_path = TVC_FEEDS_DIR . '/' . $file_name;

				if ( file_exists( $file_path ) ) {
					unlink( $file_path );
				}
			}
		}
	}

	// end of TVC_File_Class

endif;
