<?php

if (!function_exists('dd')) {
    /**
     * Dies and dumps.
     * @return string
     */
    function dd()
    {
        call_user_func_array('dump', func_get_args());
        die;
    }
}

if (!function_exists('content_directory')) {
    /**
     * Gets the content directory.
     * @return string
     */
    function content_directory()
    {
        return WP_CONTENT_DIR;
    }
}

if (!function_exists('plugin_directory')) {
    /**
     * Gets the plugin directory.
     * @return string
     */
    function plugin_directory()
    {
        return WP_PLUGIN_DIR;
    }
}


if (!function_exists('plugin_url')) {
    /**
     * Gets the plugin url.
     * @return string
     */
    function plugin_url()
    {
        return WP_PLUGIN_URL;
    }
}

if (!function_exists('errors')) {
    /**
     * Get the errors.
     * @param string key
     * @return array
     */
    function errors($key = null)
    {
        $errors = isset($errors[0]) ? $errors[0] : $errors;

        if (!$key) return $errors;

        return array_get($errors, $key);
    }
}
