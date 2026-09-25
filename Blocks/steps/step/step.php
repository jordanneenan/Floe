<?php
/**
 * Step (child of Steps). The number is added in order by CSS.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$title = trim( (string) $attributes['title'] );
$text  = trim( (string) $attributes['text'] );
if ( '' === $title && '' === $text ) {
	return;
}
$level = min( 4, (int) ( $block->context['floe/headingLevel'] ?? 2 ) + 1 );
?>
<li <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<span class="step__marker" aria-hidden="true"></span>
	<div class="step__body">
		<?php if ( $title ) : ?>
			<h<?php echo (int) $level; ?> class="step__title"><?php echo wp_kses_post( $title ); ?></h<?php echo (int) $level; ?>>
		<?php endif; ?>
		<?php if ( $text ) : ?>
			<p class="step__text"><?php echo wp_kses_post( $text ); ?></p>
		<?php endif; ?>
	</div>
</li>
