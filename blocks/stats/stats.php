<?php
/**
 * Stats: section header plus two to four figures.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}

$wrapper = array(
	'surface' => 'base',
	'class'   => 'stats--count-' . min( 4, count( $block->inner_blocks ) ),
);
// stats.js ticks the figures up from zero as they scroll into view.
if ( ! empty( $attributes['countUp'] ) ) {
	$wrapper['data-count-up'] = '';
}
?>
<section <?php echo Floe\block_attributes( $block, $wrapper ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="stats__inner">
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
		<div class="stats__list"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</section>
