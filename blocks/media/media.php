<?php
/**
 * Media (child block): one image or looping MP4, used inside Article and
 * Images rows. Inside a "fill" Images section the row sets the shape.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$in_row = isset( $block->context['floe/imagesFit'] );
$fill   = $in_row && 'natural' !== $block->context['floe/imagesFit'];

echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'media',
	(array) $attributes['media'] + array(
		'cover'   => $fill,
		'sizes'   => $in_row ? '(min-width: 1440px) 624px, (min-width: 768px) 50vw, 92vw' : '(min-width: 800px) 760px, 92vw',
		'caption' => $in_row ? '' : (string) $attributes['caption'],
		'class'   => 'media-block',
	)
);
