<?php
/**
 * Page Banner: breadcrumb (automatic, can be switched off), H1 (Display),
 * intro, optional link, optional media. Tint surface by default.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$heading = trim( (string) $attributes['heading'] ) ?: esc_html( get_the_title() );
$intro   = trim( (string) $attributes['intro'] );
$link    = component( 'button', Floe\Components\button_args_from_link( $attributes['link'], array( 'style' => 'link' ) ) );
$media   = ! empty( $attributes['showMedia'] ) ? component(
	'media',
	(array) $attributes['media'] + array(
		'ratio' => '1248/440',
		'sizes' => '(min-width: 1440px) 1248px, 92vw',
		'class' => 'page-banner__media',
	)
) : '';
$crumbs  = ! empty( $attributes['showBreadcrumb'] ) ? component( 'breadcrumb' ) : '';
$classes = array( $media ? 'has-media' : 'no-media' );
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'], 'class' => $classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="page-banner__inner">
		<?php echo $crumbs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="page-banner__title-row">
			<h1 class="page-banner__heading"><?php echo wp_kses_post( $heading ); ?></h1>
			<?php if ( $intro || $link ) : ?>
				<div class="page-banner__supporting">
					<?php if ( $intro ) : ?>
						<p class="page-banner__intro"><?php echo wp_kses_post( $intro ); ?></p>
					<?php endif; ?>
					<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		</div>
		<?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
