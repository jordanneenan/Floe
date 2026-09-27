<?php
/**
 * Spacing: a gap below a section. The section before it drops its bottom
 * margin, so the gap is exactly the chosen size.
 *
 * @var array $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$presets = array(
	'large'  => array( 120, 120, 86, 67 ),
	'medium' => array( 80, 80, 57, 44 ),
	'small'  => array( 40, 40, 29, 22 ),
	'none'   => array( 0, 0, 0, 0 ),
);
$size    = isset( $presets[ $attributes['size'] ?? '' ] ) ? $attributes['size'] : 'large';
$values  = $presets[ $size ];

if ( ! empty( $attributes['custom'] ) ) {
	foreach ( array( 'large', 'desktop', 'tablet', 'mobile' ) as $index => $key ) {
		if ( isset( $attributes[ $key ] ) && is_numeric( $attributes[ $key ] ) ) {
			$values[ $index ] = max( 0, min( 500, (int) $attributes[ $key ] ) );
		}
	}
}

$style = sprintf(
	'--spacing-large:%dpx;--spacing-desktop:%dpx;--spacing-tablet:%dpx;--spacing-mobile:%dpx',
	...$values
);

printf(
	'<div %s aria-hidden="true"></div>',
	Floe\block_attributes( $block, array( 'style' => $style ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes().
);
