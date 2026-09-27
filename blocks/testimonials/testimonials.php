<?php
/**
 * Testimonials: quote cards. Up to three sit in a grid; more become a
 * carousel (the Carousel component) with previous/next buttons in a row
 * below it, on the right. The heading group is a Block intro above it.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}
$slider = count( $block->inner_blocks ) > 3 ? Floe\component(
	'carousel',
	array(
		'content'  => $content,
		'label'    => __( 'Quotes', 'floe' ),
		'per_view' => array( 1.16, 2, 3 ),
		'prev'     => __( 'Previous quotes', 'floe' ),
		'next'     => __( 'Next quotes', 'floe' ),
	)
) : '';
?>
<section <?php echo Floe\block_attributes( $block, array( 'class' => $slider ? 'testimonials--slider' : 'testimonials--grid' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="testimonials__inner">
		<?php if ( $slider ) : ?>
			<?php echo $slider; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output. ?>
		<?php else : ?>
			<div class="testimonials__track"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
	</div>
</section>
