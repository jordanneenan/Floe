<?php
/**
 * Front-end housekeeping, following Made: no emoji scripts, and the admin bar
 * sits in the page flow instead of pushing the layout down.
 */

namespace Floe\Config\Admin\Frontend;

defined( 'ABSPATH' ) || exit;

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

function admin_bar_in_flow(): void {
	add_theme_support( 'admin-bar', array( 'callback' => '__return_false' ) );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\admin_bar_in_flow' );

function admin_bar_styles(): void {
	if ( is_admin_bar_showing() ) {
		wp_add_inline_style( 'floe-base', '#wpadminbar{position:relative!important}' );
	}
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\admin_bar_styles', 20 );
