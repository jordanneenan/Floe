<?php
/** Admin interface changes, kept separate from theme and content behavior. */

namespace Floe\Config\AdminUI;

defined( 'ABSPATH' ) || exit;

function remove_menu_items(): void {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu_items' );

function tidy_admin_bar( \WP_Admin_Bar $bar ): void {
	$bar->remove_node( 'comments' );
	$bar->remove_node( 'wp-logo' );
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\\tidy_admin_bar', 999 );

function footer_text(): string {
	return esc_html__( 'Built with Floe.', 'floe' );
}
add_filter( 'admin_footer_text', __NAMESPACE__ . '\\footer_text' );
