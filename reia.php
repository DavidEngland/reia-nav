<?php
/*
Plugin Name: REIA Nav Walker
Plugin URI: http://www.RealEstate-Huntsville.com
Description:  Adds widget for menu display with BootStrap styling. 
Version: 1.0
Author: David England
Author URI: http://about.me/DavidEngland
Author Email: DavidEngland@hotmail.com
License:

Copyright 2014 Real Estate Intelligence Agency, Inc. (mikko@realestate-huntsville.com)

This program is free software;
you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY;
without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program;
if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
if ( !class_exists(wp_bootstrap_navwalker ) ) {
	include('inc/wp_bootstrap_navwalker.php');
}

if (!class_exists("REIA_Nav_Widget")) {

add_action('widgets_init', 'load_reia_nav_widget');

	function load_reia_nav_widget() {
		register_widget( 'REIA_Nav_Widget' );
	}

	class REIA_Nav_Widget extends WP_Widget {

		function __construct() {

		load_plugin_textdomain( 'reia-nav-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
			$widget_ops = array( 'classname' => 'reia-nav-widget', 'description' => __('Custom menu with Bootstrap styling.', 'reia-nav-widget') );
			$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'reia-nav-widget' );
			$this->WP_Widget( 'reia-nav-widget', __('REIA Nav', 'reia-nav-widget'), $widget_ops, $control_ops );	
		}

		function widget($args, $instance) {
			
			$nav_menu = wp_get_nav_menu_object( $instance['nav_menu'] ); /* Get menu */
	
			if ( !$nav_menu )
				return;
	
			$instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
	
			echo $args['before_widget'];
	
			if ( !empty($instance['title']) && !empty($instance['title_url']) )
				echo $args['before_title'] . '<a href="' . $instance['title_url'] . '">' . $instance['title'] . '</a>' . $args['after_title'];
	
			if ( !empty($instance['title']) && empty($instance['title_url']) )
				echo $args['before_title'] . $instance['title'] . $args['after_title'];
	
			//wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'menu_class' => $instance['menu_class'], 'container' => false ) );
			 wp_nav_menu( array(
						'menu'		 => $nav_menu,
						'depth'		 => 2,
						'container'	 => false,
						'menu_class' => $instance['menu_class'],
						'fallback_cb' => 'wp_page_menu',
		//Process nav menu using our custom nav walker
						'walker' => new wp_bootstrap_navwalker())
						);
	
			echo $args['after_widget'];
		}

		// widget admin
			
		function update( $new_instance, $old_instance ) {
			$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];
			$instance['title_url'] = $new_instance['title_url'];
			$instance['menu_class'] = $new_instance['menu_class'];
			return $instance;
		}
	
		function form( $instance ) {
			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
			$title_url = isset( $instance['title_url'] ) ? $instance['title_url'] : '';
			$menu_class = isset( $instance['menu_class'] ) ? $instance['menu_class'] : 'sub-menu';
	
			// Get menus
			$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
	
			// If no menus exists, direct the user to create some.
			if ( !$menus ) {
				echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.', 'reia-nav-widget'), admin_url('nav-menus.php') ) .'</p>';
				return;
			}
			?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'reia-nav-widget') ?></label><input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" /></p>
			<p><label for="<?php echo $this->get_field_id('title_url'); ?>"><?php _e('Title URL:', 'reia-nav-widget') ?></label><input type="text" class="widefat" id="<?php echo $this->get_field_id('title_url'); ?>" name="<?php echo $this->get_field_name('title_url'); ?>" value="<?php echo $title_url; ?>" /></p>
			<p><label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:', 'reia-nav-widget'); ?></label>
				<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
			<?php 
				foreach ( $menus as $menu ) {
					$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
					echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
				}
			?>
				</select></p>
			<p><label for="<?php echo $this->get_field_id('menu_class'); ?>"><?php _e('Menu Class:', 'reia-nav-widget') ?></label><input type="text" class="widefat" id="<?php echo $this->get_field_id('menu_class'); ?>" name="<?php echo $this->get_field_name('menu_class'); ?>" value="<?php echo $menu_class; ?>" />
				<small><?php _e( 'CSS class to use for the ul menu element.', 'reia-nav-widget' ); ?></small></p>
				<p class="credits"><small><?php _e('Developed by', 'reia-nav-widget'); ?> <a href="http://about.me/DavidEngland" rel="nofollow">David England</a></small></p>
			<?php
		}
	}// end class
	
} // end if
?>
