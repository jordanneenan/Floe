<?php
/**
 * Server render for floe/spacing. WordPress supplies $attributes and $block.
 *
 * @package Floe
 */

defined( 'ABSPATH' ) || exit;

$size = isset( $attributes['size'] ) && in_array( $attributes['size'], array( 'large', 'medium', 'small', 'none' ), true )
	? $attributes['size']
	: 'large';

$desktop = isset( $attributes['desktop'] ) && is_numeric( $attributes['desktop'] )
	? (float) $attributes['desktop']
	: -1;
$tablet = isset( $attributes['tablet'] ) && is_numeric( $attributes['tablet'] )
	? (float) $attributes['tablet']
	: -1;
$mobile = isset( $attributes['mobile'] ) && is_numeric( $attributes['mobile'] )
	? (float) $attributes['mobile']
	: -1;
$automatic = ! isset( $attributes['automatic'] ) || (bool) $attributes['automatic'];

$styles = array();
if ( $desktop >= 0 ) {
	$desktop = min( $desktop, 500 );
	$styles[] = '--floe-spacing-desktop:' . $desktop . 'px';
	$styles[] = '--floe-spacing-tablet:' . ( $automatic || $tablet < 0 ? round( $desktop / 1.4 ) : min( $tablet, 500 ) ) . 'px';
	$styles[] = '--floe-spacing-mobile:' . ( $automatic || $mobile < 0 ? round( $desktop / 1.8 ) : min( $mobile, 500 ) ) . 'px';
} elseif ( ! $automatic ) {
	if ( $tablet >= 0 ) {
		$styles[] = '--floe-spacing-tablet:' . min( $tablet, 500 ) . 'px';
	}
	if ( $mobile >= 0 ) {
		$styles[] = '--floe-spacing-mobile:' . min( $mobile, 500 ) . 'px';
	}
}

$wrapper = array( 'class' => 'floe-spacing floe-spacing--' . $size );
if ( $styles ) {
	$wrapper['style'] = implode( ';', $styles );
}
?>
<div <?php echo get_block_wrapper_attributes( $wrapper ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes wrapper attributes. ?> aria-hidden="true"></div>
