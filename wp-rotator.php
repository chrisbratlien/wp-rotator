<?php
/*
Plugin Name: WP Rotator
Plugin URI: http://www.wprotator.com
Description: Rotator for featured images or custom markup. Slide or crossfade. Posts chosen using query vars, just like query_posts() uses.
Version: 0.3
Author: Chris Bratlien, Bill Erickson
Author URI: http://www.wprotator.com/developers
*/



/* Set up defaults */

function wp_rotator_default_array() {
  return array(
  'query_vars' => 'post_type=rotator&status=published&showposts=-1&posts_per_page=-1',
  'animate_ms' => 1000,
  'rest_ms' => 7000,
  'animate_style' => 'fade',
  'pane_width' => 400,
  'pane_height' => 300
  );
}


function wp_rotator_default($key) {
  $options = wp_rotator_default_array();
  if (isset($options[$key])) {
    return $options[$key];
  }
  else {
    return false;
  }
}


function wp_rotator_defaulter(&$you,$your_name) {
  if (isset($you)) { return $you; }
  $stored = wp_rotator_option($your_name);
  if (!empty($stored)) { return $stored; }
  $default = wp_rotator_default($your_name);
  return $default;
}




/* Set up featured image size */

global $bsd_pane_height, $bsd_pane_width;
$bsd_pane_width = wp_rotator_defaulter($bsd_pane_width,'pane_width');
$bsd_pane_height = wp_rotator_defaulter($bsd_pane_height,'pane_height');

add_theme_support( 'post-thumbnails' );
add_image_size('wp_rotator', $bsd_pane_width, $bsd_pane_height, true);




/* [wp_rotator] Shortcode */
/*** Note: Uses wp_rotator_markup(), which is the default outer markup. ***/

function wp_rotator_shortcode($atts, $content = null) {
  return wp_rotator_markup();
}
add_shortcode('wp_rotator', 'wp_rotator_shortcode');  

/* WP Rotator Widget */
include_once('rotator-widget.php');




/* Register Options */

function wp_rotator_option($key) {
  $options = get_option('wp_rotator_options');
  if (!empty($options[$key])) { 
    return $options[$key]; 
  }
  else {
    return wp_rotator_default($key);
  }
}


function wp_rotator_custom_options_init() {
  register_setting('wp_rotator_options','wp_rotator_options');
}
add_action('admin_init', 'wp_rotator_custom_options_init');




/* WP Rotator Options Page */

function wp_rotator_custom_menus() {
add_submenu_page('options-general.php', 'WP Rotator', 'WP Rotator', 'administrator', 'wp_rotator_admin_options', 'wp_rotator_admin_menu');
  $bsdnonce = md5('bsd');
}
add_action('admin_menu', 'wp_rotator_custom_menus');



