<?php
/**
 * Steps: section header plus a numbered, connected sequence of 3–5 steps.
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
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="steps__inner">
		<?php
		echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'action'        => Floe\Components\button_args_from_link( $attributes['action'], array( 'style' => 'secondary' ) ),
			)
		);
		?>
		<ol class="steps__list"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ol>
	</div>
</section>
