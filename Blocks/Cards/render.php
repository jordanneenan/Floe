<?php
/** Manual Cards section. */

defined( 'ABSPATH' ) || exit;

echo '<section ' . get_block_wrapper_attributes( array( 'class' => 'floe-section floe-cards' ) ) . '><div class="floe-section__inner">' . \Floe\Blocks\eyebrow( $attributes ) . \Floe\Blocks\heading( $attributes, 'heading' ) . '<div class="floe-cards__grid">' . $content . '</div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Child blocks are rendered by WordPress and helpers escape content.
