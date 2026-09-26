<?php
/**
 * Image + Copy: media left or right, eyebrow, heading, body, optional action.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$level  = max( 2, min( 4, (int) $attributes['headingLevel'] ) );
$body   = trim( (string) $attributes['body'] );
$action = component( 'button', Floe\Components\button_args_from_link( $attributes['action'] ) );
$media  = component(
	'media',
	(array) $attributes['media'] + array(
		'ratio' => '624/560',
		'sizes' => '(min-width: 1440px) 624px, (min-width: 768px) 50vw, 92vw',
		'class' => 'image-copy__media',
	)
);
$classes = array( 'image-copy--media-' . ( 'right' === $attributes['mediaPosition'] ? 'right' : 'left' ), $media ? 'has-media' : 'no-media' );
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'], 'class' => $classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="image-copy__inner">
		<?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="image-copy__copy">
			<?php echo component( 'eyebrow', array( 'text' => $attributes['eyebrow'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( trim( (string) $attributes['heading'] ) ) : ?>
				<h<?php echo (int) $level; ?> class="image-copy__heading"><?php echo wp_kses_post( $attributes['heading'] ); ?></h<?php echo (int) $level; ?>>
			<?php endif; ?>
			<?php if ( $body ) : ?>
				<p class="image-copy__body"><?php echo wp_kses_post( $body ); ?></p>
			<?php endif; ?>
			<?php if ( $action ) : ?>
				<div class="image-copy__action"><?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
