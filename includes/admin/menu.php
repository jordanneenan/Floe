<?php
/**
 * Admin menu, following Made: Dashboard hidden, content first. Appearance and
 * Customize are never hidden. Also turns off the periodic admin-email check.
 */

namespace Floe\Includes\Admin\Menu;

defined( 'ABSPATH' ) || exit;

function remove_items(): void {
	remove_menu_page( 'index.php' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_items', 999 );

function order( $menu_order ) {
	if ( ! $menu_order ) {
		return true;
	}
	return array(
		'edit.php?post_type=page',
		'edit.php',
		'upload.php',
		'plugins.php',
		'users.php',
		'options-general.php',
	);
}
add_filter( 'custom_menu_order', __NAMESPACE__ . '\\order' );
add_filter( 'menu_order', __NAMESPACE__ . '\\order' );

// With Dashboard hidden, send people to Pages instead of an empty dashboard.
function landing_url(): string {
	return current_user_can( 'edit_pages' ) ? admin_url( 'edit.php?post_type=page' ) : admin_url( 'profile.php' );
}

function redirect_dashboard(): void {
	global $pagenow;
	if ( 'index.php' === $pagenow && empty( $_GET['page'] ) && ! wp_doing_ajax() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( landing_url() );
		exit;
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\\redirect_dashboard' );

function login_redirect( string $redirect_to, string $requested ): string {
	return ( '' === $requested || admin_url() === $redirect_to ) ? landing_url() : $redirect_to;
}
add_filter( 'login_redirect', __NAMESPACE__ . '\\login_redirect', 10, 2 );

add_filter( 'admin_email_check_interval', '__return_false' );
