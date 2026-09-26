<?php
/**
 * Contact: heading group, contact details (linked email and phone, address,
 * hours), optional map image, and a form slot for any form plugin.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

use function Floe\component;

defined( 'ABSPATH' ) || exit;

$rows  = array();
$email = sanitize_email( wp_strip_all_tags( (string) $attributes['email'] ) );
if ( $email ) {
	$rows[] = array( __( 'Email', 'floe' ), '<a class="contact__link contact__link--accent" href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>' );
}
$phone = trim( wp_strip_all_tags( (string) $attributes['phone'] ) );
if ( $phone ) {
	$rows[] = array( __( 'Phone', 'floe' ), '<a class="contact__link" href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>' );
}
if ( trim( (string) $attributes['address'] ) ) {
	$label  = trim( wp_strip_all_tags( (string) $attributes['addressLabel'] ) ) ?: __( 'Address', 'floe' );
	$rows[] = array( $label, wp_kses_post( $attributes['address'] ) );
}
if ( trim( (string) $attributes['hours'] ) ) {
	$rows[] = array( __( 'Hours', 'floe' ), wp_kses_post( $attributes['hours'] ) );
}

$map  = component(
	'media',
	(array) $attributes['map'] + array(
		'ratio' => '23/10',
		'sizes' => '(min-width: 1440px) 460px, (min-width: 768px) 40vw, 92vw',
		'class' => 'contact__map',
	)
);
$form = trim( $content );
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base', 'class' => $form ? 'has-form' : 'no-form' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="contact__inner">
		<div class="contact__details">
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
			<?php if ( $rows ) : ?>
				<dl class="contact__list">
					<?php foreach ( $rows as $row ) : ?>
						<div class="contact__row">
							<dt class="contact__label"><?php echo esc_html( $row[0] ); ?></dt>
							<dd class="contact__value"><?php echo $row[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
			<?php echo $map; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php if ( $form ) : ?>
			<div class="contact__form form-slot"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
	</div>
</section>
