<?php
/**
 * Button component. One source of truth for every button on the site: blocks
 * never write their own button markup or button CSS.
 *
 *   Floe\component( 'button', array(
 *       'label'   => 'Get started',
 *       'url'     => '/contact/',
 *       'style'   => 'primary',   // primary | secondary | inverse | link
 *       'arrow'   => true,
 *       'new_tab' => false,
 *   ) );
 *
 * Primary buttons switch to the Inverse look automatically on the Inverse and
 * Accent surfaces, so blocks don't need to know which surface they sit on.
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

const BUTTON_STYLES = array( 'primary', 'secondary', 'inverse', 'link' );

/**
 * Normalise a stored link attribute ({label,url,newTab}) into button args.
 */
function button_args_from_link( $link, array $defaults = array() ): array {
	$link = is_array( $link ) ? $link : array();
	return array_merge(
		$defaults,
		array(
			'label'   => (string) ( $link['label'] ?? '' ),
			'url'     => (string) ( $link['url'] ?? '' ),
			'new_tab' => ! empty( $link['newTab'] ),
		)
	);
}

function button( array $args ): string {
	$label = trim( wp_strip_all_tags( (string) ( $args['label'] ?? '' ) ) );
	$url   = (string) ( $args['url'] ?? '' );
	if ( '' === $label || '' === $url ) {
		return '';
	}

	$style = in_array( $args['style'] ?? '', BUTTON_STYLES, true ) ? $args['style'] : 'primary';
	$arrow = array_key_exists( 'arrow', $args ) ? (bool) $args['arrow'] : true;

	$attributes = array(
		'class' => trim( 'button button--' . $style . ' ' . ( $args['class'] ?? '' ) ),
		'href'  => esc_url( $url ),
	);
	$new_tab_note = '';
	if ( ! empty( $args['new_tab'] ) ) {
		$attributes['target'] = '_blank';
		$attributes['rel']    = 'noopener';
		$new_tab_note         = '<span class="screen-reader-text"> ' . esc_html__( '(opens in a new tab)', 'floe' ) . '</span>';
	}
	if ( ! empty( $args['rel'] ) ) {
		$attributes['rel'] = trim( ( $attributes['rel'] ?? '' ) . ' ' . $args['rel'] );
	}
	if ( ! empty( $args['aria_label'] ) ) {
		$attributes['aria-label'] = (string) $args['aria_label'];
	}

	$html = '';
	foreach ( $attributes as $name => $value ) {
		$html .= sprintf( ' %s="%s"', $name, esc_attr( $value ) );
	}

	return sprintf(
		'<a%1$s><span class="button__label">%2$s</span>%3$s%4$s</a>',
		$html,
		esc_html( $label ),
		$new_tab_note,
		$arrow ? \Floe\icon( 'arrow', array( 'class' => 'button__icon' ) ) : ''
	);
}
