<?php
/**
 * Images: rows of one to three media slots. A heading group, if wanted, is a
 * Block intro above it or beside it in two columns.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'class' => 'images--' . ( 'natural' === $attributes['fit'] ? 'natural' : 'fill' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="images__inner">
		<div class="images__rows"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</section>
