<?php
/**
 * Card component (Figma "Content card"). Three variants:
 *
 *   post    media, category chip, date, title, excerpt (Posts section)
 *   feature number, title, text, "Learn more" (Cards section, Feature style)
 *   media   media, title, text (Cards section, Media style)
 *
 * The title is the link and covers the whole card, so there is no repeated
 * "Read more" link for screen readers. The Feature card's "Learn more" is a
 * visual cue only.
 *
 *   Floe\component( 'card', array(
 *       'variant'       => 'post',
 *       'title'         => 'A considered approach',
 *       'text'          => 'Excerpt…',
 *       'url'           => get_permalink(),
 *       'media'         => array( 'id' => 12 ),   // Media component args
 *       'category'      => 'Insights',
 *       'date'          => '12 Jun 2026',
 *       'datetime'      => '2026-06-12',
 *       'number'        => '01',
 *       'link_label'    => 'Learn more',
 *       'heading_level' => 3,
 *   ) );
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function card( array $args ): string {
	$variant = in_array( $args['variant'] ?? 'post', array( 'post', 'feature', 'media' ), true ) ? ( $args['variant'] ?? 'post' ) : 'post';
	$level   = max( 2, min( 4, (int) ( $args['heading_level'] ?? 3 ) ) );
	$title   = trim( (string) ( $args['title'] ?? '' ) );
	$url     = (string) ( $args['url'] ?? '' );
	$text    = trim( (string) ( $args['text'] ?? '' ) );

	if ( '' === $title && '' === $text ) {
		return '';
	}

	$title_html = '' !== $url
		? sprintf(
			'<a class="card__link" href="%s"%s>%s</a>',
			esc_url( $url ),
			! empty( $args['new_tab'] ) ? ' target="_blank" rel="noopener"' : '',
			wp_kses_post( $title )
		)
		: wp_kses_post( $title );

	$parts = array();

	if ( 'feature' === $variant ) {
		// 'auto' numbers cards in order with a CSS counter (01, 02, …).
		if ( 'auto' === ( $args['number'] ?? '' ) ) {
			$parts[] = '<p class="card__number card__number--auto" aria-hidden="true"></p>';
		} elseif ( ! empty( $args['number'] ) ) {
			$parts[] = '<p class="card__number">' . esc_html( (string) $args['number'] ) . '</p>';
		}
	} else {
		$media_args = (array) ( $args['media'] ?? array() );
		$parts[]    = \Floe\component(
			'media',
			$media_args + array(
				'ratio'       => '4/3',
				'sizes'       => '(min-width: 1440px) 395px, (min-width: 768px) 33vw, 100vw',
				'placeholder' => 'media' === $variant,
				'class'       => 'card__media',
			)
		);
	}

	if ( 'post' === $variant && ( ! empty( $args['category'] ) || ! empty( $args['date'] ) ) ) {
		$meta = '';
		if ( ! empty( $args['category'] ) ) {
			$meta .= '<span class="card__chip">' . esc_html( (string) $args['category'] ) . '</span>';
		}
		if ( ! empty( $args['date'] ) ) {
			$meta .= sprintf(
				'<time class="card__date"%s>%s</time>',
				! empty( $args['datetime'] ) ? ' datetime="' . esc_attr( (string) $args['datetime'] ) . '"' : '',
				esc_html( (string) $args['date'] )
			);
		}
		$parts[] = '<div class="card__meta">' . $meta . '</div>';
	}

	if ( '' !== $title ) {
		$parts[] = sprintf( '<h%1$d class="card__title">%2$s</h%1$d>', $level, $title_html );
	}
	if ( '' !== $text ) {
		$parts[] = '<p class="card__text">' . wp_kses_post( $text ) . '</p>';
	}
	if ( 'feature' === $variant && '' !== $url && ! empty( $args['link_label'] ) ) {
		$parts[] = '<span class="card__more button button--link" aria-hidden="true"><span class="button__label">' . esc_html( (string) $args['link_label'] ) . '</span>' . \Floe\icon( 'arrow', array( 'class' => 'button__icon' ) ) . '</span>';
	}

	$classes = 'card card--' . $variant . ( '' !== $url ? ' card--linked' : '' );
	return '<article class="' . esc_attr( $classes ) . '">' . implode( '', array_filter( $parts ) ) . '</article>';
}
