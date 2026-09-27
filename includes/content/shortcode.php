<?php
/**
 * Shortcode blocks can go directly on a page, between sections, for the odd
 * plugin shortcode that doesn't belong inside an Article. There, the output
 * sits in the content width with a section's spacing below it (the
 * .shortcode classes in base.css). Inside a section it's left as it is.
 * Delete this file to keep Shortcode blocks inside sections only.
 */

namespace Floe\Includes\Content\Shortcode;

defined( 'ABSPATH' ) || exit;

function top_level( array $blocks ): array {
	$blocks[] = 'core/shortcode';
	return $blocks;
}
add_filter( 'floe_top_level_blocks', __NAMESPACE__ . '\\top_level' );

// Mark Shortcode blocks that have no parent block.
function mark( array $parsed, array $source, $parent ): array {
	if ( 'core/shortcode' === ( $parsed['blockName'] ?? '' ) && null === $parent ) {
		$parsed['attrs']['floeTopLevel'] = true;
	}
	return $parsed;
}
add_filter( 'render_block_data', __NAMESPACE__ . '\\mark', 10, 3 );

// The shortcode itself runs later, over the whole page, so this wraps the
// shortcode text and its output ends up inside the wrapper.
function wrap( string $content, array $block ): string {
	if ( empty( $block['attrs']['floeTopLevel'] ) || '' === trim( $content ) ) {
		return $content;
	}
	return '<div class="shortcode floe-section"><div class="shortcode__inner">' . $content . '</div></div>';
}
add_filter( 'render_block_core/shortcode', __NAMESPACE__ . '\\wrap', 10, 2 );
