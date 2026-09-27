<?php
/**
 * Cards: a grid of Feature or Media cards. The heading group comes from an
 * Intro block holding the Cards.
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
		<div class="cards__grid"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</section>
