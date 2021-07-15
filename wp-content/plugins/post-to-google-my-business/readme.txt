=== Post to Google My Business ===
Contributors: koen12344, freemius
Donate link: https://tycoonmedia.net/?utm_source=repository&utm_medium=link&utm_campaign=donate
Tags: google my business, google, business, auto publish, posts, post, local search, google my business posts, google places, google plus, google+
Requires at least: 4.9.0
Tested up to: 5.8
Stable tag: 2.2.43
Requires PHP: 5.6.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily create new posts on Google My Business directly from the WordPress Dashboard!

== Description ==

The new posts functionality in Google My Business is a great way to improve the presence of your, or your clients' business on Google. It can be a hassle however to have to log in to Google My Business every time you want to create a new post, likewise it can be an easy thing to forget.

Don't miss out on the SEO benefit, and save time by creating posts on Google My Business directly from the WordPress Dashboard!

Use the Auto-post feature to instantly publish your latest WordPress post to Google My Business, based on a preset template and the posts' featured image.

The Post to Google My Business plugin utilizes the official Google My Business API with secure oAuth authentication to ensure your Google account is safe.

= New: "COVID-19 update" post type support =
The plugin now supports the new **"COVID-19 Update"** post type, which allows you to share announcements about how your business is dealing with the coronavirus pandemic.

= Features =
* Create, edit or delete posts without having to visit your Google My Business dashboard
* Automatically publish your latest WordPress posts to GMB using the Auto-post feature
* Network- and site-level Multisite support
* Supports publishing to GMB from external apps (such as Windows Live Writer, Zapier, Integromat, ManageWP, InfiniteWP, MainWP etc)
* Uses official Google My Business API
* Developer friendly. Uses the latest built-in WordPress functions and has various actions/filters to hook into.
* Translatable. Uses built-in WordPress functions for easy translation.
* Gutenberg compatible

