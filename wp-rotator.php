<?php
/**
 * @package WP_Rotator
 * @version 0.1
 */
/*
Plugin Name: WP Rotator
Plugin URI: http://chrisbratlien.com/wp-rotator
Description: Rotator for featured images. Control which posts to rotate with Query Vars setting. Slide or cross-fade animation style. Control animate/rest delay times. Toggle info box and override clicktrough URLs at a per-post level. Override the CSS in your theme's functions.php file.
Author: Chris Bratlien, Bill Erickson
Author URI: http://chrisbratlien.com/wp-rotator
*/

add_theme_support( 'post-thumbnails' );

global $bsd_pane_height;
global $bsd_pane_width;

$bsd_pane_width = wp_rotator_defaulter($bsd_pane_width,'pane_width');
$bsd_pane_height = wp_rotator_defaulter($bsd_pane_height,'pane_height');

if (!function_exists('pp')) {
  function pp($obj,$label = '') {  
    $data = json_encode(print_r($obj,true));
    ?>
    <script type="text/javascript">
      var obj = <?php echo $data; ?>;
      var logger = document.getElementById('bsdLogger');
      if (!logger) {
        logger = document.createElement('div');
        logger.id = 'bsdLogger';
        document.body.appendChild(logger);
      }
      ////console.log(obj);
      var pre = document.createElement('pre');
      var h2 = document.createElement('h2');
      pre.innerHTML = obj;
      
      h2.innerHTML = '<?php echo addslashes($label); ?>';
      logger.appendChild(h2);
      logger.appendChild(pre);
    </script>
    <?php
  }
}


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
  <h4>Rotator Settings</h4>
  <table class="form-table">
    <tr valign="top">
      <th scope="row">Posts Query Vars</th>
      <td><input type="text" style="width: 700px;" name="wp_rotator_options[query_vars]" value="<?php echo $options['query_vars']; ?>" /></td>
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
    <!--
    <tr valign="top">
      <th scope="row">Show Info Box</th>
      <td><input type="checkbox" name="wp_rotator_options[show_info_box]" <?php ///checked('on',$options['show_info_box']); ?> /></td>
    </tr>    
    -->
    <tr valign="top">
      <th scope="row">Pane width (pixels)</th>
      <td><input type="text"  style="width: 50px;" name="wp_rotator_options[pane_width]" value="<?php echo $options['pane_width']; ?>" /></td>
    </tr>
    <tr valign="top">
      <th scope="row">Pane height (pixels)</th>
      <td><input type="text"  style="width: 50px;" name="wp_rotator_options[pane_height]" value="<?php echo $options['pane_height']; ?>" /></td>
    </tr>    
  </table>  
    <div style="clear: both;">&nbsp;</div>
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
  </form>

  <h4>Per-Post Custom Fields</h4>
  <table class="form-table">
    <tr valign="top">
      <th>Name</th>
      <th scope="row">Custom Field</th>
      <th>Value</th>
      <th>Description</th>
    </tr>    
    <tr valign="top">
      <td>Show Info Box</td>
      <td>show_info</td>
      <td>1</td>
      <td>This will show an info box overlaying the lower portion of the featured image. Inside the box will be the post title and excerpt</td>
    </tr>
    <tr valign="top">
      <td>Clickthrough URL</td>
      <td>url</td>
      <td>http://whatever/url/you/want</td>
      <td>Clicking the featured image will send the visitor to this URL</td>
    </tr>
  </table>

<h4>Preview</h4>
<?php do_action('wp_rotator'); ?>
<?php
}

function wp_rotator_add_jquery() {
  wp_enqueue_script('jquery');
}
add_action('init','wp_rotator_add_jquery');
add_action('admin_init','wp_rotator_add_jquery');


