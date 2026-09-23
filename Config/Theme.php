<?php
/** Theme setup and native WordPress features. */

namespace Floe\Config\Theme;

defined( 'ABSPATH' ) || exit;

function setup(): void {
	load_theme_textdomain( 'floe', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	register_nav_menus(
		array( 'primary' => __( 'Primary menu', 'floe' ) )
	);
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup' );
