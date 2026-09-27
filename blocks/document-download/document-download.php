<?php
/**
 * Document Download: a list of file rows. The heading group comes from an
 * Intro block holding the list (usually beside it).
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
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="document-download__inner">
		<ul class="document-download__files"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
	</div>
</section>
