<?php
/**
 * Media component: an image or a silent looping MP4 from the media library.
 *
 *   Floe\component( 'media', array(
 *       'id'          => 123,            // attachment ID (image or video/mp4)
 *       'poster'      => 456,            // poster image ID, used for MP4s
 *       'alt'         => '',             // optional override; otherwise read from the library at render time
 *       'sizes'       => '(min-width: 1440px) 1248px, 100vw',
 *       'ratio'       => '4/3',          // CSS aspect-ratio; '' keeps the file's natural ratio
 *       'radius'      => 'lg',           // sm | md | lg | xl | pill | none
 *       'placeholder' => false,          // show the neutral placeholder when there is no media
 *       'caption'     => '',             // wraps the media in <figure> with a caption
 *       'class'       => '',
 *   ) );
 *
 * Loading: WordPress decides eager or lazy (never forced), so banners load
 * immediately. MP4s get a visible pause/play control (WCAG 2.2.2), start only
 * when on screen and stay paused for visitors who prefer reduced motion.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function media( array $args ): string {
	$id          = absint( $args['id'] ?? 0 );
	$ratio       = (string) ( $args['ratio'] ?? '' );
	$radius      = in_array( $args['radius'] ?? 'lg', array( 'sm', 'md', 'lg', 'xl', 'pill', 'none' ), true ) ? ( $args['radius'] ?? 'lg' ) : 'lg';
	$classes     = array( 'media', 'media--radius-' . $radius );
	$style       = preg_match( '#^[0-9.]+\s*/\s*[0-9.]+$#', $ratio ) ? 'aspect-ratio:' . $ratio : '';
	$inner       = '';
	$mime        = $id ? (string) get_post_mime_type( $id ) : '';
	$is_image    = $id && wp_attachment_is_image( $id );
	$is_video    = $id && 'video/mp4' === $mime;

	if ( $is_image ) {
		$image_args = array( 'class' => 'media__image' );
		if ( ! empty( $args['sizes'] ) ) {
			$image_args['sizes'] = (string) $args['sizes'];
		}
		if ( isset( $args['alt'] ) && '' !== trim( (string) $args['alt'] ) ) {
			$image_args['alt'] = (string) $args['alt'];
		}
		$inner = wp_get_attachment_image( $id, 'full', false, $image_args );
	} elseif ( $is_video ) {
		$poster = absint( $args['poster'] ?? 0 );
		$poster = $poster ? wp_get_attachment_image_url( $poster, 'laptop' ) ?: wp_get_attachment_image_url( $poster, 'full' ) : '';
		$source = wp_get_attachment_url( $id );
		if ( $source ) {
			$classes[] = 'media--video';
			$label     = (string) ( $args['alt'] ?? '' );
			$inner     = sprintf(
				'<video class="media__video" muted loop playsinline preload="metadata"%1$s%2$s><source src="%3$s" type="video/mp4"></video>'
				. '<button type="button" class="media__toggle" aria-pressed="false" data-label-pause="%4$s" data-label-play="%5$s"><span class="screen-reader-text">%4$s</span>%6$s%7$s</button>',
				$poster ? ' poster="' . esc_url( $poster ) . '"' : '',
				'' !== $label ? ' aria-label="' . esc_attr( $label ) . '"' : ' aria-hidden="true"',
				esc_url( $source ),
				esc_attr__( 'Pause video', 'floe' ),
				esc_attr__( 'Play video', 'floe' ),
				\Floe\icon( 'pause', array( 'class' => 'media__icon-pause' ) ),
				\Floe\icon( 'play-small', array( 'class' => 'media__icon-play' ) )
			);
			wp_enqueue_script( 'floe-media' );
		}
	}

	if ( '' === $inner ) {
		if ( empty( $args['placeholder'] ) ) {
			return '';
		}
		$classes[] = 'media--placeholder';
	}

	if ( ! empty( $args['class'] ) ) {
		$classes[] = (string) $args['class'];
	}

	$html = sprintf(
		'<div class="%1$s"%2$s>%3$s</div>',
		esc_attr( implode( ' ', $classes ) ),
		$style ? ' style="' . esc_attr( $style ) . '"' : '',
		$inner
	);

	$caption = trim( (string) ( $args['caption'] ?? '' ) );
	if ( '' !== $caption ) {
		$html = '<figure class="media-figure">' . $html . '<figcaption class="media-figure__caption">' . wp_kses_post( $caption ) . '</figcaption></figure>';
	}
	return $html;
}
