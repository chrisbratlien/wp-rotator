<?php
/*
Plugin Name: WP Rotator
Plugin URI: http://www.wprotator.com
Description: Rotator for featured images or custom markup. Slide or crossfade. Posts chosen using query vars, just like query_posts() uses.
Version: 0.6
Author: Chris Bratlien, Bill Erickson
Author URI: http://www.wprotator.com/developers
*/

/* Translations */
load_plugin_textdomain( 'wp-rotator', false, basename( dirname( __FILE__ ) ) . '/languages' );


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


/* Initialize WP Rotator default options */
function wp_rotator_options_init() {
     // set options equal to defaults
     global $wp_rotator_options;
     $wp_rotator_options = get_option( 'wp_rotator_options' );
     if ( false === $wp_rotator_options ) {
          $wp_rotator_options = wp_rotator_default_array();
     }
     update_option( 'wp_rotator_options', $wp_rotator_options );
}
register_activation_hook(__FILE__, 'wp_rotator_options_init');


/* Helper Functions */
function wp_rotator_default($key) {
  $options = wp_rotator_default_array();
  if (isset($options[$key])) {
    return $options[$key];
  }
  else {
    return false;
  }
}

function wp_rotator_option($key) {
  $options = get_option('wp_rotator_options');
  
  // Escape everything except the query vars, which are sanitized on save
  if (!empty($options[$key])) { 
  	if( 'query_vars' === $key )
  		return $options[$key];
  	else
	    return esc_attr( $options[$key] ); 
  }
  else {
    return wp_rotator_default($key);
  }
}


/* Set up featured image size */
global $bsd_pane_height, $bsd_pane_width;
$wp_rotator_options = get_option('wp_rotator_options');
$bsd_pane_width = esc_attr( wp_rotator_option('pane_width') );
$bsd_pane_height = esc_attr( wp_rotator_option('pane_height') );

add_theme_support( 'post-thumbnails' );
add_image_size('wp_rotator', $bsd_pane_width, $bsd_pane_height, true);


/* [wp_rotator] Shortcode */
function wp_rotator_shortcode($atts, $content = null) {
  return wp_rotator_markup();
}
add_shortcode('wp_rotator', 'wp_rotator_shortcode');  


/* WP Rotator Widget */
include_once('rotator-widget.php');


/* WP Rotator Settings Page */
/*** Add the WP Rotator subpage to Settings ***/
function wp_rotator_menu_options() {
     add_submenu_page('options-general.php', __( 'WP Rotator', 'wp-rotator' ), __( 'WP Rotator', 'wp-rotator' ), 'edit_theme_options', 'wp-rotator-settings', 'wp_rotator_settings_page');
}
add_action('admin_menu', 'wp_rotator_menu_options');

/*** Markup that shows on WP Rotator settings page ***/
function wp_rotator_settings_page() {?>
	<div class="wrap">	
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e( 'WP Rotator', 'wp-rotator' );?></h2>
	<form action="options.php" method="post">
	<?php
	settings_fields('wp_rotator_options');
	do_settings_sections('wp_rotator');
	?>
	
	<p><input name="wp_rotator_options[submit-general]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'wp_rotator'); ?>" />
	
	<input name="wp_rotator_options[reset-general]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'wp_rotator'); ?>" /></p>


	<h3><?php _e( 'Preview' , 'wp-rotator' );?></h3>
	<?php do_action('wp_rotator');?>
	</form>
	</div>
<?php }

