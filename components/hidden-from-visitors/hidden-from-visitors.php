<?php
/**
 * Hidden from visitors: any Floe block can be kept off the public site while
 * it isn't ready. The switch is in each block's Advanced settings
 * (hidden-from-visitors-editor-script.js) and is saved as the
 * hiddenFromVisitors attribute, which this file adds to every floe/* block.
 *
 * People who can edit the page being viewed still see the block, marked with
 * a dashed outline and a "Hidden from visitors" tag (hidden-from-visitors.scss).
 * For everyone else it isn't rendered at all: no markup, no block assets, and
 * its parent lays out only the children that remain.
 *
 * Other modules ask whether a block will show through the floe_block_visible
 * filter (In-page navigation uses it to skip hidden sections). Delete this
 * folder and hidden blocks show to everyone again.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

// No markup of its own: the marker is added to the hidden block itself.
function hidden_from_visitors( array $args = array() ): string {
	return '';
}

/** Adds the hiddenFromVisitors attribute to every Floe block. */
function hidden_from_visitors_attribute( array $args, string $name ): array {
	if ( str_starts_with( $name, 'floe/' ) ) {
		$args['attributes'] = (array) ( $args['attributes'] ?? array() ) + array(
			'hiddenFromVisitors' => array(
				'type'    => 'boolean',
				'default' => false,
			),
		);
	}
	return $args;
}
add_filter( 'register_block_type_args', __NAMESPACE__ . '\\hidden_from_visitors_attribute', 10, 2 );

/**
 * Is this parsed block hidden from the current viewer? Hidden blocks show to
 * anyone who can edit the post being rendered (the page being viewed, also
 * for blocks inside a synced pattern on it), or, where there's no post, to
 * editors and administrators.
 */
function hidden_from_viewer( array $block ): bool {
	if ( empty( $block['attrs']['hiddenFromVisitors'] ) ) {
		return false;
	}
	$post = get_post();
	return ! ( $post ? current_user_can( 'edit_post', $post->ID ) : current_user_can( 'edit_others_posts' ) );
}

// Hidden blocks render nothing for visitors, so their CSS and JS never load.
function hidden_from_visitors_skip( $pre, array $block ) {
	return null === $pre && hidden_from_viewer( $block ) ? '' : $pre;
}
add_filter( 'pre_render_block', __NAMESPACE__ . '\\hidden_from_visitors_skip', 10, 2 );

/**
 * Takes hidden children out of a block before it renders, so a parent that
 * counts its children (CTA columns, the Testimonials slider, Stats) lays out
 * only the ones this viewer will see.
 */
function hidden_from_visitors_prune( array $block ): array {
	if ( empty( $block['innerBlocks'] ) ) {
		return $block;
	}
	$inner   = array();
	$content = array();
	$index   = 0;
	foreach ( (array) ( $block['innerContent'] ?? array() ) as $chunk ) {
		if ( is_string( $chunk ) ) {
			$content[] = $chunk;
			continue;
		}
		$child = $block['innerBlocks'][ $index++ ] ?? null;
		if ( is_array( $child ) && ! hidden_from_viewer( $child ) ) {
			$inner[]   = hidden_from_visitors_prune( $child );
			$content[] = null;
		}
	}
	$block['innerBlocks']  = $inner;
	$block['innerContent'] = $content;
	return $block;
}
add_filter( 'render_block_data', __NAMESPACE__ . '\\hidden_from_visitors_prune' );

// Marks a hidden block for the people who can still see it.
function hidden_from_visitors_mark( $content, array $block ): string {
	$content = (string) $content;
	if ( empty( $block['attrs']['hiddenFromVisitors'] ) || '' === trim( $content ) ) {
		return $content;
	}
	$tags = new \WP_HTML_Tag_Processor( $content );
	if ( $tags->next_tag() ) {
		$tags->add_class( 'hidden-from-visitors' );
		$tags->set_attribute( 'data-hidden-label', __( 'Hidden from visitors', 'floe' ) );
	}
	return $tags->get_updated_html();
}
add_filter( 'render_block', __NAMESPACE__ . '\\hidden_from_visitors_mark', 10, 2 );

/** Answers floe_block_visible for other modules (see In-page navigation). */
function hidden_from_visitors_visible( bool $visible, array $block ): bool {
	return $visible && ! hidden_from_viewer( $block );
}
add_filter( 'floe_block_visible', __NAMESPACE__ . '\\hidden_from_visitors_visible', 10, 2 );
