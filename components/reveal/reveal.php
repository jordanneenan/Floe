<?php
/**
 * Reveal: content fades gently up into place as it scrolls into view. Loaded
 * on every front-end page; it has no markup of its own.
 *
 * reveal.js picks the targets itself (see there), and only ever hides things
 * that are still below the fold, so the first screen is never held back and
 * nothing is hidden without JavaScript. Reduced motion turns it off.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function reveal( array $args = array() ): string {
	return '';
}

function reveal_enqueue(): void {
	wp_enqueue_script( 'floe-reveal' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\reveal_enqueue' );
