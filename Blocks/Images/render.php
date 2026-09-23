<?php
/** Images section. */

defined( 'ABSPATH' ) || exit;

echo '<section ' . get_block_wrapper_attributes( array( 'class' => 'floe-section floe-images' ) ) . '><div class="floe-section__inner">' . \Floe\Blocks\eyebrow( $attributes ) . \Floe\Blocks\heading( $attributes, 'heading' ) . '<div class="floe-images__rows">' . $content . '</div></div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Child blocks are rendered by WordPress.
