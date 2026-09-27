<?php
/**
 * Team: people (portrait, name, role). The heading group is a Block intro
 * above it; names are H3 (block context "floe/headingLevel", default 2, plus
 * one).
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
<section <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="team__inner">
		<ul class="team__grid"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
	</div>
</section>
