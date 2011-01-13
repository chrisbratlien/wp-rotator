=== WP Rotator ===
Contributors: chrisbratlien, billerickson
Donate link: http://chrisbratlien.com/
Tags: rotator, image, featured, javascript, slider, crossfade
Requires at least: 2.9.2
Tested up to: 3.0.2
Stable tag: trunk

Rotator for featured images. Slide or crossfade. Posts chosen using query vars, just like query_posts() uses.

== Description ==

Rotator for featured images. Control which posts to rotate with Query Vars setting. Slide or cross-fade animation style. Control animate/rest delay times. Toggle info box and override clicktrough URLs at a per-post level. Use hooks to override the CSS, Javascript, or markup in your theme's functions.php file.

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

== Upgrade Notice ==

= 0.2.1 =
This version fixes a z-index issue affecting clickthrough URLs. It also uses a cleaner Javascript namespace
