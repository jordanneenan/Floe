<?php
/**
 * Testimonial: a single long quote with attribution and optional portrait.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$quote = trim( (string) $attributes['quote'] );
if ( '' === $quote ) {
	return;
}
$name     = trim( (string) $attributes['name'] );
$role     = trim( (string) $attributes['role'] );
$portrait = component(
	'media',
	(array) $attributes['portrait'] + array(
		'ratio'  => '1/1',
		'radius' => 'pill',
		'sizes'  => '56px',
		'class'  => 'testimonial__portrait',
	)
);
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => $attributes['surface'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<figure class="testimonial__inner">
		<span class="testimonial__mark" aria-hidden="true">&ldquo;</span>
		<blockquote class="testimonial__quote"><p><?php echo wp_kses_post( $quote ); ?></p></blockquote>
		<?php if ( $name || $role ) : ?>
			<figcaption class="testimonial__attribution">
				<?php echo $portrait; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="testimonial__who">
					<?php if ( $name ) : ?>
						<span class="testimonial__name"><?php echo wp_kses_post( $name ); ?></span>
					<?php endif; ?>
					<?php if ( $role ) : ?>
						<span class="testimonial__role"><?php echo wp_kses_post( $role ); ?></span>
					<?php endif; ?>
				</span>
			</figcaption>
		<?php endif; ?>
	</figure>
</section>
