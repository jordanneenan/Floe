<?php
/**
 * CTA: one to three CTA panels in a row. The column count follows the number
 * of panels; a single panel lays out as a wide banner.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$count = min( 3, count( $block->inner_blocks ) );
if ( ! $count || '' === trim( $content ) ) {
	return;
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base', 'class' => 'cta--count-' . $count ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="cta__inner">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