function wp_rotator_admin_menu() {

  global $current_user;
  global $user_ID, $bsdnonce;

  get_currentuserinfo();

  $options = get_option('wp_rotator_options');  
  $options['query_vars'] = wp_rotator_defaulter($options['query_vars'],'query_vars');
  $options['animate_ms'] = wp_rotator_defaulter($options['animate_ms'],'animate_ms');
  $options['rest_ms'] = wp_rotator_defaulter($options['rest_ms'],'rest_ms');
  $options['animate_style'] = wp_rotator_defaulter($options['animate_style'],'animate_style');
  $options['pane_width'] = wp_rotator_defaulter($options['pane_width'],'pane_width');
  $options['pane_height'] = wp_rotator_defaulter($options['pane_height'],'pane_height');

?>
  <form method="post" action="options.php">
  <?php settings_fields('wp_rotator_options');  ?>
  <h2>Rotator Settings</h2>
  <table class="form-table">
    <tr valign="top">
      <th scope="row">Posts Query Vars</th>
      <td>
        <input type="text" style="width: 700px;" name="wp_rotator_options[query_vars]" value="<?php echo $options['query_vars']; ?>" />
        <a target="_blank" href="http://codex.wordpress.org/Function_Reference/query_posts">Help</a>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">Animate Duration (ms)</th>
      <td><input type="text" style="width: 50px;" name="wp_rotator_options[animate_ms]" value="<?php echo $options['animate_ms']; ?>" /></td>
    </tr>
    <tr valign="top">
      <th scope="row">Remain Still Duration (ms)</th>
      <td><input type="text" style="width: 50px;" name="wp_rotator_options[rest_ms]" value="<?php echo $options['rest_ms']; ?>" /></td>
    </tr>
    <tr valign="top">
      <th scope="row">Animate Style</th>
      <td>
        Slide <input type="radio" name="wp_rotator_options[animate_style]" value="slide" <?php checked('slide',$options['animate_style']); ?> style="margin-right: 15px;" />
        Fade <input type="radio" name="wp_rotator_options[animate_style]" value="fade" <?php checked('fade',$options['animate_style']); ?> />
      </td>
    </tr>    
    <tr valign="top">
      <th scope="row">Pane width (pixels)</th>
      <td><input type="text"  style="width: 50px;" name="wp_rotator_options[pane_width]" value="<?php echo $options['pane_width']; ?>" /></td>
    </tr>
    <tr valign="top">
      <th scope="row">Pane height (pixels)</th>
      <td><input type="text"  style="width: 50px;" name="wp_rotator_options[pane_height]" value="<?php echo $options['pane_height']; ?>" /></td>
    </tr>    
   <tr valign="top"><td colspan="2"><strong>Note: if you change the image size you'll need to use <a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/">Regenerate Thumbnails</a> plugin for your old images to be resized.</strong></td></tr>  
   </table>  
    <div style="clear: both;">&nbsp;</div>
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
  </form>
<p><strong>For information on how to use and customize this plugin, please read the <a href="http://www.wprotator.com/documentation">documentation</a>.</strong></p>


  <h2>Preview</h2>
  <?php do_action('wp_rotator'); ?>
<?php
}




/* Enqueue JQuery */

function wp_rotator_add_jquery() {
  wp_enqueue_script('jquery');
}
add_action('init','wp_rotator_add_jquery');
add_action('admin_init','wp_rotator_add_jquery');



/* Default Javascript */

/***	Don't modify this. You can unhook it and use your own like this: 	*/
/***	remove_action('wp_head', 'wp_rotator_javascript'); 		*/
/***	remove_action('admin_head','wp_rotator_javascript'); 	*/
/*** 	add_action('wp_head', 'custom_rotator_javascript');		*/
/***	add_action('admin_head', 'custom_rotator_javascript');	*/


function wp_rotator_javascript() {
  global $bsd_pane_width;

?>
<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-content/plugins/wp-rotator/jquery.scrollTo.js"></script>
<script type="text/javascript">

  var WPROTATOR = {}; //namespace
  WPROTATOR.instance1 = false;
  WPROTATOR.elementsWidth = false; //global for debugging

  WPROTATOR.createRotator = function() {
    var that = {};
    that.init = function() {
      that.currentOffset = 0;
      that.slideDelay = <?php echo wp_rotator_option('animate_ms'); ?>;
      that.sliderAtRestDelay = <?php echo wp_rotator_option('rest_ms');?>;
      that.animateStyle = '<?php echo wp_rotator_option('animate_style'); ?>';
      that.candidates = jQuery('.featured-cell');
      that.autoPage = true;
      
      that.totalPages = that.candidates.length;

	  that.nexts = [];
      that.prevs = [];
      for (var i = 0; i < that.totalPages; i++) {
        that.nexts[i] = i + 1;
        that.prevs[i] = (i + that.totalPages - 1 ) % that.totalPages;
      }
      that.nexts[i-1] = 0;  
    }

    that.gotoPage = function(offset) {
      var newPage = that.pageJQ(offset);
      var oldPage = that.pageJQ(that.currentOffset);

      if (that.animateStyle == 'slide') {
        that.slideToPage(offset);
      }
      else {
        that.fadeToPage(offset);
      }

      oldPage.removeClass('current-cell');
      newPage.addClass('current-cell');

      jQuery('.pager-a li.current').removeClass('current');
      jQuery('.pager-a #pager-' + offset).addClass('current');
      
      that.currentOffset = offset;
    };


    that.pageJQ = function(i) {
      return jQuery(that.candidates[i]);
    };

    that.slideToPage = function(offset) {
      jQuery('.pane').scrollTo(that.candidates[offset],{axis: 'x',duration: that.slideDelay});    
    };

    that.fadeToPage = function(offset) {
        var newPage = that.pageJQ(offset);
        var oldPage = that.pageJQ(that.currentOffset);
        
        newPage.fadeTo(that.slideDelay/2,1,function(){
          oldPage.fadeTo(that.slideDelay/2,0);
        });
    };

  
    that.nexter = function(offset) {
      return that.nexts[that.currentOffset];    
    };
    
    that.prever = function(offset) {
      return that.prevs[that.currentOffset];
    }

    that.rotate = function() {
      if (that.autoPage) {
        that.gotoPage(that.nexter());
        setTimeout(function() { that.rotate(); },that.sliderAtRestDelay);    
      }
    }
    
    that.goNext = function() {
      that.autoPage = false;
      //// allow rightmost to rotate to leftmost when next hit //if (that.currentOffset == that.totalPages - 1) { return; }
      that.gotoPage(that.nexter());
    };

    that.goPrev = function() {
      that.autoPage = false;
      /// allow leftmost to rotate to rightmost when prev hit ////  if (that.currentOffset == 0) { return; }
      that.gotoPage(that.prever());
    };




    that.start = function() {
      setTimeout(function() { that.rotate(); },that.sliderAtRestDelay);    
    }

    that.init();
    
    return that;
  };

  jQuery(document).ready(function() {
    WPROTATOR.instance1 = WPROTATOR.createRotator();
    WPROTATOR.elementsWidth = <?php echo $bsd_pane_width; ?> * WPROTATOR.instance1.totalPages; 
    jQuery('.wp-rotator-wrap .elements').css('width',WPROTATOR.elementsWidth.toString() +'px');
    WPROTATOR.instance1.start();
    jQuery('.pager-a li').click(function() {
      var offset = this.id.replace('pager-','');
      WPROTATOR.instance1.autoPage = false;
      WPROTATOR.instance1.gotoPage(offset);
    });  
  });
</script>
<?php
}

