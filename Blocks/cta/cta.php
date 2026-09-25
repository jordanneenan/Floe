<?php
/**
 * CTA: a rounded panel on the page background. Ink (default) or Accent.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$level   = max( 2, min( 4, (int) $attributes['headingLevel'] ) );
$surface = 'accent' === $attributes['surface'] ? 'accent' : 'inverse';
$action  = component( 'button', Floe\Components\button_args_from_link( $attributes['action'] ) );
$note    = trim( (string) $attributes['note'] );
$body    = trim( (string) $attributes['body'] );
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="cta__inner">
		<div class="cta__panel surface-<?php echo esc_attr( $surface ); ?>">
			<div class="cta__copy">
				<?php echo component( 'eyebrow', array( 'text' => $attributes['eyebrow'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h<?php echo (int) $level; ?> class="cta__heading"><?php echo wp_kses_post( $attributes['heading'] ); ?></h<?php echo (int) $level; ?>>
				<?php if ( $body ) : ?>
					<p class="cta__body"><?php echo wp_kses_post( $body ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $action || $note ) : ?>
				<div class="cta__actions">
					<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( $note ) : ?>
						<p class="cta__note"><?php echo wp_kses_post( $note ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
