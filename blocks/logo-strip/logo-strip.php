<?php
/**
 * Logo strip: a label plus logos from the media library in one muted tone.
 * Logos with transparency (PNG, WebP, GIF, SVG) are recoloured to the muted
 * ink with a CSS mask; other files are shown in greyscale. Four to eight sit
 * in a row; as a ticker (the Carousel component) up to 24 scroll past.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$ticker = ! empty( $attributes['ticker'] );
$items  = '';
foreach ( array_slice( (array) $attributes['logos'], 0, $ticker ? 24 : 8 ) as $logo ) {
	$id  = absint( $logo['id'] ?? 0 );
	$src = $id ? wp_get_attachment_image_src( $id, 'mobile' ) : false;
	if ( ! $src ) {
		$src = $id ? wp_get_attachment_image_src( $id, 'full' ) : false;
	}
	if ( ! $src ) {
		continue;
	}
	$alt   = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) ?: get_the_title( $id );
	$ratio = ( $src[1] && $src[2] ) ? round( $src[1] / $src[2], 4 ) : 3;
	$mime  = (string) get_post_mime_type( $id );

	if ( in_array( $mime, array( 'image/png', 'image/webp', 'image/gif', 'image/svg+xml' ), true ) ) {
		$items .= sprintf(
			'<li class="logo-strip__item"><span class="logo-strip__logo logo-strip__logo--mask" role="img" aria-label="%1$s" style="--logo:url(%2$s);--ratio:%3$s"></span></li>',
			esc_attr( $alt ),
			esc_url( $src[0] ),
			esc_attr( (string) $ratio )
		);
	} else {
		$items .= sprintf(
			'<li class="logo-strip__item"><img class="logo-strip__logo logo-strip__logo--image" src="%1$s" alt="%2$s" style="--ratio:%3$s" loading="lazy" decoding="async"></li>',
			esc_url( $src[0] ),
			esc_attr( $alt ),
			esc_attr( (string) $ratio )
		);
	}
}

if ( '' === $items ) {
	return;
}
$label = trim( (string) $attributes['label'] );
$logos = $ticker ? Floe\component(
	'carousel',
	array(
		'mode'    => 'ticker',
		'list'    => true,
		'content' => $items,
		'label'   => $label ? wp_strip_all_tags( $label ) : __( 'Logos', 'floe' ),
		'class'   => 'logo-strip__ticker',
	)
) : '';
?>
<section <?php echo Floe\block_attributes( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="logo-strip__inner">
		<?php if ( $label ) : ?>
			<p class="logo-strip__label"><?php echo wp_kses_post( $label ); ?></p>
		<?php endif; ?>
		<?php if ( $logos ) : ?>
			<?php echo $logos; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output. ?>
		<?php else : ?>
			<ul class="logo-strip__logos"><?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></ul>
		<?php endif; ?>
	</div>
</section>
