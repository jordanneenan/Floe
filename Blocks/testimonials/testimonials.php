<?php
/**
 * Testimonials: section header plus quote cards. Up to three sit in a grid;
 * more become a slider with previous/next buttons.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}
$count    = count( $block->inner_blocks );
$slider   = $count > 3;
$track_id = wp_unique_id( 'testimonials-' );
$controls = $slider ? sprintf(
	'<div class="testimonials__controls"><button type="button" class="testimonials__prev" aria-controls="%1$s" disabled><span class="screen-reader-text">%2$s</span>%3$s</button><button type="button" class="testimonials__next" aria-controls="%1$s"><span class="screen-reader-text">%4$s</span>%5$s</button></div>',
	esc_attr( $track_id ),
	esc_html__( 'Previous quotes', 'floe' ),
	Floe\icon( 'arrow-left' ),
	esc_html__( 'Next quotes', 'floe' ),
	Floe\icon( 'arrow' )
) : '';
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base', 'class' => $slider ? 'testimonials--slider' : 'testimonials--grid' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="testimonials__inner">
		<?php
		echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'aside'         => $controls,
			)
		);
		?>
		<div class="testimonials__track" id="<?php echo esc_attr( $track_id ); ?>"<?php echo $slider ? ' role="region" aria-roledescription="carousel" aria-label="' . esc_attr__( 'Quotes', 'floe' ) . '" tabindex="0"' : ''; ?>>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
