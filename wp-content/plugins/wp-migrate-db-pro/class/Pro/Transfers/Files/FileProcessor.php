<?php

namespace DeliciousBrains\WPMDB\Pro\Transfers\Files;

use DeliciousBrains\WPMDB\Common\Filesystem\Filesystem;
use DeliciousBrains\WPMDB\Common\Http\Http;

/**
 * Class FileProcessor
 *
 * @package WPMDB\Transfers\Files
 */
class FileProcessor
{

    public $filesystem;
    /**
     * @var Http
     */
    private $http;

    /**
     * FileProcessor constructor.
     *
     * @param Filesystem $filesystem
     * @param Http       $http
     */
    public function __construct(
        Filesystem $filesystem,
        Http $http
    ) {
        $this->filesystem = $filesystem;
        $this->http       = $http;
    }

    /**
     * Given an array of directory paths, loops over each dir and returns an array of files and metadata
     *
     * @param $directories
     * @param $abs_path
     *
     * @return array
     */
    public function get_local_files($directories, $abs_path = '', $excludes = array(), $stage = '', $date = null, $timezone = null)
    {
        $count      = 0;
        $total_size = 0;
        $files      = [];
        $manifest   = [];

        foreach ($directories as $directory) {
            $file_size          = 0;
            $is_single          = false;
            $files_in_directory = [];

            if (!$this->filesystem->file_exists($directory)) {
                continue;
            }

            if (!$this->filesystem->is_dir($directory)) {
                $is_single = true;
            }

            $nice_name = $this->get_item_nice_name($stage, $directory, $is_single);

            if ($is_single) {
                $basename                      = wp_basename($directory);
                $file_info                     = $this->filesystem->get_file_info(wp_basename($directory), $abs_path);
                $files_in_directory[$basename] = $file_info;
            } else {
                $files_in_directory = $this->get_files_by_path($directory);
            }

            if (is_wp_error($files_in_directory)) {
                return $files_in_directory;
            }

            foreach ($files_in_directory as $key => $file) {
                $not_excluded = $this->check_file_against_excludes($file, $excludes);
                $date_compare = true;

                if ($not_excluded) {
                    $date_compare = $this->check_file_against_date($file, $date, $timezone);
                }

                if (is_wp_error($date_compare)) {
                    return $date_compare;
                }

                if (
                    $not_excluded === false ||
                    $date_compare === false
                ) {
                    unset($files_in_directory[$key]);
                    continue;
                }

                //Check for manifest files, don't want those suckers
                if (preg_match("/(([a-z0-9]+-){5})manifest/", $key)) {
                    unset($files_in_directory[$key]);
                    continue;
                }

                $file_size  += $file['size'];
                $total_size += $file['size'];
                $manifest[] = $file['subpath'];
            }

            $filtered_files = $this->filter_folder_data($files_in_directory, $file_size, $directory, $nice_name);

            if (!empty($filtered_files)) {
                $files[$directory] = $filtered_files;
            }

            $count += \count($files_in_directory);
        }

        $return = [
            'meta'  => [
                'count'    => $count,
                'size'     => $total_size,
                'manifest' => $manifest,
            ],
            'files' => $files,
        ];

        return $return;
    }

    /**
     * @param string $stage
     * @param array  $directory
     * @param bool   $is_single
     *
     * @return string
     */
    public function get_item_nice_name($stage, $directory, $is_single = false)
    {
        $directory_info = 'themes' === $stage ? wp_get_themes() : get_plugins();
        $exploded       = explode(DIRECTORY_SEPARATOR, $directory);
        $directory_key  = $exploded[count($exploded) - 1];
        $nice_name      = '';

        if ('media_files' === $stage) {
            return $directory_key;
        }

        if ('themes' === $stage) {
            if (isset($directory_info[$directory_key])) {
                $nice_name = html_entity_decode($directory_info[$directory_key]->Name);
            }
        } else {
            foreach ($directory_info as $key => $info) {
                $pattern = '/^' . $directory_key;

                if (!$is_single) {
                    $pattern .= '(\/|\\\)'; // Account for Windows slashes
                }

                $pattern .= '/';

                if (1 === preg_match($pattern, $key)) {
                    $nice_name = html_entity_decode($info['Name']);
                    break;
                }
            }
        }

        return $nice_name;
    }

    /**
     * @param string $abs_path
     * @param string $filename
     * @param int    $size
     * @param array  $files
     * @param int    $count
     *
     * @return array
     */
    public function handle_single_file($abs_path, $filename, $size, $files, $count, $nice_name, $fix_path = false)
    {
        $file = wp_basename($filename);

        $file_info = $this->filesystem->get_file_info($file, $abs_path);

        $size += $file_info['size'];

        $filtered_files                       = $this->filter_folder_data([$file_info], $size, $filename, $nice_name);
        $files[$filename][$file_info['name']] = $filtered_files[0];
        ++$count;

        return array($size, $filtered_files, $files, $count);
    }

    /**
     * @param array $files_in_directory
     * @param int   $size
     *
     * @return array
     */
    public function filter_folder_data($files_in_directory, $size, $folder_path, $nice_name)
    {
        $filtered_files = [];

        foreach ($files_in_directory as $key => $files) {
            $filtered_files[$key]                    = $files;
            $filtered_files[$key]['folder_size']     = $size;
            $filtered_files[$key]['folder_abs_path'] = $folder_path;
            $filtered_files[$key]['nice_name']       = $nice_name;
        }

        return $filtered_files;
    }

    /**
     * @param string $directory
     *
     * @return array|bool
     */
    public function get_files_by_path($directory)
    {
        // @TODO potentially filter this list
        $files = $this->filesystem->scandir_recursive($directory);

        if (is_wp_error($files)) {
            return $this->http->end_ajax($files);
        }

        return $files;
    }

    /**
     * @param array $file
     * @param array $excludes
     *
     * @return bool
     */
    public function check_file_against_excludes($file, $excludes)
    {
        if (empty($excludes)) {
            return true;
        }

        $testMatch = Excludes::shouldExcludeFile($file['absolute_path'], $excludes);

        if (!empty($testMatch['exclude'])) {
            return false;
        }

        return true;
    }


    /**
     * Compare file modified date against a date and timezone
     *
     * Debug: $date = $date->format('Y-m-d H:i:sP');
     *
     * @param $file
     * @param $date
     * @param $clientTimezone
     *
     * @return bool
     */
    public function check_file_against_date($file, $date, $clientTimezone)
    {
        if (is_null($date)) {
            return true;
        }

        $serverdate     = new \DateTime();
        $serverTimeZone = $serverdate->getTimezone();

        $date = new \DateTime($date, new \DateTimeZone($clientTimezone));// Create client date object with associated timezone so we can compare against filemtime() which uses the server timezone

        $date->setTimezone(new \DateTimeZone($serverTimeZone->getName()));
        $abs_path = $file['absolute_path'];

        if (!file_exists($abs_path)) {
            return $this->http->end_ajax(new \WP_Error('wpmdb-file-does-not-exist', sprintf(__('File %s does not exist', 'wp-migrate-db'), $abs_path)));
        }

        $timestamp = $date->getTimestamp();
        $fileMTime = filemtime($abs_path);

        if ($fileMTime <= $timestamp) {
            return false;
        }

        return true;
    }
}
