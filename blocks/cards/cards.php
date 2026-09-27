<?php
/**
 * Cards: a grid of Feature, Icon or Media cards, two to four across. The
 * heading group is a Block intro above it.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}
$style   = in_array( $attributes['style'], array( 'feature', 'icon', 'media' ), true ) ? $attributes['style'] : 'feature';
$columns = max( 2, min( 4, (int) $attributes['columns'] ) );
?>
<section <?php echo Floe\block_attributes( $block, array( 'class' => array( 'cards--' . $style, 'cards--cols-' . $columns ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="cards__inner">
		<div class="cards__grid"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</section>
