<?php
/**
 * Slide (child of Image carousel): one image or looping MP4, cropped to the
 * carousel's shape, with an optional caption. Renders nothing without media.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$media = Floe\component(
	'media',
	(array) $attributes['media'] + array(
		'ratio'   => (string) ( $block->context['floe/carouselRatio'] ?? '3/2' ),
		'sizes'   => '(min-width: 1440px) 1248px, 92vw',
		'caption' => (string) $attributes['caption'],
	)
);
if ( '' === $media ) {
	return;
}
?>
<div <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output. ?></div>
