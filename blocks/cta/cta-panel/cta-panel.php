<?php
/**
 * CTA panel (child of CTA): rounded panel on the Ink, Accent, Tint or Subtle
 * surface with eyebrow, heading, body, action, optional note and image.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$heading = trim( (string) $attributes['heading'] );
$action  = component( 'button', Floe\Components\button_args_from_link( $attributes['action'] ) );
if ( '' === $heading && ! $action ) {
	return;
}
$level   = max( 2, min( 4, (int) $attributes['headingLevel'] ) );
$surface = in_array( $attributes['surface'], array( 'inverse', 'accent', 'tint', 'subtle' ), true ) ? $attributes['surface'] : 'inverse';
$body    = trim( (string) $attributes['body'] );
$note    = trim( (string) $attributes['note'] );
$media   = component(
	'media',
	(array) $attributes['media'] + array(
		'ratio' => '16/9',
		'sizes' => '(min-width: 1440px) 600px, (min-width: 768px) 45vw, 92vw',
		'class' => 'cta-panel__media',
	)
);
?>
<div <?php echo Floe\block_attributes( $block, array( 'surface' => $surface, 'class' => $media ? 'has-media' : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="cta-panel__copy">
		<?php echo component( 'eyebrow', array( 'text' => $attributes['eyebrow'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $heading ) : ?>
			<h<?php echo (int) $level; ?> class="cta-panel__heading"><?php echo wp_kses_post( $heading ); ?></h<?php echo (int) $level; ?>>
		<?php endif; ?>
		<?php if ( $body ) : ?>
			<p class="cta-panel__body"><?php echo wp_kses_post( $body ); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( $action || $note ) : ?>
		<div class="cta-panel__actions">
			<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $note ) : ?>
				<p class="cta-panel__note"><?php echo wp_kses_post( $note ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
