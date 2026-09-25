<?php
/**
 * Admin branding, following Made: no WordPress logo, a Floe footer credit,
 * "Howdy" removed and a simpler login label.
 */

namespace Floe\Config\Admin\Branding;

defined( 'ABSPATH' ) || exit;

function admin_bar( \WP_Admin_Bar $bar ): void {
	$bar->remove_node( 'wp-logo' );

	$account = $bar->get_node( 'my-account' );
	if ( $account && is_string( $account->title ) ) {
		$bar->add_node(
			array(
				'id'    => 'my-account',
				'title' => preg_replace( '/^.*?(<span class="display-name">)/s', '$1', $account->title ),
			)
		);
	}
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar', 999 );

function footer_text(): string {
	return esc_html__( 'Built with Floe.', 'floe' );
}
add_filter( 'admin_footer_text', __NAMESPACE__ . '\\footer_text' );

function login_styles(): void {
	echo '<style>.login h1 a{display:none!important}</style>';
}
add_action( 'login_head', __NAMESPACE__ . '\\login_styles' );

function login_label( string $translation, string $text ): string {
	return 'Username or Email Address' === $text ? __( 'Email', 'floe' ) : $translation;
}

function login_labels(): void {
	add_filter( 'gettext', __NAMESPACE__ . '\\login_label', 10, 2 );
}
add_action( 'login_init', __NAMESPACE__ . '\\login_labels' );
