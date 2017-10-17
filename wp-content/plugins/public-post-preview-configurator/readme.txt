=== Public Post Preview Configurator ===
Contributors: bjoerne
Tags: public, post, preview, posts, configuration
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XS98Y5ASSH5S4
Requires at least: 3.5
Tested up to: 4.7.2
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Enables you to configure the 'public post preview' plugin with a user interface.

== Description ==

With this plugin it's possible to configure the expiration time of a link provided by the 'public post preview' plugin.

== Installation ==

1. Upload plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure plugin (Settings -> Public Post Preview Configurator)

== Screenshots ==

1. Options

== Changelog ==

= 1.0.0 =
* Provide configuration page to configure expiration time of 'public post preview' plugin

= 1.0.1 =
* Bugfix: ppp_configurator_nonce_life filter was only applied when logged in

= 1.0.2 =
* Clean up code based on WordPress-Plugin-Boilerplate
* Remove option when plugin is uninstalled

= 1.0.3 =
* Bugfix: Reset config when entering empty value
