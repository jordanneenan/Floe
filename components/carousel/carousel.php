<?php
/**
 * Carousel component: Floe's own carousel, no library. Every carousel on the
 * site uses it, in one of two modes:
 *
 *   slides  a row of slides that scrolls sideways: swipe, trackpad, arrow
 *           keys, and previous/next buttons that step one slide and disable
 *           at either end. The buttons hide when everything already fits.
 *   ticker  the items scroll past continuously, like a news ticker. It pauses
 *           while hovered or focused and has a visible pause button
 *           (WCAG 2.2.2). With reduced motion, or without JavaScript, the
 *           items simply sit in a centred, wrapping row.
 *
 *   Floe\component( 'carousel', array(
 *       'content'  => $html,            // the slides: each top-level element is one slide
 *       'label'    => 'Quotes',         // accessible name of the carousel
 *       'mode'     => 'slides',         // slides | ticker
 *       'per_view' => array( 1.15, 2, 3 ), // slides visible on mobile, from 550px (tablet), from 768px (desktop)
 *       'list'     => false,            // ticker: true renders the track as a <ul> (items must be <li>)
 *       'prev'     => 'Previous',       // button labels (slides mode)
 *       'next'     => 'Next',
 *       'speed'    => 40,               // ticker speed in pixels per second
 *       'empty'    => false,            // render even with no content (a shell filled by script)
 *       'class'    => '',
 *   ) );
 *
 * Scripts that build carousels themselves (the lightbox) call
 * window.floe.carousel.init( element ) after adding the markup.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function carousel( array $args ): string {
	$content = (string) ( $args['content'] ?? '' );
	if ( '' === trim( $content ) && empty( $args['empty'] ) ) {
		return '';
	}

	$mode  = 'ticker' === ( $args['mode'] ?? '' ) ? 'ticker' : 'slides';
	$tag   = empty( $args['list'] ) ? 'div' : 'ul';
	$id    = wp_unique_id( 'carousel-' );
	$label = (string) ( $args['label'] ?? __( 'Carousel', 'floe' ) );
	$class = trim( 'carousel carousel--' . $mode . ' ' . ( $args['class'] ?? '' ) );

	wp_enqueue_script( 'floe-carousel' );

	if ( 'ticker' === $mode ) {
		$speed = max( 10, min( 200, (int) ( $args['speed'] ?? 40 ) ) );
		return sprintf(
			'<div class="%1$s" data-carousel="ticker" style="--carousel-speed:%2$d" role="region" aria-label="%3$s">'
			. '<div class="carousel__viewport"><%4$s class="carousel__track">%5$s</%4$s></div>'
			. '<button type="button" class="carousel__pause" aria-pressed="false" hidden><span class="screen-reader-text">%6$s</span>%7$s%8$s</button>'
			. '</div>',
			esc_attr( $class ),
			$speed,
			esc_attr( $label ),
			$tag,
			$content,
			esc_html__( 'Pause scrolling', 'floe' ),
			\Floe\icon( 'pause', array( 'class' => 'carousel__icon-pause' ) ),
			\Floe\icon( 'play-small', array( 'class' => 'carousel__icon-play' ) )
		);
	}

	$per_view = array_values( (array) ( $args['per_view'] ?? array( 1, 1, 1 ) ) );
	$views    = array();
	foreach ( array( 'mobile', 'tablet', 'desktop' ) as $index => $size ) {
		$value = (float) ( $per_view[ $index ] ?? end( $per_view ) );
		if ( $value > 0 ) {
			$views[] = sprintf( '--carousel-%s:%s', $size, rtrim( rtrim( number_format( $value, 3, '.', '' ), '0' ), '.' ) );
		}
	}

	return sprintf(
		'<div class="%1$s" data-carousel="slides" style="%2$s">'
		. '<div class="carousel__track" id="%3$s" role="region" aria-roledescription="%4$s" aria-label="%5$s" tabindex="0">%6$s</div>'
		. '<div class="carousel__controls">'
		. '<button type="button" class="carousel__prev" aria-controls="%3$s" disabled><span class="screen-reader-text">%7$s</span>%8$s</button>'
		. '<button type="button" class="carousel__next" aria-controls="%3$s"><span class="screen-reader-text">%9$s</span>%10$s</button>'
		. '</div></div>',
		esc_attr( $class ),
		esc_attr( implode( ';', $views ) ),
		esc_attr( $id ),
		esc_attr__( 'carousel', 'floe' ),
		esc_attr( $label ),
		$content,
		esc_html( (string) ( $args['prev'] ?? __( 'Previous', 'floe' ) ) ),
		\Floe\icon( 'arrow-left' ),
		esc_html( (string) ( $args['next'] ?? __( 'Next', 'floe' ) ) ),
		\Floe\icon( 'arrow' )
	);
}
