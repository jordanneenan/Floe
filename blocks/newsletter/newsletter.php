<?php
/**
 * Newsletter: tint panel with copy on the left and a form slot on the right
 * (the site's mailing provider). With no form, an optional button turns it
 * into a slimmer CTA.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$form   = trim( $content );
$action = $form ? '' : component( 'button', Floe\Components\button_args_from_link( $attributes['action'] ) );
$note   = trim( (string) $attributes['note'] );
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="newsletter__inner">
		<div class="newsletter__panel surface-tint">
			<?php
			echo component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'section-header',
				array(
					'layout'        => 'stacked',
					'eyebrow'       => $attributes['eyebrow'],
					'heading'       => $attributes['heading'],
					'heading_level' => $attributes['headingLevel'],
					'intro'         => $attributes['intro'],
				)
			);
			?>
			<?php if ( $form || $action || $note ) : ?>
				<div class="newsletter__side">
					<?php if ( $form ) : ?>
						<div class="newsletter__form form-slot"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>
					<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( $note ) : ?>
						<p class="newsletter__note"><?php echo wp_kses_post( $note ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
