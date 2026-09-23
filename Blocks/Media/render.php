<?php
/** Shared image or looping video item. */

defined( 'ABSPATH' ) || exit;

$markup = \Floe\Blocks\media( $attributes );
if ( $markup ) {
	echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'floe-media-item' ) ) . '>' . $markup . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes wrapper attributes, media helper escapes content.
}
