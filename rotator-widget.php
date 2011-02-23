<?php
/* WP Rotator Widget */
/** Add our function to the widgets_init hook. **/
add_action( 'widgets_init', 'wp_rotator_load_widgets' );
function wp_rotator_load_widgets() {
	register_widget( 'WP_Rotator_Widget' );
}

/** Define the Widget as an extension of WP_Widget **/
class WP_Rotator_Widget extends WP_Widget {
	function WP_Rotator_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_rotator', 'description' => 'Displays the WP Rotator. Go to Settings > WP Rotator to configure.' );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'rotator-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'rotator-widget', 'WP Rotator', $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );

		echo $before_widget;
		do_action('wp_rotator');
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Rotator' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<?php
	}


}


?>