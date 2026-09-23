<?php
/** Render the six simple section layouts. */

namespace Floe\Blocks;

defined( 'ABSPATH' ) || exit;

function youtube_id( string $url ): string {
	$parts = wp_parse_url( $url );
	if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
		return '';
	}
	$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );
	$id   = '';
	if ( 'youtu.be' === $host ) {
		$id = trim( $parts['path'] ?? '', '/' );
	} elseif ( in_array( $host, array( 'youtube.com', 'm.youtube.com', 'youtube-nocookie.com' ), true ) ) {
		parse_str( $parts['query'] ?? '', $query );
		$id = (string) ( $query['v'] ?? '' );
		if ( ! $id && preg_match( '#^/(?:embed|shorts)/([^/]+)#', $parts['path'] ?? '', $matches ) ) {
			$id = $matches[1];
		}
	}
	return preg_match( '/^[A-Za-z0-9_-]{11}$/', $id ) ? $id : '';
}

function render_simple( string $kind, array $attributes ): void {
	$surface = surface_class( $attributes );
	$classes = 'floe-section floe-' . $kind . ' ' . $surface;
	if ( ! empty( $attributes['showMedia'] ) || ! empty( $attributes['mediaId'] ) ) {
		$classes .= ' has-media';
	}
	$wrapper = get_block_wrapper_attributes( array( 'class' => $classes ) );
	$heading_tag = in_array( $kind, array( 'home-banner', 'page-banner' ), true ) ? 'h1' : 'h2';
	$head = heading( $attributes, 'heading', $heading_tag );
	$copy = eyebrow( $attributes ) . $head . body( $attributes ) . action( $attributes );

	if ( 'home-banner' === $kind ) {
		echo '<section ' . $wrapper . '><div class="floe-section__inner floe-home-banner__grid"><div class="floe-home-banner__copy">' . $copy . '</div>' . media( $attributes ) . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Helpers escape individual content and core escapes wrapper attributes.
		return;
	}
	if ( 'page-banner' === $kind ) {
		$breadcrumb = text( $attributes, 'breadcrumb' );
		$breadcrumb = $breadcrumb ? '<p class="floe-section__eyebrow">' . $breadcrumb . '</p>' : '';
		echo '<section ' . $wrapper . '><div class="floe-section__inner floe-page-banner__grid"><div class="floe-page-banner__copy">' . $breadcrumb . $head . body( $attributes ) . action( $attributes ) . '</div>' . ( ! empty( $attributes['showMedia'] ) ? media( $attributes ) : '' ) . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	if ( 'image-copy' === $kind ) {
		$reversed = ! empty( $attributes['mediaRight'] ) ? ' is-reversed' : '';
		echo '<section ' . $wrapper . '><div class="floe-section__inner floe-image-copy__grid' . esc_attr( $reversed ) . '">' . media( $attributes ) . '<div class="floe-image-copy__copy">' . $copy . '</div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	if ( 'cta' === $kind ) {
		echo '<section ' . $wrapper . '><div class="floe-section__inner floe-cta__content">' . $head . body( $attributes ) . action( $attributes ) . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	if ( 'testimonial' === $kind ) {
		$quote = text( $attributes, 'quote' );
		$name  = text( $attributes, 'attribution' );
		echo '<section ' . $wrapper . '><div class="floe-section__inner floe-testimonial__grid"><div class="floe-testimonial__copy"><span class="floe-testimonial__mark" aria-hidden="true">“</span>' . ( $quote ? '<blockquote class="floe-testimonial__quote">' . $quote . '</blockquote>' : '' ) . ( $name ? '<p class="floe-testimonial__attribution">' . $name . '</p>' : '' ) . '</div>' . ( ! empty( $attributes['showMedia'] ) ? media( $attributes ) : '' ) . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	if ( 'video' === $kind ) {
		$id = youtube_id( (string) value( $attributes, 'youtubeUrl' ) );
		$cover = media( $attributes, 'cover' );
		$button = $id && $cover ? '<button class="floe-video__play" type="button" data-floe-youtube="' . esc_attr( $id ) . '" aria-label="' . esc_attr__( 'Play video', 'floe' ) . '">▶ ' . esc_html__( 'Play video', 'floe' ) . '</button>' : '';
		echo '<section ' . $wrapper . '><div class="floe-section__inner">' . eyebrow( $attributes ) . $head . '<div class="floe-video__cover">' . $cover . $button . '</div>' . body( $attributes ) . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