> **Time-saving features available in the Premium version:**
>
> * Schedule Google My Business posts for automatic publishing in the future
> * Create event and offer posts
> * Create new Google My Business posts from any WordPress post type (e.g. WooCommerce products)
> * Pick location per post, or post to multiple locations at once
> * Automatic re-posting (post recycling) - Automatically recycle your GMB posts at preset intervals and x amount of times
> * Auto publish posts with specific tags or categories
> * Make unique posts using Spintax and %variables%
> * Post Campaigns - Create posts on GMB that aren't tied to any specific WordPress post or page.
> * Post Analytics - See how many views and clicks your post has gotten straight from the WordPress Dash
> * Increased posting (API) limits
> * Priority email support from the developer of the plugin
> * Support and updates for 1 year
> * Much more!
>
> **[Learn more about Post to Google My Business Premium](https://tycoonmedia.net/?utm_source=repository&utm_medium=link&utm_campaign=learn_more&utm_content=description)**

= Great support! =
We're here to help in case you're having trouble using Post to Google My Business. Just ask in the support forum and we'll get back to you ASAP. Feedback and ideas to improve the plugin are always welcome.

== Installation ==

Installing and configuring Post to Google My Business is easy!

1. Upload the plugin files to the `/wp-content/plugins/post-to-google-my-business` directory, or install it through the plugins page within the WordPress Dashboard.
2. Activate the plugin through the **Plugins** page in WordPress
3. Go to the **Post to GMB** > **Settings** page to configure the plugin
4. To allow your website to post to Google My Business on your behalf, click **Connect to Google My Business**. Confirm the authorization using the Google account that holds your business location.
5. You will be redirected back to the settings page. Select your business location and press **Save Changes**.
6. All set! When creating a new WordPress **Post** there will a new metabox that allows you to create posts on Google My Business.


== Frequently Asked Questions ==

= Can I use this plugin on a localhost installation? =

Yes, but you may run into errors if you add a link or image to your post. Google will try to fetch your image/video, or resolve the link to your website, but if your localhost installation can't be reached from the outside world, it won't be able to do so.
The quick post feature will not work at all in that case, because it uses the URL and Featured Image of your post.

= Why is/are my location(s) grayed out? =

Not every Google My Business listing is allowed to create posts on Google My Business (localPostAPI is disabled). This means the plugin can't create posts on those locations. First, make sure your location is fully verified & live. Business chains (10+ locations) are normally exempt from creating posts, but are temporarily allowed to create them to share updates about the corona virus.

= Why are my scheduled posts being published too late/not at all? =

Post to Google My Business relies on the WP Cron system to send out scheduled posts. By default, it is only triggered when someone visits your website. If your site doesn't get a lot of visitors, your posts may be sent out too late. To make the WP Cron system more dependable, you can [hook it into the system task scheduler](https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/)


== Screenshots ==

1. Customizing and posting GMB post
2. Using the Auto-post feature
3. Creating a "What's new" post
4. Creating an event
5. Creating an offer post
6. Auto-post template settings

== Changelog ==

= 2.2.43 =
* Tested on WordPress 5.8
* Fixed issue with metabox not working in block editor

> **Premium**
>
> * Fixed issue with automatic re-post that made a post go out every minute(!) instead of every day of the month
> * Added "Daily" option to re-post feature

= 2.2.42 =
* Add Brazilian Portuguese translations (thanks to @valdemirmaran)

= 2.2.41 =
* Add selection function for location groups
* Add note for Block Editor CPTs without "custom-fields" support enabled
* Update Freemius SDK

= 2.2.39 =
* Fixes "Cannot declare class PGMB\Vendor\Cron\AbstractField, because the name is already in use" error when saving post

= 2.2.34 =
* Fix for image size detection logic

= 2.2.33 =
* Add extra error message for when Product post type is chosen
* Fix block editor issue for custom post types that don't have custom-fields enabled

= 2.2.32 =
* Use wp_get_http_headers instead of get_headers for better compatibility

= 2.2.31 =
* Testing on WP 5.6, small fixes for PHP8 compatibility

= 2.2.29 =
* Fixed some issues with checking of post image size
* Fixed image not showing up in editor when it was too small to have a "medium" thumbnail
* Updated Freemius SDK

= 2.2.28 =
* Fix compatibility issue with older version of plugin

= 2.2.27 =
* Fix date and time checker
* Fix checkbox state not being loaded since 5.5.1

= 2.2.26 =
* Added (a lot) of location-specific variables

= 2.2.25 =
* Make all GMB post types and WP post types available in free version
* Fix Gutenberg assets being loaded in post type that was disabled

> **Premium**
>
> * Fix campaigns feature being partially available in Pro version
> * Fix default location selector showing checkboxes instead of radiobuttons when refreshing

= 2.2.19 =
* Fixed handling of URLs containing non-ascii characters
* Added more info about product posts

= 2.2.18 =
* Temporarily remove check for isLocalPostApiDisabled due to COVID-19

= 2.2.17 =
* Added COVID-19 post
* Added full autopost template editor in settings to edit the default autopost template
* Added welcome message
* Added location-specific variables (%location_primaryPhone%, %location_websiteUrl% ...)
* Added new mbp_placeholder_decorators ($decorators, $parent_post_id) filter, and VariableInterface
* Add checks for image size, disable video (no longer allowed by Google)
* Simplified & improved auto-post logic
* Improved image uploader (ready for 10 images, but Google API doesn't support it yet)
* Improved error notices in metabox
* Improvements to multisite handling and activation/deactivation/delete routines
* Fixed error notice when fetching empty location groups
* Fixed Free version not deactivating when activating premium
* Fixes empty location group causing PHP notice
* Fixed Gutenberg double post bug and improve compatibility
* Fix Draft post showing incorrect publish date
* Fix notice when Google error doesn't include details
* Update Freemius SDK to fix multisite issue

> **Premium**
>
> * Add "Event all day" checkbox for events without a start and end time
> * Added feature to enable autopost per category or tag
> * Fixed Auto-post template editor button being shown on campaigns page
> * Fixed errors when when all post types are unchecked in the settings page

= 2.2.10 =
* Fix issue with paragraphs getting removed in auto-post

= 2.2.9 =
* Fix auto-post incorrectly throwing 1500 character error
* Hide "Save draft" button on already published post

> **Premium**
>
> * Fix caption on "Save Template" button switching to "Publish" when adding schedule

= 2.2.8 =
* Fix gutenberg issue caused by 2.2.7

= 2.2.7 =
* Fix compatibility issue with Yoast SEO & Classic Editor

= 2.2.6 =
* Updated .pot file and Grunt scripts
* Updated Dutch, Russian and Ukrainian translations
* Display post publish date in metabox

= 2.2.5 =
* Fix duplicate post issue

> **Premium**
>
> * Remove Auto-post checkbox being shown on campaigns page

= 2.2.4 =
* Fix issues with CTA URL on button
* Improve updater (again)
* Fix default value for CTA URL field
* Fix CTA url field disappearing when loading post

> **Premium**
>
> * Fix repost schedule being improperly parsed

= 2.2.3 =
* Improve updater

= 2.2.2 =
* Fix Form field parser allowing dates in the past

= 2.2.1 =
* Fix for Gutenberg autopost
* Restore filter functions
* Update Freemius SDK

= 2.2 =
* Moved API communication to an asynchronous process
* Added Auto-post template editor
* Added Debug info tab to settings page
* Added dialog with created posts
* Made UI more intuitive
* Added some fixes to improve compatibility with Gutenberg
* Added functionality to fetch image from content or use the featured image
* Tons of improvements and bug fixes "under the hood"
* Lots more to come in future updates!

> **Premium**
>
> * Made re-posting much more flexible
> * Re-posted posts will now appear as a separate post in the metabox

= 2.1.18 =
> **Premium**
>
> * Actually parse the relative datetimes on scheduled posts :)
> * Disable Product post support (removed from GMB api)
> * Improve display of datetimes, better timezone handling

= 2.1.17 =
> **Premium**
>
> * Allow relative time notation in datetimepickers

= 2.1.16 =
* Improved parsing of post content

= 2.1.11 =
* Improve development & deployment methods

= 2.1.10 =
* Update Freemius SDK
* Remove shortcodes from auto-post by default

= 2.1.9 =
* Security fix

= 2.1.8 =
* Fixes issue with image URL spinner with no image set (PHOTO media error)

= 2.1.7 =
* Properly delete child posts and schedules when deleting parent post

= 2.1.6 =
> **Premium**
>
> * Spin image URL

= 2.1.5 =
* Fix issue when trying to load more than 100 locations
* Apply filter mbp_get_locations filter to cached locations

= 2.1.4 =
* Fix settings page conflict caused by plugins using old version of WeDevs Settings API
* Fix Learn more link about grayed out locations

= 2.1.3 =
* Fix auto-post sending campaign posts
* Improve Gutenberg compatibility

= 2.1.2 =
* Fix auto-post being triggered too early
* Simplify business selector

> **Premium**
>
> * Fix repost sheduled events not getting deleted
> * Fix reposts not being published when recurrence was set to 0
> * Fix reposts being posted twice when not scheduled

= 2.1.1 =
* Increase API timeout
* Fix multiline posts
* Improve error messages
* Fix API token refresh requests when network activated

= 2.1.0 =
* Improves location loading (+100 Locations in account)
* Check whether locations have access to the Posts API
* Call Now button support
* Improves auto-post logic
* Restructuring code

> **Premium**
>
> * Improves posting to multiple locations at once
> * Product post support

= 2.0.10 =
* Update Freemius SDK
* Add filters to Auto post feature

> **Premium**
>
> * Add option to edit Auto post URL

= 2.0.9 =
* Fix 500 error on PHP 5.4 https://wordpress.org/support/topic/500-error-when-adding-a-new-post/

= 2.0.8 =
* Fix issue some settings getting deleted when updating from 2.0.6 to 2.0.7
* Improve compatibility with external publishing apps and services
* Made plugin settings page more intuitive

= 2.0.7 =
* Strip HTML from posts
* Cut posts to 1500 characters
* Added word and character counters
* Simplified business selector
* Removed user selector, now integrated with business selector
* Better support for grouped locations
* Allow filtering/searching of locations
* Remove references to datetimepicker

> **Premium**
>
> * Added buttons to Select/Deselect all locations at once
> * Fixed some issues with Pro features in trial

= 2.0.6 =
* Add ability to save posts as draft
* Add option to invert the "Quick Publish" checkbox. Allows you to automatically publish to GMB when the WordPress post is created externally
* Fix display issue when location has no thumbnail
* Add placeholder index.php files to plugin folders
* Show "No GMB posts found." again when last post is deleted.
* Close form when the post currently being edited is being deleted

> **Premium**
>
> * Fix Premium features not being enabled when in trial
> * Fix scheduled posts not being posted in Pro
> * Fix issue with post type settings causing error when no post type is selected and settings are saved
> * Check default location when creating new post
> * Fix Metabox not visible on post campaigns
> * Fix invisible month switching icons on the datetimepicker


= 2.0.5 =
* Fix location info when importing old posts
* Fix Google link not appearing when using quickpost

= 2.0.4 =
* Version function magically disappeared, fixed

= 2.0.3 =
* Fixes updating issue on multisite

= 2.0.2 =
* Fix issue causing fatal error on PHP 5.6 < https://wordpress.org/support/topic/2-0-1-update-crashes-site/

= 2.0.1 =
* Fixes issue with certain Google post fields not updating when updating post

= 2.0.0 =
* Improved metabox, easily create multiple GMB posts per WordPress post
* Supports new Google post types
* Added Quick post feature to create GMB posts based on a preset template. All you have to do is tick the checkbox to post!
* Fixed plugin conflict causing endless loop
* Improved and simplified settings page
* Improved business selector
* And much, much more!

> **New premium features**
>
> * Automatic reposting - Choose time interval and amount of times to repost
> * Post spinning and variables - Make your posts unique using Spintax and variables such as %site_name%, %post_title%
> * Video posts
>
> **[Learn more about Post to Google My Business Premium](https://tycoonmedia.net/?utm_source=repository&utm_medium=link&utm_campaign=learn_more&utm_content=changelog)**

= 1.2 =
* Added logic for cleaning up options when uninstalling plugin
* Improved admin/error notices

= 1.1.1 =
* Fixed PHP compatibility issue https://wordpress.org/support/topic/getting-parse-error-when-installing/

= 1.1 =
* Improved business location selector
* Fixed timepicker issues
* Javascript for metabox now in separate file
* Fixed incorrect language code causing issues when posting
* Various other small improvements and fixes

= 1.0 =
* Initial release

== Upgrade Notice ==
