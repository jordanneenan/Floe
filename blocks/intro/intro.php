<?php
/**
 * Intro: the heading group for the block it holds. "above" puts the heading
 * group over the block (heading left, intro and button right); "beside" puts
 * it in a narrow left column with the block on the right. The held block
 * takes the Intro's surface and padding (see Floe\block_attributes()).
 *
 * Without a block it's a heading on its own. If its block renders nothing
 * (no posts, no files, hidden from this visitor…), the Intro renders nothing
 * either.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$beside = 'beside' === $attributes['layout'];
// "hiddenChildren": blocks taken out before rendering because this viewer
// can't see them (Hidden from visitors). The Intro still counts as holding.
$held = count( $block->inner_blocks ) > 0 || ! empty( $block->parsed_block['hiddenChildren'] );

if ( $held && '' === trim( $content ) ) {
	return;
}

$header = Floe\component(
	'section-header',
	array(
		'layout'        => $beside ? 'stacked' : 'split',
		'eyebrow'       => $attributes['eyebrow'],
		'heading'       => $attributes['heading'],
		'heading_level' => $attributes['headingLevel'],
		'intro'         => $attributes['intro'],
		'action'        => Floe\Components\button_args_from_link( $attributes['action'], array( 'style' => $beside ? 'link' : 'secondary' ) ),
	)
);

if ( '' === $header && ! $held ) {
	return;
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'], 'class' => 'intro--' . ( $beside ? 'beside' : 'above' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="intro__inner">
		<?php echo $header; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $held ) : ?>
			<div class="intro__block"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
	</div>
</section>
