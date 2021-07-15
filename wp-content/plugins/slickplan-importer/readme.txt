=== Plugin Name ===
Contributors: slickplan
Donate link: https://slickplan.com/
Tags: slickplan, import, xml
Requires at least: 3.0
Tested up to: 5.8
Stable tag: trunk
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The Slickplan import plugin allows you to quickly import your Slickplan projects into your WordPress site.

When you are finished planning your website project, import your Slickplan website plan. Upon import, your pages, navigation structure, and content will be instantly ready in your CMS.

This plugin is compatible with the Gutenberg page builder. It automatically imports your Slickplan's Content Planner blocks into corresponding blocks in WordPress.

== Installation ==

1. Upload the `slickplan-importer` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the 'Tools' -> 'Import' screen, Click on 'Slickplan'

== Changelog ==

= 2.2 =
* New: Added basic support for Gutenberg
* New: Dropped support for PHP 5
* Fix: Non-recognizable files in Content Planner upload blocks with "multiple" option enabled

= 2.1.3 =
* Fix: Custom menu name now works as expected

= 2.1.2 =
* Fix: Non-recognizable XML file when section ID is missing

= 2.1.1 =
* Fix: Import content blocks in correct order, fixed file download bug

= 2.1 =
* New: Added support for internal links when importing content
* New: Added an information about approx total files size to "import files" option
* Fix: Don't use AJAX importer when importing content from notes
* Fix: Hide "import files" option when there is no files to import

= 2.0 =
* New: Import is now a 2 steps process - import options have been moved to Step 2
* New: Added Content Planner support - plugin now can import page content, SEO meta and download files to media library
* Fix: Dropped the PHP's libxml extension requirement, plugin now requires DOMElement which is default PHP5 extension

= 1.2 =
* New: Automatically add currect page order attribute

= 1.1 =
* Fix: Support for PHP 5.2

= 1.0.1 =
* New: WordPress 4.x compatibility

= 1.0 =
* New: Options to manipulate page titles prior to import
* Fix: updated styling to suit WordPress 3.8

= 0.4 =
* New: Option to ignore pages marked as 'External' page type

= 0.3 =
* New: Import sections

= 0.2 =
* New: Import notes as pages contents
* New: Import footer section items

= 0.1 =
* Initial release
