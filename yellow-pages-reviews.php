<?php
/*
Plugin Name: Yellow Pages Reviews
Plugin URI: http://wordiempress.com/plugins/yellow-pages-reviews-pro/
Description: Display Yellow Pages reviews for your business using an easy to use and intuitive widget. <strong>Upgrade to <a href="https://wordimpress.com/plugins/yellow-pages-reviews-pro/" target="_blank" title="Upgrade to Yellow Pages Reviews Pro">Yellow Pages Reviews Pro</a> for more reviews and customization options.</strong> If you enjoy the plugin, please consider rating it on WordPress.
Version: 1.0.0
Author: Devin Walker
Author URI: http://imdev.in/
Text Domain: ypr
*/

define( 'YPR_PLUGIN_NAME', 'yellow-pages-reviews' );
define( 'YPR_PLUGIN_NAME_PLUGIN', plugin_basename( __FILE__ ) );
define( 'YPR_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'YPR_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
define( 'YPR_DEBUG', false );

function init_yellow_pages_reviews_widget() {

	// Include Core Framework class
	require_once 'classes/core.php';

	//	global $yellow_pages_reviews;
	// Create plugin instance
	$yellow_pages_reviews = new YPR_Plugin_Framework( __FILE__ );

	// Include options set
	include_once 'inc/options.php';

	// Create options page
	$yellow_pages_reviews->add_options_page( array(), $yellow_pages_reviews_options );

	// Make plugin meta translatable
	__( 'Yellow Pages Reviews', $yellow_pages_reviews->textdomain );
	__( 'Devin Walker', $yellow_pages_reviews->textdomain );
	__( 'ypr', $yellow_pages_reviews->textdomain );

	//Include the widget
	if ( ! class_exists( 'Yellow_Pages_Reviews' ) ) {
		require 'classes/widget.php';
	}

	return $yellow_pages_reviews;

}

/*
 * @DESC: Register Open Table widget
 */
add_action( 'widgets_init', 'init_yellow_pages_reviews_widget' );
add_action( 'widgets_init', create_function( '', 'register_widget( "Yellow_Pages_Reviews" );' ) );


/**
 * Custom CSS for Options Page
 */
add_action( 'admin_enqueue_scripts', 'ypr_options_scripts' );

function ypr_options_scripts( $hook ) {
	$suffix = defined( 'YPR_DEBUG' ) && YPR_DEBUG ? '' : '.min';

	if ( $hook === 'settings_page_yellowpagesreviews' ) {
		wp_register_style( 'ypr_custom_options_styles', plugin_dir_url( __FILE__ ) . 'assets/css/options' . $suffix . '.css' );
		wp_enqueue_style( 'ypr_custom_options_styles' );

	}
}