=== WP Rotator ===
Contributors: chrisbratlien, billerickson
Donate link: http://wprotator.com/
Tags: rotator, image, featured, javascript, slider, crossfade
Requires at least: 2.9.2
Tested up to: 3.0.4
Stable tag: 0.2.2

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


== Upgrade Notice ==

= 0.2.1 =
This version fixes a z-index issue affecting clickthrough URLs. It also uses a cleaner Javascript namespace

= 0.2.2 =
Added new hook called wp_rotator_use_this_post for providing fine-grained control over which posts are included
