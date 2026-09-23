<?php
/** Shared, theme-owned rendering helpers for Floe blocks. */

namespace Floe\Blocks;

defined( 'ABSPATH' ) || exit;

function value( array $attributes, string $key, $default = '' ) {
	return isset( $attributes[ $key ] ) ? $attributes[ $key ] : $default;
}

function text( array $attributes, string $key ): string {
	return wp_kses_post( (string) value( $attributes, $key ) );
}

function heading( array $attributes, string $key, string $tag = 'h2' ): string {
	$content = text( $attributes, $key );
	if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
		return '';
	}
	return '<' . $tag . ' class="floe-section__heading">' . $content . '</' . $tag . '>';
}

function eyebrow( array $attributes ): string {
	$content = text( $attributes, 'eyebrow' );
	return $content ? '<p class="floe-section__eyebrow">' . $content . '</p>' : '';
}

function body( array $attributes, string $key = 'body' ): string {
	$content = text( $attributes, $key );
	return $content ? '<div class="floe-section__body">' . wpautop( $content ) . '</div>' : '';
}

function action( array $attributes ): string {
	$label = trim( wp_strip_all_tags( (string) value( $attributes, 'buttonLabel' ) ) );
	$url   = (string) value( $attributes, 'buttonUrl' );
	if ( ! $label || ! $url ) {
		return '';
	}
	return '<a class="floe-button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
}

function media( array $attributes, string $prefix = 'media', string $class = '' ): string {
	$id   = absint( value( $attributes, $prefix . 'Id', 0 ) );
	$type = (string) value( $attributes, $prefix . 'Type', 'image' );
	if ( ! $id ) {
		return '';
	}

	$class_name = trim( 'floe-media ' . $class );
	if ( 'video' === $type ) {
		if ( 'video/mp4' !== get_post_mime_type( $id ) ) {
			return '';
		}
		$poster_id = absint( value( $attributes, $prefix . 'PosterId', 0 ) );
		$poster    = $poster_id ? wp_get_attachment_image_url( $poster_id, 'full' ) : false;
		$source    = wp_get_attachment_url( $id );
		if ( ! $poster || ! $source ) {
			return '';
		}
		return '<div class="' . esc_attr( $class_name ) . '"><video class="floe-loop" muted loop playsinline preload="none" poster="' . esc_url( $poster ) . '" aria-hidden="true"><source src="' . esc_url( $source ) . '" type="video/mp4"></video></div>';
	}

	if ( ! wp_attachment_is_image( $id ) ) {
		return '';
	}
	$image_attributes = array( 'class' => 'floe-media__image', 'loading' => 'lazy' );
	$alt              = (string) value( $attributes, $prefix . 'Alt' );
	if ( $alt ) {
		$image_attributes['alt'] = $alt;
	}
	$image = wp_get_attachment_image( $id, 'large', false, $image_attributes );
	return $image ? '<div class="' . esc_attr( $class_name ) . '">' . $image . '</div>' : '';
}

function surface_class( array $attributes ): string {
	$surface = (string) value( $attributes, 'surface', 'base' );
	return in_array( $surface, array( 'base', 'subtle', 'accent' ), true ) ? 'floe-surface--' . $surface : 'floe-surface--base';
}

function card( string $meta, string $title, string $description, string $visual, string $url = '', string $link_label = '' ): string {
	$markup  = '<article class="floe-card">';
	$markup .= $visual;
	$markup .= '<div class="floe-card__content">';
	$markup .= $meta ? '<p class="floe-card__meta">' . wp_kses_post( $meta ) . '</p>' : '';
	$markup .= $title ? '<h3 class="floe-card__title">' . wp_kses_post( $title ) . '</h3>' : '';
	$markup .= $description ? '<div class="floe-card__description">' . wpautop( wp_kses_post( $description ) ) . '</div>' : '';
	$markup .= $url && $link_label ? '<a class="floe-card__link" href="' . esc_url( $url ) . '">' . esc_html( $link_label ) . '</a>' : '';
	return $markup . '</div></article>';
}
