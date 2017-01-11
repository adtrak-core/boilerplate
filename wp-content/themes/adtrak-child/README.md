# Readme

- Put your WP Parts within parts. Declare using `get_template_part('parts/part-name.php')`. In some situations you may need to use `include locate_template('parts/part-name')` if you need to pass PHP in and out of it. Self contained php use `get_template_part`
- Put all your files in the predefined folders.
- Use `get_theme_file_uri('images/logo.svg')` when referencing files withing (WordPress 4.7+).
- Use `get_parent_theme_file_uri('css/style.scss')` if you need to reference anything in the parent theme (WordPress 4.7+).

## Fields
Available fields to use with the plugin. Last updated Dec 7, 2016.

### Company
* site_logo (image, array)
* site_email (email)
* site_address (repeater)
	* address_line (text)
* site_postcode (text)
* vat_number (text)
* reg_number (text)

### Phone / Location
* default_location (text)
* prefix_phone_number (text)
* default_phone_number (text)

### Social Links
* social_twitter (url)
* social_facebook (url)
* social_google (url)
* social_instagram (url)
* social_pinterest (url)

### Logos / Clients
* logos_header (text)
* logos (repeater)
	* name (text)
	* image (image, array)
	* link (url)

### Credit Cards
* cc_header (text)
* credit_cards (repeater)
	* card (image, array)

### Why Choose Us
* why_header (text)
* why_choose_us (repeater)
	* icon (text)
	* reason (text)

### Buckets
* buckets_header (text)
* buckets (repeater)
	* image (image, array)
	* text (text area)
	* link (url) 