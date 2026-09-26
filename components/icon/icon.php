<?php
/**
 * Icon component: the Floe inline SVG set, drawn on a 16/18/22px grid in
 * currentColor. Icons are decorative (aria-hidden) unless given a label.
 *
 *   Floe\icon( 'arrow' )
 *   Floe\component( 'icon', array( 'name' => 'check', 'label' => 'Included' ) )
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function icon_paths(): array {
	return array(
		'arrow'       => array( 16, '<path d="M3 8H13M9 12L13 8L9 4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>' ),
		'arrow-left'  => array( 16, '<path d="M13 8H3M7 12L3 8L7 4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>' ),
		'download'    => array( 18, '<path d="M9 3V13M13.5 8.5L9 13L4.5 8.5M3.5 15.5H14.5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>' ),
		'play'        => array( 22, '<path d="M7 4.8V17.2C7 18 7.9 18.5 8.6 18.1L18.3 11.9C18.9 11.5 18.9 10.5 18.3 10.1L8.6 3.9C7.9 3.5 7 4 7 4.8Z" fill="currentColor" stroke="none"/>' ),
		'plus'        => array( 16, '<path d="M8 3V13M3 8H13" stroke-width="1.6" stroke-linecap="round"/>' ),
		'minus'       => array( 16, '<path d="M3 8H13" stroke-width="1.6" stroke-linecap="round"/>' ),
		'check'       => array( 18, '<path d="M4 9.5L7.2 12.5L14 5.5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>' ),
		'dash'        => array( 18, '<path d="M5 9H13" stroke-width="1.6" stroke-linecap="round"/>' ),
		'menu'        => array( 24, '<path d="M4 7H20M4 12H20M4 17H20" stroke-width="1.8" stroke-linecap="round"/>' ),
		'close'       => array( 24, '<path d="M6 6L18 18M18 6L6 18" stroke-width="1.8" stroke-linecap="round"/>' ),
		'chevron'     => array( 16, '<path d="M4 6L8 10L12 6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>' ),
		'pause'       => array( 16, '<path d="M5.5 3.5V12.5M10.5 3.5V12.5" stroke-width="1.8" stroke-linecap="round"/>' ),
		'play-small'  => array( 16, '<path d="M5 3.6V12.4C5 13 5.6 13.3 6.1 13L12.9 8.6C13.4 8.3 13.4 7.7 12.9 7.4L6.1 3C5.6 2.7 5 3 5 3.6Z" fill="currentColor" stroke="none"/>' ),
	);
}

/**
 * @param array{name:string,label?:string,class?:string} $args
 */
function icon( array $args ): string {
	$paths = icon_paths();
	$name  = (string) ( $args['name'] ?? '' );
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	[ $size, $markup ] = $paths[ $name ];

	$label = (string) ( $args['label'] ?? '' );
	$a11y  = '' !== $label ? 'role="img" aria-label="' . esc_attr( $label ) . '"' : 'aria-hidden="true" focusable="false"';
	$class = trim( 'icon icon-' . $name . ' ' . ( $args['class'] ?? '' ) );

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 %2$d %2$d" fill="none" stroke="currentColor" %3$s>%4$s</svg>',
		esc_attr( $class ),
		$size,
		$a11y,
		$markup
	);
}
