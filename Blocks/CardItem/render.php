<?php
/** Manual card item. */

defined( 'ABSPATH' ) || exit;

echo \Floe\Blocks\card( (string) ( $attributes['meta'] ?? '' ), (string) ( $attributes['title'] ?? '' ), (string) ( $attributes['description'] ?? '' ), \Floe\Blocks\media( $attributes ), (string) ( $attributes['linkUrl'] ?? '' ), (string) ( $attributes['linkLabel'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared card renderer escapes content.
