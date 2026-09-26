<?php
/**
 * Quote card (child of Testimonials).
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$quote = trim( (string) $attributes['quote'] );
if ( '' === $quote ) {
	return;
}
$name     = trim( (string) $attributes['name'] );
$role     = trim( (string) $attributes['role'] );
$portrait = Floe\component(
	'media',
	(array) $attributes['portrait'] + array(
		'ratio'       => '1/1',
		'radius'      => 'pill',
		'placeholder' => true,
		'sizes'       => '48px',
		'class'       => 'quote__portrait',
	)
);
?>
<figure <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<blockquote class="quote__text"><p><?php echo wp_kses_post( $quote ); ?></p></blockquote>
	<?php if ( $name || $role ) : ?>
		<figcaption class="quote__attribution">
			<?php echo $portrait; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="quote__who">
				<span class="quote__name"><?php echo wp_kses_post( $name ); ?></span>
				<?php if ( $role ) : ?>
					<span class="quote__role"><?php echo wp_kses_post( $role ); ?></span>
				<?php endif; ?>
			</span>
		</figcaption>
	<?php endif; ?>
</figure>
