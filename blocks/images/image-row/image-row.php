<?php
/**
 * Image row: one to three Media blocks. The column count follows the number
 * of items.
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
$classes = array( 'image-row--count-' . $count, empty( $attributes['gap'] ) ? 'image-row--no-gap' : '' );
?>
<div <?php echo Floe\block_attributes( $block, array( 'class' => $classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
