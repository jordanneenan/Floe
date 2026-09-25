<?php
/**
 * Registers every enabled block found by Config/Modules.php, and provides the
 * helpers block templates share.
 */

namespace Floe\Config\Blocks;

use Floe\Config\Modules;

defined( 'ABSPATH' ) || exit;

function register(): void {
	foreach ( Modules\enabled( 'block' ) as $module ) {
		register_block_type( $module['dir'] );
	}
}
add_action( 'init', __NAMESPACE__ . '\\register' );

namespace Floe;

/**
 * Wrapper attributes for a block's outer element. Adds the block's own class
 * (its folder name, e.g. "page-banner", so you can inspect a page and find the
 * folder), "floe-section" for top-level sections (blocks in the Floe sections
 * category, unless block.json sets "supports": { "floeSection": false }), plus
 * a surface class ('surface' => 'tint' gives "surface-tint"), and any extra
 * classes and attributes.
 *
 * @param \WP_Block|null $block Current block instance (the $block variable in render templates).
 */
function block_attributes( $block, array $extra = array() ): string {
	$name    = $block instanceof \WP_Block ? (string) $block->name : '';
	$type    = $block instanceof \WP_Block ? $block->block_type : null;
	$section = $type && 'floe-sections' === $type->category && false !== ( $type->supports['floeSection'] ?? true );
	$classes = array_filter(
		array_merge(
			array( substr( $name, (int) strpos( $name, '/' ) + 1 ), $section ? 'floe-section' : '' ),
			(array) ( $extra['class'] ?? array() )
		)
	);
	if ( ! empty( $extra['surface'] ) ) {
		$classes[] = 'surface-' . sanitize_html_class( (string) $extra['surface'] );
	}
	unset( $extra['class'], $extra['surface'] );
	$extra['class'] = implode( ' ', $classes );
	// Sections with an HTML anchor get it as their id (used by In-page navigation).
	if ( $block instanceof \WP_Block && ! empty( $block->attributes['anchor'] ) && empty( $extra['id'] ) ) {
		$extra['id'] = sanitize_title( (string) $block->attributes['anchor'] );
	}
	return get_block_wrapper_attributes( $extra );
}
