<?php
/**
 * Eyebrow component: accent dot plus mono uppercase label. The tone follows
 * the surface automatically (Default, Inverse on dark, all white on Accent).
 *
 *   Floe\component( 'eyebrow', array( 'text' => 'Latest posts' ) )
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function eyebrow( array $args ): string {
	$text = (string) ( $args['text'] ?? '' );
	if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
		return '';
	}
	$class = trim( 'eyebrow ' . ( $args['class'] ?? '' ) );
	return '<p class="' . esc_attr( $class ) . '"><span class="eyebrow__dot" aria-hidden="true"></span><span class="eyebrow__text">' . wp_kses( $text, array( 'br' => array() ) ) . '</span></p>';
}
