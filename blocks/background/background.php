<?php
/**
 * Background: a full-width band of colour behind the blocks inside it. Text,
 * links and buttons switch to their light versions on dark colours by
 * themselves; "Force light text" makes them light on any colour. With Auto
 * spacing (on by default) the band has the section space above its first
 * block, and its last block's margin gives the same below. Turn it off to set
 * the space with Spacing blocks inside instead.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( '' === trim( $content ) ) {
	return;
}

$surface = in_array( $attributes['surface'], array( 'subtle', 'tint', 'inverse', 'accent' ), true ) ? $attributes['surface'] : 'subtle';
$classes = array_filter(
	array(
		$attributes['autoSpacing'] ? 'background--spaced' : '',
		$attributes['lightText'] ? 'force-light-text' : '',
	)
);
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $surface, 'class' => $classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="background__inner"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</section>
