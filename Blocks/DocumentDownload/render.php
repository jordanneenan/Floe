<?php
/** Document Download section. */

defined( 'ABSPATH' ) || exit;

echo '<section ' . get_block_wrapper_attributes( array( 'class' => 'floe-section floe-document-download' ) ) . '><div class="floe-section__inner">' . \Floe\Blocks\eyebrow( $attributes ) . \Floe\Blocks\heading( $attributes, 'heading' ) . '<div class="floe-document-download__items">' . $content . '</div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Child blocks are rendered by WordPress and helpers escape content.