/*** Register fields and validate data ***/
add_action('admin_init', 'wp_rotator_register_settings');
function wp_rotator_register_settings() {
	register_setting( 'wp_rotator_options', 'wp_rotator_options', 'wp_rotator_options_validate' );
	add_settings_section('wp_rotator_options_general', __( 'Settings', 'wp-rotator' ), 'wp_rotator_options_general_header_text', 'wp_rotator');
	add_settings_field('wp_rotator_query_vars', __( 'Post Query Vars', 'wp-rotator' ), 'wp_rotator_query_vars', 'wp_rotator', 'wp_rotator_options_general');
	add_settings_field('wp_rotator_animate_ms', __( 'Animate Duration (ms)', 'wp-rotator' ), 'wp_rotator_animate_ms', 'wp_rotator', 'wp_rotator_options_general');
	add_settings_field('wp_rotator_rest_ms', __( 'Remain Still Duration (ms)', 'wp-rotator' ), 'wp_rotator_rest_ms', 'wp_rotator', 'wp_rotator_options_general');
	add_settings_field('wp_rotator_animate_style', __( 'Animate Style', 'wp-rotator' ), 'wp_rotator_animate_style', 'wp_rotator', 'wp_rotator_options_general');
	add_settings_field('wp_rotator_pane_width', __( 'Pane Width (pixels)', 'wp-rotator' ), 'wp_rotator_pane_width', 'wp_rotator', 'wp_rotator_options_general');
	add_settings_field('wp_rotator_pane_height', __( 'Pane Height (pixels)', 'wp-rotator' ), 'wp_rotator_pane_height', 'wp_rotator', 'wp_rotator_options_general');
	
	function wp_rotator_options_general_header_text() {
		echo sprintf( __( '<p><a target="_blank" href="%s">Please read the documentation</a> for information on how to use and customize this plugin.</p>', 'wp-rotator' ), 'http://www.wprotator.com/documentation' );
	}	
	function wp_rotator_query_vars() {
		$wp_rotator_options = get_option('wp_rotator_options');
	 	echo '<input type="text" value="'. $wp_rotator_options['query_vars'] .'" name="wp_rotator_options[query_vars]" style="width: 500px;"> <a href="http://codex.wordpress.org/Function_Reference/query_posts" target="_blank">' . __( 'Help', 'wp-rotator' ) . '</a>';
	}
	function wp_rotator_animate_ms() {
		$wp_rotator_options = get_option('wp_rotator_options');
		echo '<input type="text" value="'. esc_attr( $wp_rotator_options['animate_ms'] ) .'" name="wp_rotator_options[animate_ms]" class="normal_text" />';
	}
	function wp_rotator_rest_ms() {
		$wp_rotator_options = get_option('wp_rotator_options');
		echo '<input type="text" value="'.esc_attr( $wp_rotator_options['rest_ms'] ) .'" name="wp_rotator_options[rest_ms]" class="normal_text" />';
	}
	function wp_rotator_animate_style() {
		$wp_rotator_options = get_option('wp_rotator_options'); ?>
       <?php _e( 'Slide', 'wp-rotator' );?> <input type="radio" name="wp_rotator_options[animate_style]" value="slide" <?php checked('slide', esc_attr( $wp_rotator_options['animate_style'] ) ); ?> style="margin-right: 15px;" />
        <?php _e( 'Fade', 'wp-rotator' );?> <input type="radio" name="wp_rotator_options[animate_style]" value="fade" <?php checked('fade', esc_attr( $wp_rotator_options['animate_style'] ) ); ?> />
        <?php
	}
	function wp_rotator_pane_width() {
		$wp_rotator_options = get_option('wp_rotator_options');
		echo '<input type="text" value="'. esc_attr( $wp_rotator_options['pane_width'] ) .'" name="wp_rotator_options[pane_width]" class="normal_text" /> ' . sprintf( __( 'If you change this after uploading an image, use the <a href="%s" target="_blank">Regenerate Thumbnails</a> plugin to update images.', 'wp-rotator'), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' );
	}
	function wp_rotator_pane_height() {
		$wp_rotator_options = get_option('wp_rotator_options');
		echo '<input type="text" value="'. esc_attr( $wp_rotator_options['pane_height'] ) .'" name="wp_rotator_options[pane_height]" class="normal_text" /> ' . sprintf( __( 'If you change this after uploading an image, use the <a href="%s" target="_blank">Regenerate Thumbnails</a> plugin to update images.', 'wp-rotator' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' );
	}
	
	function wp_rotator_options_validate($input) {
		$wp_rotator_options = get_option('wp_rotator_options');
		$valid_input = $wp_rotator_options;
		
		$submit_general = ( ! empty( $input['submit-general']) ? true : false );
		$reset_general = ( ! empty($input['reset-general']) ? true : false );
		
		if($submit_general) {
			$valid_input['query_vars'] = strip_tags( $input['query_vars'] );
			$valid_input['animate_ms'] = (is_numeric($input['animate_ms']) ? $input['animate_ms'] : $valid_input['animate_ms']);
			$valid_input['rest_ms'] = (is_numeric($input['rest_ms']) ? $input['rest_ms'] : $valid_input['rest_ms']);
			$valid_input['animate_style'] = ('fade' == $input['animate_style'] ? 'fade' : 'slide');
			$valid_input['pane_width'] = (is_numeric($input['pane_width']) ? $input['pane_width'] : $valid_input['pane_width']);
			$valid_input['pane_height'] = (is_numeric($input['pane_height']) ? $input['pane_height'] : $valid_input['pane_height']);
		}elseif($reset_general) {
			$wp_rotator_default_options = wp_rotator_default_array();
			$valid_input['query_vars'] = $wp_rotator_default_options['query_vars'];
			$valid_input['animate_ms'] = $wp_rotator_default_options['animate_ms'];
			$valid_input['rest_ms'] = $wp_rotator_default_options['rest_ms'];
			$valid_input['animate_style'] = $wp_rotator_default_options['animate_style'];
			$valid_input['pane_width'] = $wp_rotator_default_options['pane_width'];
			$valid_input['pane_height'] = $wp_rotator_default_options['pane_height'];
		}		
		return $valid_input;
	}		
}


/* Enqueue JQuery and ScrollTo */
function wp_rotator_add_jquery() {
  wp_enqueue_script('jquery');
  wp_register_script('scrollTo', get_bloginfo('url').'/wp-content/plugins/wp-rotator/jquery.scrollTo-1.4.2-min.js', array('jquery'), '1.4.2');
  wp_enqueue_script('scrollTo');
}
add_action('init','wp_rotator_add_jquery');
add_action('admin_init','wp_rotator_add_jquery');


/* Default Javascript */
/***	Don't modify this. You can unhook it and use your own like this: 	*/
/***	remove_action('wp_head', 'wp_rotator_javascript'); 		*/
/***	remove_action('admin_head','wp_rotator_javascript'); 	*/
/*** 	add_action('wp_head', 'custom_rotator_javascript');		*/
/***	add_action('admin_head', 'custom_rotator_javascript');	*/
function wp_rotator_javascript() { ?>
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
    WPROTATOR.elementsWidth = <?php echo wp_rotator_option('pane_width'); ?> * WPROTATOR.instance1.totalPages; 
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
  background: url(<?php echo plugins_url( 'feature-bg.png', __FILE__ );?> ) transparent;
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
  global $bsd_pane_width, $bsd_pane_height, $animate_style, $first;
  $animate_style = esc_attr( wp_rotator_option('animate_style') );
  $result = '';
  $result .= '<div class="wp-rotator-wrap">';
  $result .= '  <div class="pane">';
  $result .= '    <ul class="elements" style="width: 5000px">';
  $featured = new WP_Query( wp_rotator_option('query_vars') );
  $inner = '';
  $first = true;
  while ($featured->have_posts()) : $featured->the_post(); 
    global $post; 
    
    if (apply_filters('wp_rotator_use_this_post',true)) {
      $inner .= apply_filters('wp_rotator_featured_cell_markup','');
    }
  endwhile;
  wp_reset_query(); // IMPORTANT so that main Loop $post var isn't disturbed
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
    global $post, $animate_style, $first;
    $clickthrough_url = esc_url (get_post_meta($post->ID, 'wp_rotator_url', true));
    $show_info = esc_attr(get_post_meta($post->ID, 'wp_rotator_show_info', true));
    /* Backwards compatible with old version, where we didn't prefix the field */
    if (empty($clickthrough_url)) { $clickthrough_url = esc_url(get_post_meta($post->ID,'url',true)); }
    if (empty($show_info)) { $show_info = esc_attr(get_post_meta($post->ID,'show_info',true)); }
    /* */
    if (!isset($clickthrough_url)) {
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
      if (get_the_excerpt()) $result .= '          <p>' . get_the_excerpt() . '</p>';
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
