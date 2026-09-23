<?php
/** One Images media row. */

defined( 'ABSPATH' ) || exit;

$columns = max( 1, min( 3, absint( $attributes['columns'] ?? 2 ) ) );
$classes = 'floe-image-row floe-image-row--' . $columns . ( ! empty( $attributes['gap'] ) ? '' : ' is-gapless' ) . ( ( $attributes['fit'] ?? 'cover' ) === 'natural' ? ' is-natural' : '' );
echo '<div ' . get_block_wrapper_attributes( array( 'class' => $classes ) ) . '>' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Child blocks are rendered by WordPress.
