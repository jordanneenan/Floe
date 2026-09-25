<?php
/**
 * Comments and pingbacks are switched off site-wide (following Made).
 * Delete this file to allow comments again.
 */

namespace Floe\Config\Admin\Comments;

defined( 'ABSPATH' ) || exit;

function remove_support(): void {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', __NAMESPACE__ . '\\remove_support', 100 );

add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

function remove_pingback_header( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', __NAMESPACE__ . '\\remove_pingback_header' );

function admin_screens(): void {
	global $pagenow;

	if ( in_array( $pagenow, array( 'edit-comments.php', 'comment.php' ), true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'admin_init', __NAMESPACE__ . '\\admin_screens' );

function remove_menu(): void {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu' );

function remove_admin_bar_node( \WP_Admin_Bar $bar ): void {
	$bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\\remove_admin_bar_node', 999 );