function wp_rotator_javascript() {
  global $bsd_pane_width;

?>
<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-content/plugins/wp-rotator/jquery.scrollTo.js"></script>
<script type="text/javascript">

  var r = false; // global rotator object (for debugging)
  var elementsWidth = false; //global for debugging

  var rotator = function() {
    var that = {};
    that.init = function() {
      that.currentOffset = 0;
      that.slideDelay = <?php echo wp_rotator_option('animate_ms'); ?>;
      that.sliderAtRestDelay = <?php echo wp_rotator_option('rest_ms');?>;
      that.animateStyle = '<?php echo wp_rotator_option('animate_style'); ?>';
      that.candidates = jQuery('.featured-cell');
      that.autoPage = true;
      

      //that.candidates = [];
      //jQuery('.featured-cell img').each(function(i,e){
      //  that.candidates.push(jQuery(e).attr('src'));
      //});
      
      /////console.log(that.candidates);
      
      that.totalPages = that.candidates.length;
      
      that.nexts = [];
      for (var i = 0; i < that.totalPages; i++) {
        that.nexts[i] = i + 1;
      }
      that.nexts[i-1] = 0;
    }



    that.gotoPage = function(offset) {
      if (that.animateStyle == 'slide') {
        that.slideToPage(offset);
      }
      else {
        that.fadeToPage(offset);
      }
    };


    that.slideToPage = function(offset) {
      jQuery('.pane').scrollTo(that.candidates[offset],{axis: 'x',duration: that.slideDelay});    
      that.currentOffset = offset;
    };



    that.fadeToPage = function(offset) {
      //jQuery('.pane').fadeOut(that.slideDelay/2, function() {
        var newPage = jQuery(that.candidates[offset]);
        var oldPage = jQuery(that.candidates[that.currentOffset]);


        //////jQuery('.featured-cell').hide();
        
        newPage.fadeTo(that.slideDelay/2,1,function(){
          oldPage.fadeTo(that.slideDelay/2,0);
        });

        /*
        newPage.fadeTo(1000,0.5,function() {
          oldPage.fadeTo(1000,0.5,function(){
            newPage.fadeTo(1000,1,function(){
              oldPage.fadeTo(1000,0);
            });
          });
        });
        */
        
        
        //jQuery('.pane').fadeIn(that.slideDelay/2);
      
        jQuery('.pager-a li.current').removeClass('current');
        jQuery('.pager-a #pager-' + offset).addClass('current');
			//});

      that.currentOffset = offset;
    };

    that.fadeToPageV1 = function(offset) {
      jQuery('.pane').fadeOut(that.slideDelay/2, function() {
        var newPage = jQuery(that.candidates[offset]);

        jQuery('.featured-cell').hide();
        jQuery(that.candidates[offset]).show();
        jQuery('.pane').fadeIn(that.slideDelay/2);
      
        jQuery('.pager-a li.current').removeClass('current');
        jQuery('.pager-a #pager-' + offset).addClass('current');
			});

      that.currentOffset = offset;
    };

  
    that.nexter = function(offset) {
      return that.nexts[that.currentOffset];    
    };

    that.rotate = function() {
      if (that.autoPage) {
        that.gotoPage(that.nexter());
        setTimeout(function() { that.rotate(); },that.sliderAtRestDelay);    
      }
    }
    
    that.start = function() {
      setTimeout(function() { that.rotate(); },that.sliderAtRestDelay);    
    }

    that.init();
    
    return that;
  };

  jQuery(document).ready(function() {

    r = rotator(); //global for debugging purposes (no 'var').
    
    elementsWidth = <?php echo $bsd_pane_width; ?> * r.totalPages; 
    
    jQuery('.wp-rotator-wrap .elements').css('width',elementsWidth.toString() +'px');

    r.start();
    
    jQuery('.pager-a li').click(function() {
      var offset = this.id.replace('pager-','');
      r.autoPage = false;
      r.gotoPage(offset);
    });
    
  });

</script>
<?php
}

add_action('wp_head','wp_rotator_javascript');
add_action('admin_head','wp_rotator_javascript');


function wp_rotator_css() {
  global $bsd_pane_width;
  global $bsd_pane_height;
  $plugin_path_url = get_bloginfo('url') . '/wp-content/plugins/wp-rotator';
?>
<style type="text/css">
/****
WARNING: Don't modify the CSS here, because a plugin update could wipe them out.

Instead, override this hook in your functions.php like so:

//public-facing CSS
remove_action('wp_head','wp_rotator_css');
add_action('wp_head','custom_wp_rotator_css');

//admin-preview CSS
remove_action('admin_head','wp_rotator_css');
add_action('admin_head','custom_wp_rotator_css');

**/
.wp-rotator-wrap {
  padding: 25px; margin-left: -7px;
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
  /* width: 9000px; */
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
  <?php endif; ?>
  /***
   border: 1px solid red;
   background: green;
  ***/
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

.wp-rotator-wrap .featured-cell a {

}

.wp-rotator-wrap .featured-cell .info p {
  
}


#bsdLogger {
  position: absolute;
  top: 0px;
  right: 0px;
  border: 2px solid #bbb;
  padding: 3px;
  background: white;
  color: #444;
  z-index: 999;
  font-size: 1.25em;
  width: 400px;
  height: 800px;
  overflow: scroll;
}


</style>
<?php
}
add_action('wp_head','wp_rotator_css');
add_action('admin_head','wp_rotator_css');

function wp_rotator() { 
  global $bsd_pane_width;
  global $bsd_pane_height;
  $animate_style = wp_rotator_option('animate_style');
?>
  <div class="wp-rotator-wrap">
  	<div class="pane">
      <ul class="elements">
        <?php
        
          /////$foo = get_posts(wp_rotator_option('query_vars'));
          
          ////pp($foo);
        
        
          $featured = new WP_Query(wp_rotator_option('query_vars'));
          ///pp($featured);
          
          
          
          $first = true;
          while ($featured->have_posts()) : $featured->the_post(); 
            global $post; 
            //pp($post);
            $clickthrough_url = get_post_meta($post->ID,'url',true);
            $show_info = get_post_meta($post->ID,'show_info',true);
            if (empty($clickthrough_url)) {
              $clickthrough_url = get_permalink($post->ID);
            }
            
            $foo = get_posts('post_type=attachment&post_parent=' . $post->ID);
            $image_url = $foo[0]->guid;
            ///////pp($post_image);
            
            ?>
            <li class="featured-cell" 
              <?php 
                if ($animate_style == 'fade') {
                  if ($first) { 
                    $first = false; 
                  } 
                  else { 
                    echo 'style="display:none;"';
                  } 
                }
              ?>>
                    <a href="<?php echo $clickthrough_url; ?>">
                      <img width="<?php echo $bsd_pane_width; ?>" height="<?php echo $bsd_pane_height; ?>" src="<?php echo $image_url; ?>" />
                    </a>
                    <?php if ($show_info == true): ?>
                      <div class="info">
                        <h1><?php the_title(); ?></h1>
                        <p><?php the_excerpt();?></p>
                      </div>
                    <?php endif; ?>
            </li><!-- featured-cell -->
        <?php endwhile; ?>
        <?php wp_reset_query(); ?>
      </ul><!-- elements -->
  	</div><!-- #feature_box_rotator .pane -->
  </div><!-- wp-rotator-wrap -->
<?php 
}
add_action('wp_rotator','wp_rotator');

update_option('wp_rotator_options',false);

?>