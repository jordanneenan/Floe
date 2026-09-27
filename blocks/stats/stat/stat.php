<?php
/**
 * Stat (child of Stats): optional prefix, value, optional accent unit, label.
 * The prefix (£, $, ~) sits outside the value, so the count-up animation
 * only ticks the number.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$value = trim( (string) $attributes['value'] );
if ( '' === $value ) {
	return;
}
$prefix = trim( (string) $attributes['prefix'] );
$unit   = trim( (string) $attributes['unit'] );
$label  = trim( (string) $attributes['label'] );
?>
<div <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<p class="stat__figure">
		<?php if ( $prefix ) : ?><span class="stat__prefix"><?php echo esc_html( $prefix ); ?></span><?php endif; ?><span class="stat__value"><?php echo esc_html( $value ); ?></span><?php if ( $unit ) : ?><span class="stat__unit"><?php echo esc_html( $unit ); ?></span><?php endif; ?>
	</p>
	<?php if ( $label ) : ?>
		<p class="stat__label"><?php echo wp_kses_post( $label ); ?></p>
	<?php endif; ?>
</div>
