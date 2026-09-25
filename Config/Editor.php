<?php
/**
 * Editor lockdown. Floe sections are the top-level inserter items. Core and
 * third-party blocks can only be placed inside a Floe block's designated
 * slots (each Floe block's allowedBlocks decides which ones it accepts).
 * Nothing here names individual Floe blocks: they are found by namespace.
 */

namespace Floe\Config\Editor;

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
 * Floe block. Blocks that already declare a parent or ancestor keep theirs.
 */
function restrict_to_slots(): void {
	$floe = floe_block_names();
	if ( ! $floe ) {
		return;
	}
	foreach ( \WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
		if ( str_starts_with( $name, 'floe/' ) || ! empty( $type->parent ) || ! empty( $type->ancestor ) ) {
			continue;
		}
		$type->ancestor = $floe;
	}
}
add_action( 'init', __NAMESPACE__ . '\\restrict_to_slots', PHP_INT_MAX );

// New posts start with an Article section, so authors write in the reading column.
function post_template(): void {
	$post = get_post_type_object( 'post' );
	if ( $post && \WP_Block_Type_Registry::get_instance()->is_registered( 'floe/article' ) ) {
		$post->template = array( array( 'floe/article' ) );
	}
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
