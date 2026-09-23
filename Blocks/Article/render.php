<?php
/** Article section. */

defined( 'ABSPATH' ) || exit;

$wrapper = get_block_wrapper_attributes( array( 'class' => 'floe-section floe-article ' . \Floe\Blocks\surface_class( $attributes ) ) );
echo '<section ' . $wrapper . '><div class="floe-section__inner"><div class="floe-article__content">' . $content . '</div>' . \Floe\Blocks\action( $attributes ) . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner block content is rendered by WordPress; helpers escape attributes.
