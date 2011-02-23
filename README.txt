=== WP Rotator ===
Contributors: chrisbratlien, billerickson
Donate link: http://wprotator.com/
Tags: rotator, image, featured, javascript, slider, crossfade
Requires at least: 2.9.2
Tested up to: 3.0.4
Stable tag: 0.3

WP Rotator is a plugin designed for developers to quickly and easily create custom rotators.

== Description ==

    * Uses query_posts() parameters to specify what shows up in the rotator.
    * Uses Post Thumbnails for the images rotating.
    * The Javascript, CSS, and Markup are all functions that can be unhooked and replaced by your code.
    * Refer to the Documentation for more details on customizing it.

http://www.wprotator.com/documentation

== Settings ==

Go to Dashboard > Settings > WP Rotator

== Usage == 

Add the following PHP code to your template

do_action('wp_rotator');

== Changelog ==

= 0.2.1 =
* fixed z-index issue affecting clickthrough URLs
* Put Javascript into WPROTATOR namespace to prevent conflicts
* added [wp_rotator] shortcode
* added new filter hook: wp_rotator_featured_cell_markup to allow further customization
* added customization hook examples to Admin page

= 0.2.2 =
* New hook wp_rotator_use_this_post for fine-grained control of which posts are included

= 0.3 = 
* Replaced our image sizing with add_image_size(). If you change the image, you need to use the Regenerate Thumbnails plugin to fix your thumbnails. Benefit of this method is images are scaled and cropped, rather than distorted to fit. For backwards compatibility, if the thumbnail doesn't match the dimensions of add_image_size() it distorts it to fit. 
* Rebuilt the settings page using the Settings API, so there's nonces and data sanitation.
* Added prefix to custom field we use (wp_rotator_url and wp_rotator_show_info). If they don't have values, it checks the old, non-prefixed ones for backwards compatibility.
* Added documentation to the code and to wprotator.com
* Cleaned up the default css.
* Added WP Rotator Widget.
* Attached javascript to wp_footer and admin_footer rather than wp_head/admin_head
* Replaced scrollTo.js with minified version
* Moved the scrollTo reference out of the default javascript block, registered it as a script, and enqueued it (next to enqueue_script('jquery')).


== Upgrade Notice ==

= 0.2.1 =
This version fixes a z-index issue affecting clickthrough URLs. It also uses a cleaner Javascript namespace

= 0.2.2 =
Added new hook called wp_rotator_use_this_post for providing fine-grained control over which posts are included

= 0.3 =
Changed custom fields from url and show_info to wp_rotator_url and wp_rotator_show_info. It still checks for old ones to be backwards-compatible, but it's recommended to use the prefixed ones.