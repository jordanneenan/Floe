<?php
/**
 * Gallery: images from the media library in an even grid, a mosaic (a large
 * image beside two small ones, alternating sides) or a masonry layout. Each
 * image opens in the Lightbox, where visitors can step through them all.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$layout   = in_array( $attributes['layout'], array( 'grid', 'mosaic', 'masonry' ), true ) ? $attributes['layout'] : 'grid';
$columns  = max( 2, min( 4, (int) $attributes['columns'] ) );
$group    = wp_unique_id( 'image-gallery-' );
$per_item = 'mosaic' === $layout ? 3 : $columns;
$sizes    = sprintf( '(min-width: 1440px) %dpx, (min-width: 768px) %dvw, 50vw', (int) ceil( 1248 / $per_item ), (int) ceil( 100 / $per_item ) );
$wide     = '(min-width: 1440px) 832px, (min-width: 768px) 66vw, 100vw';

$items = '';
$index = 0;
foreach ( (array) $attributes['images'] as $image ) {
	$id = absint( $image['id'] ?? 0 );
	if ( ! $id || ! wp_attachment_is_image( $id ) ) {
		continue;
	}
	// In the mosaic every third image (1st, 4th, 7th…) is the large one.
	$hero  = 'mosaic' === $layout && 0 === $index % 3;
	$media = Floe\component(
		'media',
		array(
			'id'     => $id,
			'ratio'  => 'grid' === $layout ? '4/3' : '',
			'cover'  => 'mosaic' === $layout,
			'radius' => 'md',
			'sizes'  => $hero ? $wide : $sizes,
		)
	);
	if ( '' === $media ) {
		continue;
	}
	if ( $attributes['lightbox'] ) {
		$media = Floe\component(
			'lightbox',
			array(
				'id'      => $id,
				'content' => $media,
				'group'   => $group,
			)
		) ?: $media;
	}
	$items .= '<li class="image-gallery__item">' . $media . '</li>';
	++$index;
}

if ( '' === $items ) {
	return;
}
?>
<section <?php echo Floe\block_attributes( $block, array( 'class' => 'image-gallery--' . $layout, 'style' => '--image-gallery-columns:' . $columns ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="image-gallery__inner">
		<ul class="image-gallery__grid"><?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- component output. ?></ul>
	</div>
</section>
