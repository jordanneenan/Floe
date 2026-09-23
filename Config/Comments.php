<?php
/** Preserve Made's site-wide choice to disable comments and pingbacks. */

namespace Floe\Config\Comments;

defined( 'ABSPATH' ) || exit;

function remove_support(): void {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', __NAMESPACE__ . '\\remove_support', 100 );

add_filter( 'comments_open', '__return_false' );
add_filter( 'pings_open', '__return_false' );
add_filter( 'comments_array', '__return_empty_array' );
