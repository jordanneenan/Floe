<?php
/**
 * Lightbox component: wraps an image (usually a Media component) in a link
 * that opens it large in a full-screen viewer. Every image in the same group
 * becomes a slide, so visitors can step through a gallery with buttons,
 * arrow keys or a swipe. The slideshow is the Carousel component.
 *
 *   Floe\component( 'lightbox', array(
 *       'id'      => 123,          // image attachment ID
 *       'content' => $media_html,  // what the visitor clicks
 *       'group'   => 'gallery-1',  // images in one group open as one slideshow
 *       'alt'     => '',           // optional override; otherwise the library's alt text
 *       'caption' => null,         // optional; otherwise the library's caption
 *       'class'   => '',
 *   ) );
 *
 * Without JavaScript the link opens the full-size image. Anything that isn't
 * an image is returned unchanged.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function lightbox( array $args ): string {
	$id      = absint( $args['id'] ?? 0 );
	$content = (string) ( $args['content'] ?? '' );
	if ( ! $id || '' === trim( $content ) || ! wp_attachment_is_image( $id ) ) {
		return $content;
	}

	$full = wp_get_attachment_image_src( $id, 'full' );
	if ( ! $full ) {
		return $content;
	}
	$src     = wp_get_attachment_image_url( $id, 'desktop' ) ?: $full[0];
	$alt     = trim( (string) ( $args['alt'] ?? '' ) ) ?: trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	$caption = trim( (string) ( $args['caption'] ?? wp_get_attachment_caption( $id ) ) );

	wp_enqueue_script( 'floe-lightbox' );
	if ( ! has_action( 'wp_footer', __NAMESPACE__ . '\\lightbox_template' ) ) {
		add_action( 'wp_footer', __NAMESPACE__ . '\\lightbox_template', 5 );
	}

	return sprintf(
		'<a class="%1$s" href="%2$s" data-lightbox="%3$s" data-lightbox-src="%4$s" data-lightbox-srcset="%5$s" data-lightbox-width="%6$d" data-lightbox-height="%7$d" data-lightbox-alt="%8$s" data-lightbox-caption="%9$s"><span class="screen-reader-text">%10$s</span>%11$s</a>',
		esc_attr( trim( 'lightbox-link ' . ( $args['class'] ?? '' ) ) ),
		esc_url( $full[0] ),
		esc_attr( (string) ( $args['group'] ?? 'lightbox' ) ),
		esc_url( $src ),
		esc_attr( (string) wp_get_attachment_image_srcset( $id, 'full' ) ),
		(int) $full[1],
		(int) $full[2],
		esc_attr( $alt ),
		esc_attr( wp_strip_all_tags( $caption ) ),
		esc_html__( 'Open larger image:', 'floe' ),
		$content
	);
}

/**
 * The viewer, printed once at the end of any page with a lightbox link. The
 * script copies it into the page the first time a link is opened.
 */
function lightbox_template(): void {
	$carousel = \Floe\component(
		'carousel',
		array(
			'empty'    => true,
			'label'    => __( 'Images', 'floe' ),
			'per_view' => array( 1, 1, 1 ),
			'prev'     => __( 'Previous image', 'floe' ),
			'next'     => __( 'Next image', 'floe' ),
			'class'    => 'lightbox__carousel',
		)
	);
	if ( '' === $carousel ) {
		$carousel = '<div class="carousel carousel--slides lightbox__carousel"><div class="carousel__track" tabindex="0"></div></div>';
	}
	printf(
		'<template id="floe-lightbox"><dialog class="lightbox surface-inverse" aria-label="%1$s"><div class="lightbox__bar"><p class="lightbox__count" aria-hidden="true"></p><button type="button" class="lightbox__close"><span class="screen-reader-text">%2$s</span>%3$s</button></div>%4$s</dialog></template>',
		esc_attr__( 'Image viewer', 'floe' ),
		esc_html__( 'Close', 'floe' ),
		\Floe\icon( 'close' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output.
		$carousel // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output.
	);
}
