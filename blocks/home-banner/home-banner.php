<?php
/**
 * Home Banner: eyebrow, H1 (Display XL), body, primary and optional secondary
 * action, full-width media. Headline-first layout.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$heading = trim( (string) $attributes['heading'] ) ?: esc_html( get_the_title() );
$body    = trim( (string) $attributes['body'] );
$actions = component( 'button', Floe\Components\button_args_from_link( $attributes['primaryAction'], array( 'style' => 'primary' ) ) )
	. component( 'button', Floe\Components\button_args_from_link( $attributes['secondaryAction'], array( 'style' => 'secondary', 'arrow' => false ) ) );
$media   = component(
	'media',
	(array) $attributes['media'] + array(
		'ratio'  => '1248/600',
		'radius' => 'xl',
		'sizes'  => '(min-width: 1440px) 1248px, 92vw',
		'class'  => 'home-banner__media',
	)
);
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="home-banner__inner">
		<div class="home-banner__intro">
			<div class="home-banner__heading-group">
				<?php echo component( 'eyebrow', array( 'text' => $attributes['eyebrow'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h1 class="home-banner__heading"><?php echo wp_kses_post( $heading ); ?></h1>
			</div>
			<?php if ( $body || $actions ) : ?>
				<div class="home-banner__supporting">
					<?php if ( $body ) : ?>
						<p class="home-banner__body"><?php echo wp_kses_post( $body ); ?></p>
					<?php endif; ?>
					<?php if ( $actions ) : ?>
						<div class="home-banner__actions"><?php echo $actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
