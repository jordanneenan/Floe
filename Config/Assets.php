<?php
/** Front-end assets. Block assets are declared by each block.json. */

namespace Floe\Config\Assets;

defined( 'ABSPATH' ) || exit;

function enqueue(): void {
	wp_enqueue_style( 'floe', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue' );