add_action('wp_head','wp_rotator_javascript');
add_action('admin_head','wp_rotator_javascript');



/* Default CSS */

/***	Don't modify this. You can unhook it and use your own like this: 	*/
/***	remove_action('wp_head', 'wp_rotator_css'); 		*/
/***	remove_action('admin_head','wp_rotator_css'); 	*/
/*** 	add_action('wp_head', 'custom_rotator_css');		*/
/***	add_action('admin_head', 'custom_rotator_css');	*/


function wp_rotator_css() {
  global $bsd_pane_width;
  global $bsd_pane_height;
  $plugin_path_url = get_bloginfo('url') . '/wp-content/plugins/wp-rotator';
?>
<style type="text/css">

.wp-rotator-wrap {
  padding: 0; margin: 0;
}

.wp-rotator-wrap .pane {
  height: <?php echo $bsd_pane_height; ?>px;
  width: <?php echo $bsd_pane_width; ?>px;
  overflow: hidden;
  position: relative;
  padding: 0px;
  margin: 0px;
}

.wp-rotator-wrap .elements {
  height: <?php echo $bsd_pane_height; ?>px;
  padding: 0px;
  margin: 0px;
}

.wp-rotator-wrap .featured-cell {
  width: <?php echo $bsd_pane_width; ?>px;
  height: <?php echo $bsd_pane_height; ?>px;

  <?php if (wp_rotator_option('animate_style') == 'fade'): ?>
    display: block;
    position: absolute;
    top: 0;
    left: 0;

  <?php else: ?>
    display: inline;
    position: relative;
    float: left;
  <?php endif; ?>
  margin: 0px;
  padding: 0px;
}

.wp-rotator-wrap .featured-cell .image {
  position: absolute;
  top: 0;
  left: 0;
}

.wp-rotator-wrap .featured-cell .info {
  position: absolute;
  left: 0;
  bottom: 0px;
  width: <?php echo $bsd_pane_width; ?>px;
  height: 50px;
  padding: 8px 8px;
  overflow: hidden;
  background: url(<?php echo $plugin_path_url; ?>/feature-bg.png) transparent;
  color: #ddd;  
}

.wp-rotator-wrap .featured-cell .info h1 {
  margin: 0;
  padding: 0;
  font-size: 15px;
  color: #CCD;
}

.wp-rotator-wrap .current-cell { z-index: 500; }

</style>
<?php
}
add_action('wp_head','wp_rotator_css');
add_action('admin_head','wp_rotator_css');




