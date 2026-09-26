<?php
/**
 * Theme toggle: a round button that switches the site between light and dark.
 * Until a visitor chooses, the site follows their device; the choice is then
 * remembered in localStorage ("floe-theme").
 *
 *   echo Floe\component( 'theme-toggle' );
 *
 * The theme is set on <html data-theme> by a tiny inline script at the top of
 * <head>, so the page never paints in the wrong colours.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function theme_toggle( array $args = array() ): string {
	wp_enqueue_script( 'floe-theme-toggle' );

	$class = trim( 'theme-toggle ' . ( $args['class'] ?? '' ) );
	$mask  = wp_unique_id( 'theme-toggle-mask-' );

	return sprintf(
		'<button type="button" class="%1$s" aria-pressed="false" title="%2$s"><span class="screen-reader-text">%2$s</span>'
		. '<svg class="theme-toggle__icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">'
		. '<mask id="%3$s"><rect width="24" height="24" fill="#fff"/><circle class="theme-toggle__cutout" cx="21" cy="7" r="6" fill="#000"/></mask>'
		. '<g mask="url(#%3$s)"><circle class="theme-toggle__body" cx="12" cy="12" r="5" fill="currentColor"/></g>'
		. '<g class="theme-toggle__rays" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">'
		. '<path d="M12 1.75v2M12 20.25v2M1.75 12h2M20.25 12h2M4.75 4.75l1.4 1.4M17.85 17.85l1.4 1.4M4.75 19.25l1.4-1.4M17.85 6.15l1.4-1.4"/>'
		. '</g></svg></button>',
		esc_attr( $class ),
		esc_attr__( 'Dark mode', 'floe' ),
		esc_attr( $mask )
	);
}

/** Sets data-theme before first paint: the saved choice, else the device's. */
function theme_toggle_head(): void {
	if ( is_admin() ) {
		return;
	}
	$script = "(function(){var t;try{t=localStorage.getItem('floe-theme')}catch(e){}"
		. "if(t!=='light'&&t!=='dark'){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'}"
		. 'document.documentElement.dataset.theme=t})();';
	wp_print_inline_script_tag( $script );
}
add_action( 'wp_head', __NAMESPACE__ . '\\theme_toggle_head', 1 );
