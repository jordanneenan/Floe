<?php
/**
 * Editor lockdown. Floe sections are the top-level inserter items. Core and
 * third-party blocks can only be placed inside a Floe block's designated
 * slots (each Floe block's allowedBlocks decides which ones it accepts).
 * Nothing here names individual Floe blocks: they are found by namespace.
 */

namespace Floe\Includes\Editor;

defined( 'ABSPATH' ) || exit;

// Core blocks that Floe blocks may offer inside their slots. Everything else
// from core is removed from the inserter.
const CORE_SLOT_BLOCKS = array(
	'core/paragraph',
	'core/heading',
	'core/list',
	'core/list-item',
	'core/quote',
	'core/pullquote',
	'core/image',
	'core/table',
	'core/separator',
	'core/buttons',
	'core/button',
	'core/details',
	'core/shortcode',
	'core/embed',
);

function floe_block_names(): array {
	$names = array();
	foreach ( \WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
		if ( str_starts_with( $name, 'floe/' ) ) {
			$names[] = $name;
		}
	}
	return $names;
}

/**
 * Allowed blocks: every Floe block, the core slot blocks above, and any
 * non-core block (so a form plugin's block can go in a form slot).
 */
function allowed_blocks( $allowed, $context ) {
	$names = array();
	foreach ( \WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
		if ( ! str_starts_with( $name, 'core/' ) || in_array( $name, CORE_SLOT_BLOCKS, true ) ) {
			$names[] = $name;
		}
	}
	return $names;
}
add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\\allowed_blocks', 10, 2 );

/**
 * Keep non-Floe blocks out of the top level: they may only appear inside a
 * Floe block. Blocks that already declare a parent or ancestor keep theirs,
 * and blocks named by the floe_top_level_blocks filter (e.g. the Shortcode
 * block, see includes/content/shortcode.php) may also go directly on a page.
 */
function restrict_to_slots(): void {
	$floe = floe_block_names();
	if ( ! $floe ) {
		return;
	}
	$top_level = (array) apply_filters( 'floe_top_level_blocks', array() );
	foreach ( \WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
		if ( str_starts_with( $name, 'floe/' ) || in_array( $name, $top_level, true ) || ! empty( $type->parent ) || ! empty( $type->ancestor ) ) {
			continue;
		}
		$type->ancestor = $floe;
	}
}
add_action( 'init', __NAMESPACE__ . '\\restrict_to_slots', PHP_INT_MAX );

/**
 * Floe sections go on the page itself or in a Floe block's slot (Background,
 * a two-column Block intro), never inside a core block such as an FAQ's
 * Details or an Article's Quote. block.json can't say "top level or these
 * parents", so the editor's insert check does it: a Floe block without a
 * parent is refused when the block it would go into isn't a Floe block.
 */
function section_placement(): void {
	wp_add_inline_script(
		'wp-block-editor',
		"wp.hooks.addFilter( 'blockEditor.__unstableCanInsertBlockType', 'floe/section-placement', function ( canInsert, blockType, rootClientId, select ) {
			if ( ! canInsert || ! rootClientId || ! blockType.name.startsWith( 'floe/' ) || ( blockType.parent || [] ).length ) {
				return canInsert;
			}
			var root = select.getBlock( rootClientId );
			return ! root || root.name.startsWith( 'floe/' );
		} );"
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\section_placement' );

// Posts are built from blocks like pages: new posts start with a Page Banner,
// an Article for the writing, and a CTA. Blocks that are switched off are
// left out of the starting layout.
function post_template(): void {
	$post     = get_post_type_object( 'post' );
	$registry = \WP_Block_Type_Registry::get_instance();
	if ( ! $post ) {
		return;
	}
	$template = array();
	foreach ( array( 'floe/page-banner', 'floe/article' ) as $name ) {
		if ( $registry->is_registered( $name ) ) {
			$template[] = array( $name );
		}
	}
	if ( $registry->is_registered( 'floe/cta' ) && $registry->is_registered( 'floe/cta-panel' ) ) {
		$template[] = array( 'floe/cta', array(), array( array( 'floe/cta-panel' ) ) );
	}
	$post->template = $template;
}
add_action( 'init', __NAMESPACE__ . '\\post_template', PHP_INT_MAX );

// Floe block categories: sections first in the inserter.
function categories( array $categories ): array {
	return array_merge(
		array(
			array(
				'slug'  => 'floe-sections',
				'title' => __( 'Floe sections', 'floe' ),
			),
			array(
				'slug'  => 'floe-parts',
				'title' => __( 'Floe parts', 'floe' ),
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', __NAMESPACE__ . '\\categories' );

// No Openverse media tab: images come from the site's own media library.
function editor_settings( array $settings ): array {
	$settings['enableOpenverseMediaCategory'] = false;
	return $settings;
}
add_filter( 'block_editor_settings_all', __NAMESPACE__ . '\\editor_settings' );