/* Default Outer Markup */

/***	Don't modify this. You can unhook it and use your own like this: 	*/
/***	remove_action('wp_rotator','wp_rotator');		*/
/***	add_action('wp_rotator', 'custom_rotator');		*/
/***	function custom_rotator() {						*/
/***		echo custom_rotator_markup();				*/
/***	}												*/

/*** Note that [wp-rotator] shortcode also uses this so you'll need to rebuild that as well */
/***	remove_shortcode('wp_rotator');								*/
/*** 	add_shortcode('wp_rotator', 'custom_rotator_shortcode'); 	*/



function wp_rotator_markup() { 
  global $bsd_pane_width;
  global $bsd_pane_height;
  $animate_style = wp_rotator_option('animate_style');

  $result = '';
  $result .= '<div class="wp-rotator-wrap">';
  $result .= '  <div class="pane">';
  $result .= '    <ul class="elements" style="width: 5000px">';
        
        
  $featured = new WP_Query(wp_rotator_option('query_vars'));
  
  $inner = '';
  
  $first = true;
  while ($featured->have_posts()) : $featured->the_post(); 
    global $post; 
    
    if (apply_filters('wp_rotator_use_this_post',true)) {
      $inner .= apply_filters('wp_rotator_featured_cell_markup','');
    }
  endwhile;
    
  $result .= $inner;
  $result .= '      </ul><!-- elements -->';
  $result .= '  	</div><!-- #feature_box_rotator .pane -->';
  $result .= '  </div><!-- wp-rotator-wrap -->';
 
  return $result;
}

function wp_rotator() {
  echo wp_rotator_markup();
}

add_action('wp_rotator','wp_rotator');



/* Default Inner Markup */
/***	Don't modify this. You can unhook it and use your own like this: 	*/
/***	remove_filter('wp_rotator_featured_cell_markup','wp_rotator_featured_cell_markup');	*/
/*** 	add_filter('wp_rotator_featured_cell_markup','custom_featured_cell_markup'); */


function wp_rotator_featured_cell_markup($result) {

    global $post;
    $clickthrough_url = get_post_meta($post->ID,'url',true);
    $show_info = get_post_meta($post->ID,'show_info',true);
    if (empty($clickthrough_url)) {
      $clickthrough_url = get_permalink($post->ID);
    }
        
    $result .= '<li class="featured-cell"';
        if ($animate_style == 'fade') {
          if ($first) { 
            $first = false; 
          } 
          else { 
            $result .= 'style="display:none;"';
          } 
        }
    $result .= '>';
    $result .= '<a href="' . $clickthrough_url . '">';
    
    /* If you change the width/height in WP Rotator Settings but don't use Regenerate Thumbnails plugin, this will squish the image to the right dimensions rather than not changing the image. */
    
    $image =  wp_get_attachment_image_src( get_post_thumbnail_id(), 'wp_rotator' );
    global $bsd_pane_height, $bsd_pane_width;
	if ($image[1] == $bsd_pane_height && $image[2] == $bsd_pane_width)
		$result .= get_the_post_thumbnail( $post->ID, 'wp_rotator' );
	else $result .= '  <img width="' . $bsd_pane_width . '" height="' . $bsd_pane_height . '" src="' . $image[0] . '" />';

    $result .= '</a>';
    
    if ($show_info == true):
      $result .= '          <div class="info">';
      $result .= '          <h1>' . get_the_title() .'</h1>';
      $result .= '          <p>' . get_the_excerpt() . '</p>';
      $result .= '        </div>';
    endif;
    
    $result .= '</li><!-- featured-cell -->';
    return $result;
}
add_filter('wp_rotator_featured_cell_markup','wp_rotator_featured_cell_markup');



/* Fine Grained Control */
/*** Helpful if you need extra filtering beyond query_posts() */
/*** See @link http://www.wprotator.com for documentation **/

/*** 	Example: 		*/
/***	remove_filter('wp_rotator_use_this_post','wp_rotator_use_this_post'); 		*/
/*** 	add_filter('wp_rotator_use_this_post','custom_rotator_use_this_post');		*/
  
function wp_rotator_use_this_post($truthy) {
  global $post;
  return true;
}
add_filter('wp_rotator_use_this_post','wp_rotator_use_this_post');

?>