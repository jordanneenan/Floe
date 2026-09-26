<?php
/**
 * Cards: section header plus a grid of Feature or Media cards.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}
$style = 'media' === $attributes['style'] ? 'media' : 'feature';
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'], 'class' => 'cards--' . $style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="cards__inner">
		<?php
		echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'intro'         => $attributes['intro'],
			)
		);
		?>
		<div class="cards__grid"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</section>
