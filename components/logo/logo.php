<?php
/**
 * Logo component: the site's custom logo (Appearance → Customize → Site
 * Identity), or the Floe logo in currentColor, linked to the home page.
 *
 *   Floe\component( 'logo', array( 'class' => 'site-header__logo' ) )
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function logo( array $args = array() ): string {
	$class = trim( 'logo ' . ( $args['class'] ?? '' ) );
	$name  = get_bloginfo( 'name' );

	if ( has_custom_logo() ) {
		return '<div class="' . esc_attr( $class ) . '">' . get_custom_logo() . '</div>';
	}

	$svg = file_get_contents( __DIR__ . '/floe-logo.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	return sprintf(
		'<div class="%1$s"><a class="logo__link" href="%2$s" rel="home" aria-label="%3$s">%4$s</a></div>',
		esc_attr( $class ),
		esc_url( home_url( '/' ) ),
		/* translators: %s: site name. */
		esc_attr( sprintf( __( '%s home', 'floe' ), $name ) ),
		$svg
	);
}
