<?php
/** A gap that replaces the preceding Floe section's default bottom gap. */

defined( 'ABSPATH' ) || exit;

$size = in_array( $attributes['size'] ?? 'large', array( 'large', 'medium', 'small', 'none' ), true ) ? $attributes['size'] : 'large';
$presets = array(
	'large'  => array( 120, 120, 86, 67 ),
	'medium' => array( 80, 80, 57, 44 ),
	'small'  => array( 40, 40, 29, 22 ),
	'none'   => array( 0, 0, 0, 0 ),
);
$keys = array( 'largeDesktop', 'desktop', 'tablet', 'mobile' );
$custom = ! empty( $attributes['custom'] ) || ( isset( $attributes['automatic'] ) && isset( $attributes['desktop'] ) && $attributes['desktop'] >= 0 );
$values = $presets[ $size ];
if ( $custom ) {
	foreach ( $keys as $index => $key ) {
		if ( isset( $attributes[ $key ] ) && is_numeric( $attributes[ $key ] ) && $attributes[ $key ] >= 0 ) {
			$values[ $index ] = min( 500, (float) $attributes[ $key ] );
		}
	}
	if ( ! isset( $attributes['largeDesktop'] ) || $attributes['largeDesktop'] < 0 ) {
		$values[0] = $values[1];
	}
}
$styles = array();
foreach ( array( 'large-desktop', 'desktop', 'tablet', 'mobile' ) as $index => $key ) {
	$styles[] = '--floe-spacing-' . $key . ':' . $values[ $index ] . 'px';
}
$wrapper = get_block_wrapper_attributes( array(
	'class' => 'floe-spacing floe-spacing--' . $size,
	'style' => implode( ';', $styles ),
) );
echo '<div ' . $wrapper . ' aria-hidden="true"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes wrapper attributes; custom values are capped numeric values.
