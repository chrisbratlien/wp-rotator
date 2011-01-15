=== WP Rotator ===
Contributors: chrisbratlien, billerickson
Donate link: http://chrisbratlien.com/
Tags: rotator, image, featured, javascript, slider, crossfade
Requires at least: 2.9.2
Tested up to: 3.0.4
Stable tag: trunk

Rotator for featured images or custom markup. Slide or crossfade. Posts chosen using query vars, just like query_posts() uses. Has hooks for customizing

== Description ==

Rotator for featured images and custom markup. Control which posts to rotate with Query Vars setting. Slide or cross-fade animation style. Control animate/rest delay times. Toggle info box and override clicktrough URLs at a per-post level. Use hooks to override the CSS, Javascript, or markup in your theme's functions.php file.

http://chrisbratlien.com/wp-rotator

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
