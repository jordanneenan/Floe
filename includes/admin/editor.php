<?php
/**
 * Editor tidy-up, following Made: no block directory, no tags on posts and a
 * wider settings sidebar.
 */

namespace Floe\Includes\Admin\Editor;

defined( 'ABSPATH' ) || exit;

remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );

function remove_tags(): void {
	unregister_taxonomy_for_object_type( 'post_tag', 'post' );
}
add_action( 'init', __NAMESPACE__ . '\\remove_tags' );

function sidebar_width(): void {
	$css = '.interface-complementary-area.editor-sidebar,.interface-complementary-area__fill{width:100%!important;max-width:400px;min-width:300px}'
		. '@media (min-width:1650px){.interface-complementary-area.editor-sidebar,.interface-complementary-area__fill{max-width:500px}}';
	wp_add_inline_style( 'wp-edit-post', $css );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\sidebar_width' );
