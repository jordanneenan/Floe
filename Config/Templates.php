<?php
/**
 * Template helpers. Every page gets exactly one H1: banners own it, and when a
 * page has no banner the template prints the title instead.
 */

namespace Floe\Config\Templates;

defined( 'ABSPATH' ) || exit;

/** True when the post content is built from Floe sections at the top level. */
function has_sections( ?\WP_Post $post = null ): bool {
	$post = get_post( $post );
	if ( ! $post || ! has_blocks( $post->post_content ) ) {
		return false;
	}
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( isset( $block['blockName'] ) && str_starts_with( (string) $block['blockName'], 'floe/' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Print the current post's content inside the loop. Adds a page title H1 when
 * the content doesn't already contain one, and wraps content that isn't made
 * of Floe sections in a reading column.
 */
function the_content(): void {
	$content = apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core hook.
	$content = str_replace( ']]>', ']]&gt;', $content );

	if ( false === stripos( $content, '<h1' ) ) {
		echo '<header class="page-title container">';
		the_title( '<h1>', '</h1>' );
		if ( 'post' === get_post_type() ) {
			echo '<p class="text-small text-muted">' . esc_html( get_the_date() ) . '</p>';
		}
		echo '</header>';
	}

	if ( has_sections() ) {
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content.
	} else {
		echo '<div class="prose">' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content.
	}
}
