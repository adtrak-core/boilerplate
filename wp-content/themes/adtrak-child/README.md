# Readme
- Never edit the parent theme - this would be overwritten if we ever update it
- Put your WP Parts within parts. Declare using `include locate_template('parts/part-name.php')`
- Put all your files in the predefined folders
- Use `get_theme_file_uri('images/logo.svg')` when referencing files withing (WordPress 4.7+)
- Use `get_parent_theme_file_uri('css/style.scss')` if you need to reference anything in the parent theme (WordPress 4.7+)
- There is currently no forms on this theme - the theme is built to be ready for the new form plugin

## Name your theme
Don't forget to rename your theme;
- The folder name (from adtrak-child to something-else)
- The style.css
- Create a new screenshot.png with the client logo

## Buckets	
The buckets are built using relationship fields in the WordPress admin. It automatically pulls through the featured image from those pages into the buckets, as well as the title. The excerpt needs to be hand written on the "receiving" page. Excerpts for pages are already turned on in functions.php

If you wish to use icons or HTML images instead of background images, you will need to create a new image field on the "receiving" page, and instead pull the image from there (presuming you don't wish to use an icon as the featured image).

To pull through the featured image as an HTML image, use this code: <?php echo get_the_post_thumbnail( $p->ID, 'hero-600' ); ?>

## Useful Resources
Lots of useful resources like loops for ACF, video heres and menus can be found here: http://resources.ad.trak.agency/web-design/coding/wordpress/wordpress-snippets/

## Fields
Available fields to use with the plugin. Last updated Feb 16, 2017.

## Site Options

#--- Company
* site_logo (image, array)
* site_email (email)
* site_address (repeater)
	* address_line (text)
* site_postcode (text)
* vat_number (text)
* reg_number (text)

#--- Phone / Location
* default_location (text)
* prefix_phone_number (text)
* default_phone_number (text)

#--- Social Links
* social_twitter (url)
* social_facebook (url)
* social_google (url)
* social_instagram (url)
* social_pinterest (url)
* social_linkedin (url)

#--- Logos / Clients
* logos_header (text)
* logos (repeater)
	* name (text)
	* image (image, array)
	* link (url)

#--- Credit Cards
* cc_header (text)
* credit_cards (repeater)
	* card (image, array)

#--- Why Choose Us
* why_header (text)
* why_choose_us (repeater)
	* icon (text)
	* reason (text)