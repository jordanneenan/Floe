<?php
/**
 * Block intro: an eyebrow, heading, intro and button. Stacked (the default),
 * it's a heading group on its own and the block it introduces follows as the
 * next block, with a smaller gap between them. In two columns it holds one
 * block on the right (FAQ, Document Download…) and the heading group sits in
 * a narrow column on the left.
 *
 * Holding a block, it renders nothing if that block renders nothing (no
 * files, or hidden from this visitor). The held block takes the Block
 * intro's spacing (see Floe\block_attributes()).
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$columns = 'columns' === $attributes['layout'];
// "hiddenChildren": blocks taken out before rendering because this viewer
// can't see them (Hidden from visitors). It still counts as holding them.
$held = $columns && ( count( $block->inner_blocks ) > 0 || ! empty( $block->parsed_block['hiddenChildren'] ) );

if ( $held && '' === trim( $content ) ) {
	return;
}

$header = Floe\component(
	'section-header',
	array(
		'layout'        => $columns ? 'stacked' : 'split',
		'eyebrow'       => $attributes['eyebrow'],
		'heading'       => $attributes['heading'],
		'heading_level' => $attributes['headingLevel'],
		'intro'         => $attributes['intro'],
		'action'        => Floe\Components\button_args_from_link( $attributes['action'], array( 'style' => $columns ? 'link' : 'secondary' ) ),
	)
);

if ( '' === $header && ! $held ) {
	return;
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'class' => 'block-intro--' . ( $columns ? 'columns' : 'stacked' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="block-intro__inner">
		<?php echo $header; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $held ) : ?>
			<div class="block-intro__block"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
	</div>
</section>
