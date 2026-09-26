<?php
/**
 * Global assets. Block and component assets are declared by each module and
 * load only where they are used.
 */

namespace Floe\Includes\Assets;

defined( 'ABSPATH' ) || exit;

function version( string $relative_path ): string {
	$file = get_template_directory() . '/' . $relative_path;
	return is_file( $file ) ? (string) filemtime( $file ) : wp_get_theme()->get( 'Version' );
}

function enqueue(): void {
	wp_enqueue_style( 'floe-base', get_template_directory_uri() . '/assets/css/base.css', array(), version( 'assets/css/base.css' ) );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );

// Load each block's CSS only on pages that use the block (classic themes opt in).
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_filter( 'should_load_block_assets_on_demand', '__return_true' );
