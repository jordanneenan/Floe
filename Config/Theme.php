<?php
/** Theme setup and native WordPress features. */

namespace Floe\Config\Theme;

defined( 'ABSPATH' ) || exit;

function setup(): void {
	load_theme_textdomain( 'floe', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 192,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'Assets/css/base.css', 'Assets/css/editor.css' ) );

	// Floe ships its own patterns; core's generic ones don't fit the system (Made does the same).
	remove_theme_support( 'core-block-patterns' );

	register_nav_menus(
		array(
			'primary'  => __( 'Header menu', 'floe' ),
			'action'   => __( 'Header and footer button', 'floe' ),
			'footer-1' => __( 'Footer menu 1', 'floe' ),
			'footer-2' => __( 'Footer menu 2', 'floe' ),
			'footer-3' => __( 'Footer menu 3', 'floe' ),
		)
	);
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup' );

// Don't pull remote patterns from the WordPress.org directory.
add_filter( 'should_load_remote_block_patterns', '__return_false' );
