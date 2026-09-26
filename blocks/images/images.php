<?php
/**
 * Images: optional heading group, then rows of one to three media slots.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}
$header = Floe\component(
	'section-header',
	array(
		'eyebrow'       => $attributes['eyebrow'],
		'heading'       => $attributes['heading'],
		'heading_level' => $attributes['headingLevel'],
	)
);
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base', 'class' => 'images--' . ( 'natural' === $attributes['fit'] ? 'natural' : 'fill' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="images__inner">
		<?php echo $header; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="images__rows"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</section>
