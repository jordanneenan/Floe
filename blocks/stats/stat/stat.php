<?php
/**
 * Stat (child of Stats): value, optional accent unit, label.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$value = trim( (string) $attributes['value'] );
if ( '' === $value ) {
	return;
}
$unit  = trim( (string) $attributes['unit'] );
$label = trim( (string) $attributes['label'] );
?>
<div <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<p class="stat__figure">
		<span class="stat__value"><?php echo esc_html( $value ); ?></span><?php if ( $unit ) : ?><span class="stat__unit"><?php echo esc_html( $unit ); ?></span><?php endif; ?>
	</p>
	<?php if ( $label ) : ?>
		<p class="stat__label"><?php echo wp_kses_post( $label ); ?></p>
	<?php endif; ?>
</div>
