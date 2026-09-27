<?php
/**
 * Steps: a numbered, connected sequence of 3–5 steps. The heading group comes
 * from an Intro block holding the Steps; step titles are one level below its
 * heading (block context).
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
		<ol class="steps__list"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ol>
	</div>
</section>
