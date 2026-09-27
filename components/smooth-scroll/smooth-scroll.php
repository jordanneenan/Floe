<?php
/**
 * Smooth scroll: in-page links glide to their section, and a page opened with
 * an anchor in its URL (floewp.com/about/#team) loads at the top and then
 * glides down to it. Loaded on every front-end page; it has no markup.
 *
 * Clicks are native CSS (smooth-scroll.scss). For a landing, a tiny inline
 * script at the top of <head> takes the anchor off the URL before the browser
 * can jump to it, and smooth-scroll.js puts it back once the page has loaded,
 * which scrolls there smoothly. Reduced motion keeps the browser's own jump.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function smooth_scroll( array $args = array() ): string {
	return '';
}

function smooth_scroll_enqueue(): void {
	wp_enqueue_script( 'floe-smooth-scroll' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\smooth_scroll_enqueue' );

/**
 * Holds back the browser's jump to the URL's anchor on a fresh visit (not a
 * reload or Back, where the browser restores the visitor's own position).
 */
function smooth_scroll_head(): void {
	if ( is_admin() ) {
		return;
	}
	$script = "(function(){var h=location.hash,n=window.performance&&performance.getEntriesByType?performance.getEntriesByType('navigation')[0]:null;"
		. "if(h.length<2||(n&&n.type!=='navigate')||window.matchMedia('(prefers-reduced-motion: reduce)').matches){return}"
		. "document.documentElement.dataset.scrollTo=h;history.replaceState(history.state,'',location.pathname+location.search)})();";
	wp_print_inline_script_tag( $script );
}
add_action( 'wp_head', __NAMESPACE__ . '\\smooth_scroll_head', 1 );
