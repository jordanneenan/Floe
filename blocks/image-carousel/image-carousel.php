<?php
/**
 * Image carousel: Slide blocks one at a time in a Carousel, with
 * previous/next buttons below on the right. A heading group, if wanted, is a
 * Block intro above it or beside it in two columns.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$carousel = Floe\component(
	'carousel',
	array(
		'content'  => $content,
		'label'    => __( 'Images', 'floe' ),
		'per_view' => array( 1.08, 1.08, 1 ),
		'prev'     => __( 'Previous image', 'floe' ),
		'next'     => __( 'Next image', 'floe' ),
	)
);
if ( '' === $carousel ) {
	return;
}
?>
<section <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="image-carousel__inner">
		<?php echo $carousel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output. ?>
	</div>
</section>
