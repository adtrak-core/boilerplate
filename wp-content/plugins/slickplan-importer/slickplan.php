<?php
/*
Plugin Name: Slickplan Importer
Plugin URI: http://wordpress.org/extend/plugins/slickplan-importer/
Description: Quickly import your <a href="http://slickplan.com" target="_blank">Slickplan</a> project into your WordPress site. To use go to the <a href="import.php">Tools -> Import</a> screen and select Slickplan.
Author: Slickplan.com <info@slickplan.com>
Author URI: http://slickplan.com/
Version: 2.1.3
License: GPL-3.0 - http://www.gnu.org/licenses/gpl-3.0.html
*/

function_exists('ob_start') and ob_start();
function_exists('set_time_limit') and set_time_limit(600);

require_once ABSPATH . 'wp-admin/includes/import.php';
require_once ABSPATH . 'wp-admin/includes/translation-install.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

if (!class_exists('WP_Importer')) {
    $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
    if (is_file($class_wp_importer)) {
        require_once $class_wp_importer;
    }
}

if (class_exists('WP_Importer') and !class_exists('Slickplan_Importer')) {

    /**
     * Slickplan's plugin directory path.
     *
     * @var string
     */
    define('SLICKPLAN_PLUGIN_PATH', plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR);

    /**
     * Slickplan's plugin directory URL.
     *
     * @var string
     */
    define('SLICKPLAN_PLUGIN_URL', plugin_dir_url(__FILE__));

    /**
     * Slickplan's plugin importer ID.
     *
     * @var string
     */
    define('SLICKPLAN_PLUGIN_ID', 'slickplan');

    /**
     * Slickplan's plugin importer database option key.
     *
     * @var string
     */
    define('SLICKPLAN_PLUGIN_OPTION', 'slickplan_importer');

    /**
     * A string to prepend to indented pages.
     *
     * @var string
     */
    define('SLICKPLAN_PLUGIN_INDENT', '&#8212;');

    /**
     * Class Slickplan_Importer
     */
    class Slickplan_Importer extends WP_Importer
    {

        /**
         * Menu order increment.
         *
         * @var int
         */
        private $_order = 0;

        /**
         * An array of import options.
         *
         * @var array
         */
        private $_options = array();

        /**
         * An array of imported pages or errors.
         *
         * @var array
         */
        private $_summary = array();

        /**
         * An array of imported files
         *
         * @var array
         */
        private $_files = array();

        /**
         * If page has unparsed internal pages
         *
         * @var bool
         */
        private $_has_unparsed_internal_links = false;

        /**
         * Importer page routing.
         */
        public function dispatch()
        {
            // Check if WP_LOAD_IMPORTERS is present
            if (!defined('WP_LOAD_IMPORTERS')) {
                return;
            }

            $step = isset($_GET['step']) ? $_GET['step'] : null;
            $xml = get_option(SLICKPLAN_PLUGIN_OPTION, array());

            $this->_displayHeader();

            if (
                ($step === 'map' and isset($xml['pages']))
                or ($step === 'upload' and isset($_FILES) and !empty($_FILES))
            ) {
                if ($step === 'map' and isset($xml['pages'])) {
                    $result = $this->_displayImportOptions();
                } else {
                    check_admin_referer('import-upload');
                    $result = $this->handleFileUpload();
                }
                if (is_wp_error($result)) {
                    $this->_displayError($result->get_error_message());
                }
            } elseif ($step === 'import' and isset($xml['pages'], $xml['import_options'])) {
                $this->_displayAjaxImporter();
            } elseif ($step === 'done' and isset($xml['summary'])) {
                $this->_displaySummary();
            } else {
                $this->_displayUploadForm();
            }

            $this->_displayFooter();
        }

        /**
         * Handle file upload and prepare to import pages.
         */
        public function handleFileUpload()
        {
            $file = wp_import_handle_upload();
            if (isset($file['error'])) {
                return $this->_displayError($file['error']);
            }

            if (isset($file['file'], $file['id']) and is_file($file['file'])) {
                $xml_content = file_get_contents($file['file']);
                wp_import_cleanup($file['id']);

                $result = $this->_parseSlickplanXml($xml_content);
                if (is_wp_error($result)) {
                    $this->_displayError($result->get_error_message());
                } else {
                    update_option(SLICKPLAN_PLUGIN_OPTION, $result);
                    wp_redirect($this->_getAdminUrl('map'));
                    exit;
                }
            }
        }

        /**
         * Get HTML of a summary row
         *
         * @param array $page
         * @param null $id
         * @return string
         */
        public function getSummaryRow(array $page)
        {
            $html = '<div style="margin: 10px 0;">Importing „<b>' . $page['post_title'] . '</b>”&hellip;<br />';
            if (isset($page['error']) and $page['error']) {
                $html .= '<span style="color: #e00"><i class="fa fa-fw fa-times"></i> ' . $page['error'] . '</span>';
            } elseif (isset($page['url'])) {
                if (!isset($page['url_href']) or !$page['url_href']) {
                    $page['url_href'] = $page['url'];
                }
                $html .= '<i class="fa fa-fw fa-check" style="color: #0d0"></i> '
                    . '<a href="' . esc_url($page['url_href']) . '">' . $page['url'] . '</a>';
            } elseif (isset($page['loading']) and $page['loading']) {
                $html .= '<i class="fa fa-fw fa-refresh fa-spin"></i>';
            }
            if (isset($page['files']) and is_array($page['files']) and count($page['files'])) {
                $files = array();
                foreach ($page['files'] as $file) {
                    if (isset($file['url']) and $file['url']) {
                        $files[] = '<i class="fa fa-fw fa-check" style="color: #0d0"></i> <a href="'
                            . $file['url'] . '" target="_blank">' . $file['filename'] . '</a>';
                    } elseif (isset($file['error']) and $file['error']) {
                        $files[] = '<span style="color: #e00"><i class="fa fa-fw fa-times"></i> '
                            . $file['filename'] . ' - ' . $file['error'] . '</span>';
                    }
                }
                $html .= '<div style="border-left: 5px solid rgba(0, 0, 0, 0.05); margin-left: 5px; '
                    . 'padding: 5px 0 5px 11px;">Files:<br />' . implode('<br />', $files) . '</div>';
            }
            $html .= '<div>';
            return $html;
        }

        /**
         * AJAX import action
         *
         * @param array $form
         * @return array
         */
        public function ajaxImport(array $form)
        {
            $result = array();
            $xml = get_option(SLICKPLAN_PLUGIN_OPTION, array());
            if (isset($xml['import_options'])) {
                $this->_options = $xml['import_options'];
                if (isset($xml['pages'][$form['page']]) and is_array($xml['pages'][$form['page']])) {
                    $mlid = (isset($form['mlid']) and $form['mlid'])
                        ? $form['mlid']
                        : 0;
                    $page = $this->_importPage($xml['pages'][$form['page']], $mlid);
                    if (isset($page['ID']) and $page['ID']) {
                        $page['files'] = $this->_files;
                        $result = array(
                            'mlid' => $page['ID'],
                            'html' => $this->getSummaryRow($page),
                        );
                    } else {
                        $result = $page;
                    }
                }
                if (isset($form['last']) and $form['last']) {
                    $result['last'] = $form['last'];
                    $this->_checkForInternalLinks();
                    update_option(SLICKPLAN_PLUGIN_OPTION, '');
                }
                else {
                    $xml['import_options'] = $this->_options;
                    update_option(SLICKPLAN_PLUGIN_OPTION, $xml);
                }
            }
            return $result;
        }

        /**
         * Get admin URL.
         *
         * @param string $step
         * @return string
         */
        private function _getAdminUrl($step = null)
        {
            $url = 'admin.php?import=' . SLICKPLAN_PLUGIN_ID;
            if ($step) {
                $url .= '&step=' . $step;
            }
            return get_admin_url(null, $url);
        }

        /**
         * Display importer options and page mapping.
         */
        private function _displayImportOptions()
        {
            $xml = get_option(SLICKPLAN_PLUGIN_OPTION, array());
            if (!$xml or !$this->_isCorrectSlickplanXmlFile($xml, true)) {
                return new WP_Error(SLICKPLAN_PLUGIN_ID, 'Invalid file content.');
            }
            if (isset($_POST['slickplan_importer']) and is_array($_POST['slickplan_importer'])) {
                $form = $_POST['slickplan_importer'];
                if (
                    isset($form['settings_language'], $xml['settings']['language'])
                    and $form['settings_language']
                ) {
                    $this->_changeLanguage($xml['settings']['language']);
                }
                if (isset($form['settings_title']) and $form['settings_title']) {
                    $title = (isset($xml['settings']['title']) and $xml['settings']['title'])
                        ? $xml['settings']['title']
                        : $xml['title'];
                    update_option('blogname', $title);
                }
                if (isset($form['settings_tagline']) and $form['settings_tagline']) {
                    update_option('blogdescription', $xml['settings']['tagline']);
                }
                $this->_options = array(
                    'titles' => isset($form['titles_change']) ? $form['titles_change'] : '',
                    'content' => isset($form['content']) ? $form['content'] : '',
                    'content_files' => (
                        isset($form['content'], $form['content_files'])
                        and $form['content'] === 'contents'
                        and $form['content_files']
                    ),
                    'create_menu' => (isset($form['create_menu']) and $form['create_menu']),
                    'users' => isset($form['users_map']) ? $form['users_map'] : array(),
                    'internal_links' => array(),
                    'imported_pages' => array(),
                );
                if ($this->_options['create_menu']) {
                    $menu_name = $menu_name_orig = (isset($form['menu_name']) and $form['menu_name'])
                        ? $form['menu_name']
                        : 'Slickplan';
                    $i = 0;
                    while (get_term_by('name', $menu_name, 'nav_menu')) {
                        $menu_name = $menu_name_orig . ' ' . (++$i);
                    }
                    $this->_options['create_menu'] = wp_create_nav_menu($menu_name);
                } else {
                    $this->_options['create_menu'] = false;
                }
                if ($this->_options['content_files']) {
                    // Redirect to AJAX importer
                    $xml['import_options'] = $this->_options;
                    update_option(SLICKPLAN_PLUGIN_OPTION, $xml);
                    wp_redirect($this->_getAdminUrl('import'));
                } else {
                    // There is no files, so we can import pages faster than using AJAX importer
                    foreach (array('home', '1', 'util', 'foot') as $type) {
                        if (isset($xml['sitemap'][$type]) and is_array($xml['sitemap'][$type])) {
                            $this->_importPages($xml['sitemap'][$type]);
                        }
                    }

                    $this->_checkForInternalLinks();

                    update_option(SLICKPLAN_PLUGIN_OPTION, array(
                        'summary' => implode($this->_summary),
                    ));
                    do_action('import_done', SLICKPLAN_PLUGIN_OPTION);
                    wp_redirect($this->_getAdminUrl('done'));
                }
                exit;
            }

            $no_of_files = 0;
            $filesize_total = array();
            if (isset($xml['pages']) and is_array($xml['pages'])) {
                foreach ($xml['pages'] as $page) {
                    if (isset($page['contents']['body']) and is_array($page['contents']['body'])) {
                        foreach ($page['contents']['body'] as $body) {
                            if (isset($body['content']['type']) and $body['content']['type'] === 'library') {
                                ++$no_of_files;
                            }
                            if (isset($body['content']['file_size'], $body['content']['file_id']) and $body['content']['file_size']) {
                                $filesize_total[$body['content']['file_id']] = (int)$body['content']['file_size'];
                            }
                        }
                    }
                }
            }
            $filesize_total = (int)array_sum($filesize_total);

            require_once SLICKPLAN_PLUGIN_PATH . 'views/options.php';
        }

        /**
         * Display AJAX import page.
         */
        private function _displayAjaxImporter()
        {
            $xml = get_option(SLICKPLAN_PLUGIN_OPTION, array());
            require_once SLICKPLAN_PLUGIN_PATH . 'views/import.php';
        }

        /**
         * Import pages into WordPress.
         *
         * @param array $pages
         * @param int $parent_id
         */
        private function _importPages(array $pages, $parent_id = 0)
        {
            $xml = get_option(SLICKPLAN_PLUGIN_OPTION, array());
            foreach ($pages as $page) {
                if (isset($xml['pages'][$page['id']])) {
                    $page += $xml['pages'][$page['id']];
                    $this->_importPage($page, $parent_id);
                }
            }
        }

        /**
         * Import single page into WordPress.
         *
         * @param array $data
         * @param int $parent_id
         * @return array
         */
        private function _importPage(array $data, $parent_id = 0)
        {
            $this->_order += 10;

            $page = array(
                'post_status' => 'publish',
                'post_content' => '',
                'post_type' => 'page',
                'post_title' => $this->_getFormattedTitle($data),
                'menu_order' => $this->_order,
                'post_parent' => (int)$parent_id,
            );

            // Set post content
            if ($this->_options['content'] === 'desc') {
                if (isset($data['desc']) and !empty($data['desc'])) {
                    $page['post_content'] = $data['desc'];
                }
            } elseif ($this->_options['content'] === 'contents') {
                if (
                    isset($data['contents']['body'])
                    and is_array($data['contents']['body'])
                    and count($data['contents']['body'])
                ) {
                    $page['post_content'] = $this->_getFormattedContent($data['contents']['body']);
                }
            }

            // Set url slug
            if (isset($data['contents']['url_slug']) and $data['contents']['url_slug']) {
                $page['post_name'] = $data['contents']['url_slug'];
            }

            // Set post author
            if (isset(
                $data['contents']['assignee']['@value'],
                $this->_options['users'][$data['contents']['assignee']['@value']]
            )) {
                $page['post_author'] = $this->_options['users'][$data['contents']['assignee']['@value']];
            }

            // Set post status
            if (isset($data['contents']['status']) and $data['contents']['status'] === 'draft') {
                $page['post_status'] = $data['contents']['status'];
            }

            // Check if page has internal links, we need to replace them later
            $this->_has_unparsed_internal_links = false;
            if ($page['post_content']) {
                $updated_content = $this->_parseInternalLinks($page['post_content']);
                if ($updated_content) {
                    $page['post_content'] = $updated_content;
                }
            }

            $page_id = wp_insert_post($page);
            if (is_wp_error($page_id)) {
                $page['ID'] = false;
                $page['error'] = $page_id->get_error_message();
                $this->_summary[] = $this->getSummaryRow($page);
            } else {
                $page['ID'] = (int)$page_id;

                // Add page to nav menu
                if ($this->_options['create_menu']) {
                    $menu_parent = 0;
                    if ($page['post_parent']) {
                        $menu_items = (array)wp_get_nav_menu_items($this->_options['create_menu'], array(
                            'post_status' => 'publish,draft',
                        ));
                        foreach ($menu_items as $menu_item) {
                            if ($page['post_parent'] === intval($menu_item->object_id)) {
                                $menu_parent = (int)$menu_item->ID;
                                break;
                            }
                        }
                    }
                    wp_update_nav_menu_item($this->_options['create_menu'], 0, array(
                        'menu-item-title' => $page['post_title'],
                        'menu-item-object' => 'page',
                        'menu-item-object-id' => $page['ID'],
                        'menu-item-type' => 'post_type',
                        'menu-item-status' => 'publish',
                        'menu-item-parent-id' => $menu_parent,
                    ));
                }

                // Set the SEO meta values
                if (
                    isset($data['contents']['meta_title'])
                    or isset($data['contents']['meta_description'])
                    or isset($data['contents']['meta_focus_keyword'])
                ) {
                    // SEO by Yoast integration
                    if (class_exists('WPSEO_Meta') and method_exists('WPSEO_Meta', 'set_value')) {
                        if (isset($data['contents']['meta_title']) and $data['contents']['meta_title']) {
                            WPSEO_Meta::set_value('title', $data['contents']['meta_title'], $page['ID']);
                        }
                        if (isset($data['contents']['meta_description']) and $data['contents']['meta_description']) {
                            WPSEO_Meta::set_value('metadesc', $data['contents']['meta_description'], $page['ID']);
                        }
                        if (isset($data['contents']['meta_focus_keyword']) and $data['contents']['meta_focus_keyword']) {
                            WPSEO_Meta::set_value('focuskw', $data['contents']['meta_focus_keyword'], $page['ID']);
                        }
                    }
                    // All In One SEO Pack integration
                    if (defined('AIOSEOP_VERSION')) {
                        if (isset($data['contents']['meta_title']) and $data['contents']['meta_title']) {
                            delete_post_meta($page['ID'], '_aioseop_title');
                            add_post_meta($page['ID'], '_aioseop_title', $data['contents']['meta_title']);
                        }
                        if (isset($data['contents']['meta_description']) and $data['contents']['meta_description']) {
                            delete_post_meta($page['ID'], '_aioseop_description');
                            add_post_meta($page['ID'], '_aioseop_description', $data['contents']['meta_description']);
                        }
                        if (isset($data['contents']['meta_focus_keyword']) and $data['contents']['meta_focus_keyword']) {
                            delete_post_meta($page['ID'], '_aioseop_keywords');
                            add_post_meta($page['ID'], '_aioseop_keywords', $data['contents']['meta_focus_keyword']);
                        }
                    }
                }
                $page['url'] = get_permalink($page['ID']);

                // Save page permalink
                if (isset($data['id'])) {
                    $this->_options['imported_pages'][$data['id']] = $page['url'];
                }

                // Check if page has unparsed internal links, we need to replace them later
                if ($this->_has_unparsed_internal_links) {
                    $this->_options['internal_links'][] = $page['ID'];
                }

                $page['url'] = '/' . ltrim(str_replace(home_url('/'), '', $page['url']), '/');
                $page['url_href'] = get_admin_url(null, 'post.php?post=' . $page['ID'] . '&action=edit');
                $this->_summary[] = $this->getSummaryRow($page);
                if (isset($data['childs']) and is_array($data['childs'])) {
                    $this->_importPages($data['childs'], $page_id);
                }
            }
            return $page;
        }

        /**
         * Replace internal links with correct pages URLs.
         *
         * @param $content
         * @param $force_parse
         * @return bool
         */
        private function _parseInternalLinks($content, $force_parse = false)
        {
            preg_match_all('/href="slickplan:([a-z0-9]+)"/isU', $content, $internal_links);
            if (isset($internal_links[1]) and is_array($internal_links[1]) and count($internal_links[1])) {
                $internal_links = array_unique($internal_links[1]);
                $links_replace = array();
                foreach ($internal_links as $cell_id) {
                    if (
                        isset($this->_options['imported_pages'][$cell_id])
                        and $this->_options['imported_pages'][$cell_id]
                    ) {
                        $links_replace['="slickplan:' . $cell_id . '"'] = '="'
                            . esc_attr($this->_options['imported_pages'][$cell_id]) . '"';
                    } elseif ($force_parse) {
                        $links_replace['="slickplan:' . $cell_id . '"'] = '="#"';
                    } else {
                        $this->_has_unparsed_internal_links = true;
                    }
                }
                if (count($links_replace)) {
                    return strtr($content, $links_replace);
                }
            }
            return false;
        }

        /**
         * Check if there are any pages with unparsed internal links, if yes - replace links with real URLs
         */
        private function _checkForInternalLinks()
        {
            if (isset($this->_options['internal_links']) and is_array($this->_options['internal_links'])) {
                foreach ($this->_options['internal_links'] as $page_id) {
                    $page = get_post($page_id);
                    if (isset($page->post_content)) {
                        $page_content = $this->_parseInternalLinks($page->post_content, true);
                        if ($page_content) {
                            wp_update_post(array(
                                'ID' => $page_id,
                                'post_content' => $page_content,
                            ));
                        }
                    }
                }
            }
        }

        /**
         * Get formatted HTML content.
         *
         * @param array $content
         * @return string
         */
        private function _getFormattedContent(array $contents)
        {
            $post_content = array();
            foreach ($contents as $element) {
                if (!isset($element['content']) or !isset($element['type'])) {
                    continue;
                }
                $html = '';
                switch ($element['type']) {
                    case 'wysiwyg':
                        $html .= $element['content'];
                        break;
                    case 'text':
                        $html .= htmlspecialchars($element['content']);
                        break;
                    case 'image':
                        if (isset($element['content']['type'], $element['content']['url'])) {
                            $attrs = array(
                                'alt' => isset($element['content']['alt'])
                                    ? $element['content']['alt']
                                    : '',
                                'title' => isset($element['content']['title'])
                                    ? $element['content']['title']
                                    : '',
                                'file_name' => isset($element['content']['file_name'])
                                    ? $element['content']['file_name']
                                    : '',
                            );
                            if ($element['content']['type'] === 'library') {
                                $src = $this->_addMedia($element['content']['url'], true, $attrs);
                            } else {
                                $src = $element['content']['url'];
                            }
                            if ($src and !is_wp_error($src)) {
                                $html .= '<img src="' . esc_url($src) . '" alt="' . esc_attr($attrs['alt'])
                                    . '" title="' . esc_attr($attrs['title']) . '" />';
                            }
                        }
                        break;
                    case 'video':
                    case 'file':
                        if (isset($element['content']['type'], $element['content']['url'])) {
                            $attrs = array(
                                'description' => isset($element['content']['description'])
                                    ? $element['content']['description']
                                    : '',
                                'file_name' => isset($element['content']['file_name'])
                                    ? $element['content']['file_name']
                                    : '',
                            );
                            if ($element['content']['type'] === 'library') {
                                $src = $this->_addMedia($element['content']['url'], true, $attrs);
                                $name = basename($src);
                            } else {
                                $src = $element['content']['url'];
                                $name = $src;
                            }
                            if ($src and !is_wp_error($src)) {
                                $name = $attrs['description']
                                    ? $attrs['description']
                                    : ($attrs['file_name'] ? $attrs['file_name'] : $name);
                                $html .= '<a href="' . esc_url($src) . '" title="'
                                    . esc_attr($attrs['description']) . '">' . $name . '</a>';
                            }
                        }
                        break;
                    case 'table':
                        if (isset($element['content']['data'])) {
                            if (!is_array($element['content']['data'])) {
                                $element['content']['data'] = @json_decode($element['content']['data'], true);
                            }
                            if (is_array($element['content']['data'])) {
                                $html .= '<table>';
                                foreach ($element['content']['data'] as $row) {
                                    $html .= '<tr>';
                                    foreach ($row as $cell) {
                                        $html .= '<td>' . $cell . '</td>';
                                    }
                                    $html .= '</tr>';
                                }
                                $html .= '<table>';
                            }
                        }
                        break;
                }
                if ($html) {
                    $prepend = '';
                    $append = '';
                    if (isset($element['options']['tag']) and $element['options']['tag']) {
                        $element['options']['tag'] = preg_replace('/[^a-z]+/', '',
                            strtolower($element['options']['tag']));
                        if ($element['options']['tag']) {
                            $prepend = '<' . $element['options']['tag'];
                            if (isset($element['options']['tag_id']) and $element['options']['tag_id']) {
                                $prepend .= ' id="' . esc_attr($element['options']['tag_id']) . '"';
                            }
                            if (isset($element['options']['tag_class']) and $element['options']['tag_class']) {
                                $prepend .= ' class="' . esc_attr($element['options']['tag_class']) . '"';
                            }
                            $prepend .= '>';
                        }
                    }
                    if (isset($element['options']['tag']) and $element['options']['tag']) {
                        $append = '</' . $element['options']['tag'] . '>';
                    }
                    $post_content[] = $prepend . $html . $append;
                }
            }
            return implode("\n\n", $post_content);
        }

        /**
         * Reformat title.
         *
         * @param array $data
         * @return string
         */
        private function _getFormattedTitle(array $data)
        {
            $title = (isset($data['contents']['page_title']) and $data['contents']['page_title'])
                ? $data['contents']['page_title']
                : (isset($data['text']) ? $data['text'] : '');
            if ($this->_options['titles'] === 'ucfirst') {
                if (function_exists('mb_strtolower')) {
                    $title = mb_strtolower($title);
                    $title = mb_strtoupper(mb_substr($title, 0, 1)) . mb_substr($title, 1);
                } else {
                    $title = ucfirst(strtolower($title));
                }
            } elseif ($this->_options['titles'] === 'ucwords') {
                if (function_exists('mb_convert_case')) {
                    $title = mb_convert_case($title, MB_CASE_TITLE);
                } else {
                    $title = ucwords(strtolower($title));
                }
            }
            return $title;
        }

        /**
         * Add a file to Media Library from URL
         *
         * @param $url
         * @param bool $return_url
         * @return bool|int|mixed|object|string
         */
        private function _addMedia($url, $return_url = true, array $attrs = array())
        {
            if (!$this->_options['content_files']) {
                return false;
            }

            $tmp = download_url($url);
            $file_array = array(
                'name' => isset($attrs['file_name']) ? $attrs['file_name'] : basename($url),
                'tmp_name' => $tmp,
            );
            $file_array['filename'] = $file_array['name'];

            // Check for download errors
            if (is_wp_error($tmp)) {
                @unlink($file_array['tmp_name']);
                $file_array['error'] = $tmp->get_error_message();
                $this->_files[] = $file_array;
                return $tmp;
            }

            $options = array();
            if (isset($attrs['title']) and $attrs['title']) {
                $options['post_title'] = $attrs['title'];
            }
            if (isset($attrs['alt']) and $attrs['alt']) {
                $options['post_content'] = $attrs['alt'];
            }
            if (isset($attrs['file_name']) and $attrs['file_name']) {
                $options['post_content'] = $attrs['file_name'];
            }

            $id = media_handle_sideload(
                $file_array,
                0,
                isset($attrs['description']) ? $attrs['description'] : '',
                $options
            );

            // Check for handle sideload errors.
            if (is_wp_error($id)) {
                @unlink($file_array['tmp_name']);
                $file_array['error'] = $id->get_error_message();
                $this->_files[] = $file_array;
                return $id;
            }

            $file_array['url'] = wp_get_attachment_url($id);;
            $this->_files[] = $file_array;

            if ($return_url) {
                return $file_array['url'];
            }
            return $id;
        }

        /**
         * Display importer errors.
         *
         * @param array|string $errors
         */
        private function _displayError($errors)
        {
            if (is_array($errors)) {
                $errors = implode("\n\n", $errors);
            }
            $errors = trim($errors);
            if ($errors) {
                echo '<div class="error">', wpautop($errors), '</div>';
            }
            $this->_displayUploadForm(!!$errors);
        }

        /**
         * Display importer HTML form.
         *
         * @param bool $hide_info
         */
        private function _displayUploadForm($hide_info = false)
        {
            echo '<div class="narrow">';
            if (!$hide_info) {
                echo '<div class="updated" style="border-color: #FFBA00">',
                    '<p>The Slickplan Importer plugin allows you to quickly import your ',
                    '<a href="http://slickplan.com" target="_blank">Slickplan</a> projects into your WordPress site.</p>',
                    '<p>Upon import, your pages, navigation structure, and content will be instantly ready in your CMS.</p>',
                    '</div>';
                echo '<div class="updated" style="border-color: #FFBA00">',
                    '<p>Pick a XML file to upload and click Import.</p>',
                    '</div>';
            }
            ob_start();
            wp_import_upload_form($this->_getAdminUrl('upload'));
            $form_html = ob_get_clean();
            $form_html = preg_replace('/<label for="upload">.+<\/label>/sU', '', $form_html);
            $form_html = str_replace('<input type="file"', '<br><br><input type="file"', $form_html);
            echo $form_html;
            echo '</div>';
        }

        /**
         * Display import summary.
         */
        private function _displaySummary()
        {
            $xml = get_option(SLICKPLAN_PLUGIN_OPTION, array());
            if (isset($xml['summary']) and $xml['summary']) {
                require_once SLICKPLAN_PLUGIN_PATH . 'views/import.php';
            }
            update_option(SLICKPLAN_PLUGIN_OPTION, '');
        }

        /**
         * Display importer page header HTML.
         *
         * @param string $step
         */
        private function _displayHeader($step = null)
        {
            echo '<div class="wrap">',
            '<h2>Slickplan Importer';
            if ($step === 'map') {
                echo ' (Step 2)';
            }
            echo '</h2>';

            if (!class_exists('DomDocument') or version_compare(PHP_VERSION, '5.0.0', '<')) {
                $this->_displayError('Sorry! This importer requires PHP5 and DomDocument extensions.');
            }
        }

        /**
         * Display importer page footer HTML.
         */
        private function _displayFooter()
        {
            echo '</div>';
        }

        /**
         * Display checkbox.
         *
         * @param $name
         * @param string $label
         * @param bool $checked
         * @param string $description
         * @param string $value
         * @param string $type
         * @param string $class
         * @return string
         */
        private function _displayCheckbox(
            $name,
            $label = '',
            $checked = false,
            $description = '',
            $value = '1',
            $type = 'checkbox',
            $class = ''
        ) {
            $id = sanitize_title('slickplan-importer-form-' . $name . '-' . $value);
            $attrs = array(
                'type' => $type,
                'name' => 'slickplan_importer[' . $name . ']',
                'value' => $value,
                'id' => $id,
            );
            if ($class) {
                $attrs['class'] = $class;
            }
            if ($checked) {
                $attrs['checked'] = 'checked';
            }

            $html = '<label for="' . $id . '"><input';
            foreach ($attrs as $key => $value) {
                $html .= ' ' . $key . '="' . esc_attr($value) . '"';
            }
            $html .= '>' . $label . '</label>';
            if (is_array($description)) {
                $html .= implode($description);
            } elseif ($description) {
                $html .= '<br><span class="description">(' . $description . ')</span>';
            }
            return $html;
        }

        /**
         * Display radio element.
         *
         * @param $name
         * @param string $label
         * @param bool $checked
         * @param string $description
         * @param bool $checked
         * @param string $class
         * @return string
         */
        private function _displayRadio(
            $name,
            $label = '',
            $value = '',
            $description = '',
            $checked = false,
            $class = ''
        ) {
            return $this->_displayCheckbox($name, $label, $checked, $description, $value, 'radio', $class);
        }

        /**
         * Display dropdown element with users.
         *
         * @param $name
         * @param string $selected
         */
        private function _displayUsersDropdown($name, $selected = '')
        {
            wp_dropdown_users(array(
                'selected' => $selected,
                'name' => 'slickplan_importer[' . $name . ']',
            ));
        }

        /**
         * Parse Slickplan's XML file. Converts an XML DOMDocument to an array.
         *
         * @param string $input_xml
         * @return array
         */
        private function _parseSlickplanXml($input_xml)
        {
            $input_xml = trim($input_xml);
            if (substr($input_xml, 0, 5) === '<?xml') {
                try {
                    $xml = new DomDocument('1.0', 'UTF-8');
                    $xml->xmlStandalone = false;
                    $xml->formatOutput = true;
                    $xml->loadXML($input_xml);
                } catch (Exception $e) {
                    return new WP_Error(SLICKPLAN_PLUGIN_ID, 'XML parse error: ' . $e->getMessage());
                }
                if (isset($xml->documentElement->tagName) and $xml->documentElement->tagName === 'sitemap') {
                    $array = $this->_parseSlickplanXmlNode($xml->documentElement);
                    if ($this->_isCorrectSlickplanXmlFile($array)) {
                        if (isset($array['diagram'])) {
                            unset($array['diagram']);
                        }
                        if (isset($array['section']['options'])) {
                            if (!isset($array['section']['options']['id']) and isset($array['section']['@attributes']['id'])) {
                                $array['section']['options']['id'] = $array['section']['@attributes']['id'];
                            }
                            $array['section'] = array($array['section']);
                        }
                        $array['sitemap'] = $this->_getMultidimensionalArrayHelper($array);
                        $array['users'] = array();
                        $array['pages'] = array();
                        foreach ($array['section'] as $section_key => $section) {
                            if (isset($section['cells']['cell']) and is_array($section['cells']['cell'])) {
                                foreach ($section['cells']['cell'] as $cell_key => $cell) {
                                    if (
                                        isset($section['options']['id'], $cell['level'])
                                        and $cell['level'] === 'home'
                                        and $section['options']['id'] !== 'svgmainsection'
                                    ) {
                                        unset($array['section'][$section_key]['cells']['cell'][$cell_key]);
                                    }
                                    if (isset(
                                        $cell['contents']['assignee']['@value'],
                                        $cell['contents']['assignee']['@attributes']
                                    )) {
                                        $array['users'][$cell['contents']['assignee']['@value']]
                                            = $cell['contents']['assignee']['@attributes'];
                                    }
                                    if (isset($cell['@attributes']['id'])) {
                                        $array['pages'][$cell['@attributes']['id']] = $cell;
                                    }
                                }
                            }
                        }
                        unset($array['section']);
                        return $array;
                    }
                }
            }
            return new WP_Error(SLICKPLAN_PLUGIN_ID, 'Invalid file format. Please use XML file you exported from '
                . 'Slickplan (<a href="https://help.slickplan.com/hc/en-us/articles/202487180" '
                . 'target="_blank">How do I export my sitemaps?</a>)');
        }

        /**
         * Parse single node XML element.
         *
         * @param DOMElement $node
         * @return array|string
         */
        private function _parseSlickplanXmlNode($node)
        {
            if (isset($node->nodeType)) {
                if ($node->nodeType === XML_CDATA_SECTION_NODE or $node->nodeType === XML_TEXT_NODE) {
                    return trim($node->textContent);
                } elseif ($node->nodeType === XML_ELEMENT_NODE) {
                    $output = array();
                    for ($i = 0, $j = $node->childNodes->length; $i < $j; ++$i) {
                        $child_node = $node->childNodes->item($i);
                        $value = $this->_parseSlickplanXmlNode($child_node);
                        if (isset($child_node->tagName)) {
                            if ($node->tagName === 'body' and is_array($value)) {
                                $value['type'] = $child_node->tagName;
                                $output[] = $value;
                            } else {
                                if (!isset($output[$child_node->tagName])) {
                                    $output[$child_node->tagName] = array();
                                }
                                $output[$child_node->tagName][] = $value;
                            }
                        } elseif ($value !== '') {
                            $output = $value;
                        }
                    }
                    if (is_array($output)) {
                        foreach ($output as $tag => $value) {
                            if (is_array($value) and count($value) === 1) {
                                $output[$tag] = $value[0];
                            }
                        }
                        if (empty($output)) {
                            $output = '';
                        }
                    }

                    if ($node->attributes->length) {
                        $attributes = array();
                        foreach ($node->attributes as $attr_name => $attr_node) {
                            $attributes[$attr_name] = (string)$attr_node->value;
                        }
                        if (!is_array($output)) {
                            $output = array(
                                '@value' => $output,
                            );
                        }
                        $output['@attributes'] = $attributes;
                    }
                    return $output;
                }
            }
            return array();
        }

        /**
         * Check if the array is from a correct Slickplan XML file.
         *
         * @param array $array
         * @param bool $parsed
         * @return bool
         */
        private function _isCorrectSlickplanXmlFile($array, $parsed = false)
        {
            $first_test = (
                $array
                and is_array($array)
                and isset($array['title'], $array['version'], $array['link'])
                and is_string($array['link']) and strstr($array['link'], 'slickplan.')
            );
            if ($first_test) {
                if ($parsed) {
                    if (isset($array['sitemap']) and is_array($array['sitemap'])) {
                        return true;
                    }
                } elseif (
                    (isset($array['section']['cells']) or isset($array['section'][0]['cells']))
                    and (
                        isset($array['section']['options']['id'])
                        or isset($array['section'][0]['options']['id'])
                        or isset($array['section']['@attributes']['id'])
                    )
                ) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Get multidimensional array, put all child pages as nested array of the parent page.
         *
         * @param array $array
         * @return array
         */
        private function _getMultidimensionalArrayHelper(array $array)
        {
            $cells = array();
            $main_section_key = -1;
            $relation_section_cell = array();
            foreach ($array['section'] as $section_key => $section) {
                if (
                    isset($section['@attributes']['id'], $section['cells']['cell'])
                    and is_array($section['cells']['cell'])
                ) {
                    foreach ($section['cells']['cell'] as $cell_key => $cell) {
                        if (isset($cell['@attributes']['id'])) {
                            $cell_id = $cell['@attributes']['id'];
                            if (isset($cell['section']) and $cell['section']) {
                                $relation_section_cell[$cell['section']] = $cell_id;
                            }
                        } else {
                            unset($array['section'][$section_key]['cells']['cell'][$cell_key]);
                        }
                    }
                } else {
                    unset($array['section'][$section_key]);
                }
            }
            foreach ($array['section'] as $section_key => $section) {
                $section_id = $section['@attributes']['id'];
                if ($section_id !== 'svgmainsection') {
                    $remove = true;
                    foreach ($section['cells']['cell'] as $cell_key => $cell) {
                        $cell['level'] = (string)$cell['level'];
                        if ($cell['level'] === 'home') {
                            unset($array['section'][$section_key]['cells']['cell'][$cell_key]);
                        } elseif ($cell['level'] === '1' and isset($relation_section_cell[$section_id])) {
                            $array['section'][$section_key]['cells']['cell'][$cell_key]['parent']
                                = $relation_section_cell[$section_id];
                            $remove = false;
                            $array['section'][$section_key]['cells']['cell'][$cell_key]['order'] *= 10;
                        }
                    }
                    if ($remove) {
                        unset($array['section'][$section_key]);
                    }
                } else {
                    $main_section_key = $section_key;
                    foreach ($section['cells']['cell'] as $cell_key => $cell) {
                        $array['section'][$section_key]['cells']['cell'][$cell_key]['order'] /= 1000;
                    }
                }
            }
            foreach ($array['section'] as $section_key => $section) {
                $section_cells = array();
                foreach ($section['cells']['cell'] as $cell_key => $cell) {
                    $section_cells[] = $cell;
                }
                usort($section_cells, array($this, '_sortPages'));
                $array['section'][$section_key]['cells']['cell'] = $section_cells;
                $cells = array_merge($cells, $section_cells);
                unset($section_cells);
            }
            $multi_array = array();
            if (isset($array['section'][$main_section_key]['cells']['cell'])) {
                foreach ($array['section'][$main_section_key]['cells']['cell'] as $cell) {
                    if (isset($cell['@attributes']['id']) and (
                            $cell['level'] === 'home' or $cell['level'] === 'util' or $cell['level'] === 'foot'
                            or $cell['level'] === '1' or $cell['level'] === 1
                        )
                    ) {
                        $level = $cell['level'];
                        if (!isset($multi_array[$level]) or !is_array($multi_array[$level])) {
                            $multi_array[$level] = array();
                        }
                        $childs = $this->_getMultidimensionalArray($cells, $cell['@attributes']['id']);
                        $cell = array(
                            'id' => $cell['@attributes']['id'],
                            'title' => $this->_getFormattedTitle($cell),
                        );
                        if ($childs) {
                            $cell['childs'] = $childs;
                        }
                        $multi_array[$level][] = $cell;
                    }
                }
            }
            unset($array, $cells, $relation_section_cell);
            return $multi_array;
        }

        /**
         * Put all child pages as nested array of the parent page.
         *
         * @param array $array
         * @param string $parent
         * @return array
         */
        private function _getMultidimensionalArray(array $array, $parent)
        {
            $cells = array();
            foreach ($array as $cell) {
                if (isset($cell['parent'], $cell['@attributes']['id']) and $cell['parent'] === $parent) {
                    $childs = $this->_getMultidimensionalArray($array, $cell['@attributes']['id']);
                    $cell = array(
                        'id' => $cell['@attributes']['id'],
                        'title' => $this->_getFormattedTitle($cell),
                    );
                    if ($childs) {
                        $cell['childs'] = $childs;
                    }
                    $cells[] = $cell;
                }
            }
            return $cells;
        }

        /**
         * Sort cells.
         *
         * @param array $a
         * @param array $b
         * @return int
         */
        private function _sortPages(array &$a, array &$b)
        {
            if (isset($a['order'], $b['order'])) {
                return ($a['order'] < $b['order']) ? -1 : 1;
            }
            return 0;
        }

        /**
         * Change the WordPress language
         *
         * @param string $language
         */
        private function _changeLanguage($language)
        {
            $new_language = false;
            if ($language !== get_locale()) {
                $languages = get_available_languages();
                if (in_array($language, $languages)) {
                    $new_language = $language;
                } elseif (wp_can_install_language_pack()) {
                    $languages = wp_get_available_translations();
                    if (is_array($languages) and isset($languages[$language])) {
                        $new_language = wp_download_language_pack($language);
                    }
                }
            }
            if ($new_language) {
                update_option('WPLANG', $new_language);
                load_default_textdomain($new_language);
            }
        }

    }

    add_action('admin_init', 'slickplan_importer_init');

    register_activation_hook(__FILE__, 'slickplan_importer_activation_hook');
    register_deactivation_hook(__FILE__, 'slickplan_importer_deactivation_hook');

    add_action('wp_ajax_slickplan-importer', 'slickplan_importer_ajax_action');
    add_action('wp_ajax_nopriv_slickplan-importer', 'slickplan_importer_ajax_action');


    $slickplan = new Slickplan_Importer;

    /**
     * Slickplan Importer initialization
     */
    function slickplan_importer_init()
    {
        global $slickplan;

        register_importer(
            SLICKPLAN_PLUGIN_ID,
            'Slickplan',
            'The Slickplan Importer plugin allows you to quickly import your '
                . '<a href="http://slickplan.com" target="_blank">Slickplan</a> project into your WordPress site.',
            array($slickplan, 'dispatch')
        );

        $plugin = get_plugin_data(__FILE__);
        $version = isset($plugin['Version']) ? $plugin['Version'] : false;

        wp_enqueue_script(SLICKPLAN_PLUGIN_ID . '-scripts', SLICKPLAN_PLUGIN_URL . 'assets/scripts.js', array(
            'json2',
            'jquery',
            'jquery-ui-core',
            'jquery-ui-progressbar',
        ), $version, true);
        wp_enqueue_style(SLICKPLAN_PLUGIN_ID . '-styles', SLICKPLAN_PLUGIN_URL . 'assets/styles.css', array(),
            $version);
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

        wp_localize_script(SLICKPLAN_PLUGIN_ID . '-scripts', 'slickplan_ajax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(SLICKPLAN_PLUGIN_OPTION),
            'html' => $slickplan->getSummaryRow(array(
                'post_title' => '{title}',
                'loading' => 1,
            )),
        ));
    }

    /**
     * Plugin activation hook. Register an option.
     */
    function slickplan_importer_activation_hook()
    {
        add_option(SLICKPLAN_PLUGIN_OPTION, '', '', 'no');
    }

    /**
     * Plugin deactivation hook. Removes an option from database.
     */
    function slickplan_importer_deactivation_hook()
    {
        delete_option(SLICKPLAN_PLUGIN_OPTION);
    }

    /**
     * AJAX action
     */
    function slickplan_importer_ajax_action()
    {
        global $slickplan;
        check_ajax_referer(SLICKPLAN_PLUGIN_OPTION, false, true);
        $result = array();
        if (isset($_POST['slickplan']) and is_array($_POST['slickplan'])) {
            $result = $slickplan->ajaxImport($_POST['slickplan']);
        }
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($result);
        wp_die();
    }

}