<?php
/** Front-end assets. Block assets are declared by each block.json. */

namespace Floe\Config\Assets;

defined( 'ABSPATH' ) || exit;

function enqueue(): void {
	wp_enqueue_style( 'floe', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_script( 'floe-media', get_template_directory_uri() . '/Blocks/_shared/Assets/media.js', array(), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );

function enqueue_block_styles(): void {
	wp_enqueue_style( 'floe-sections', get_template_directory_uri() . '/Blocks/_shared/Assets/sections.css', array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_block_styles' );
