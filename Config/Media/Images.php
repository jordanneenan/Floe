<?php
/**
 * Image pipeline, following Made (Documentation/decisions.md D12).
 *
 * - WordPress generates only Floe's sizes: mobile 800, laptop 1440 and
 *   desktop 2400 wide (no crop), plus the thumbnail.
 * - JPEG quality 70. Uploads over 2560px are scaled by WordPress as usual
 *   (it keeps the original upload).
 * - PNGs with no transparent pixels are converted to JPEG on upload; the PNG
 *   files are deleted and the attachment switches to the JPEG. PNGs with
 *   transparency are left alone. This can't be undone for that image.
 *
 * Applies to new uploads. Delete this file to return to WordPress defaults.
 */

namespace Floe\Config\Media\Images;

defined( 'ABSPATH' ) || exit;

const SIZES        = array(
	'mobile'  => 800,
	'laptop'  => 1440,
	'desktop' => 2400,
);
const JPEG_QUALITY = 70;

function register_sizes(): void {
	foreach ( SIZES as $name => $width ) {
		add_image_size( $name, $width, 0, false );
	}
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_sizes' );

/** Generate only Floe's sizes plus the thumbnail. */
function limit_sizes( array $sizes ): array {
	return array_intersect_key( $sizes, array_flip( array_merge( array( 'thumbnail' ), array_keys( SIZES ) ) ) );
}
add_filter( 'intermediate_image_sizes_advanced', __NAMESPACE__ . '\\limit_sizes' );

function jpeg_quality( $quality, $context = '' ) {
	return JPEG_QUALITY;
}
add_filter( 'jpeg_quality', __NAMESPACE__ . '\\jpeg_quality', 10, 2 );

add_filter( 'big_image_size_threshold', static fn() => 2560 );

/** Readable names in the editor's image size picker. */
function size_names( array $names ): array {
	return array_merge(
		$names,
		array(
			'mobile'  => __( 'Mobile (800px)', 'floe' ),
			'laptop'  => __( 'Laptop (1440px)', 'floe' ),
			'desktop' => __( 'Desktop (2400px)', 'floe' ),
		)
	);
}
add_filter( 'image_size_names_choose', __NAMESPACE__ . '\\size_names' );

/**
 * True when a PNG has at least one pixel that isn't fully opaque. Checks a
 * scaled-down copy, which is fast and still catches transparent areas.
 */
function png_has_transparency( string $file ): bool {
	if ( class_exists( '\\Imagick' ) && \Imagick::queryFormats( 'PNG' ) ) {
		try {
			$image = new \Imagick( $file );
			if ( ! $image->getImageAlphaChannel() ) {
				return false;
			}
			$image->thumbnailImage( min( 400, $image->getImageWidth() ), 0 );
			$pixels = $image->exportImagePixels( 0, 0, $image->getImageWidth(), $image->getImageHeight(), 'A', \Imagick::PIXEL_CHAR );
			foreach ( $pixels as $alpha ) {
				if ( $alpha < 255 ) {
					return true;
				}
			}
			return false;
		} catch ( \Exception $e ) {
			return true; // Unsure: leave the PNG alone.
		}
	}

	$source = function_exists( 'imagecreatefrompng' ) ? @imagecreatefrompng( $file ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( ! $source ) {
		return true;
	}
	$width  = imagesx( $source );
	$height = imagesy( $source );
	$scale  = min( 1, 400 / max( 1, $width ) );
	$w      = max( 1, (int) round( $width * $scale ) );
	$h      = max( 1, (int) round( $height * $scale ) );
	$sample = imagecreatetruecolor( $w, $h );
	imagealphablending( $sample, false );
	imagesavealpha( $sample, true );
	imagecopyresampled( $sample, $source, 0, 0, 0, 0, $w, $h, $width, $height );
	imagedestroy( $source );
	for ( $x = 0; $x < $w; $x++ ) {
		for ( $y = 0; $y < $h; $y++ ) {
			if ( ( ( imagecolorat( $sample, $x, $y ) >> 24 ) & 0x7F ) > 0 ) {
				imagedestroy( $sample );
				return true;
			}
		}
	}
	imagedestroy( $sample );
	return false;
}

/** Convert opaque PNG uploads to JPEG. */
function convert_png( array $metadata, int $attachment_id ): array {
	if ( 'image/png' !== get_post_mime_type( $attachment_id ) ) {
		return $metadata;
	}
	$file = get_attached_file( $attachment_id );
	if ( ! $file || ! is_file( $file ) ) {
		return $metadata;
	}
	// Check the original upload when WordPress has scaled it.
	$original = wp_get_original_image_path( $attachment_id );
	if ( png_has_transparency( $original && is_file( $original ) ? $original : $file ) ) {
		return $metadata;
	}

	$editor = wp_get_image_editor( $original && is_file( $original ) ? $original : $file );
	if ( is_wp_error( $editor ) ) {
		return $metadata;
	}
	$editor->set_quality( JPEG_QUALITY );
	$directory = dirname( $file );
	$jpeg_name = wp_unique_filename( $directory, preg_replace( '/\.png$/i', '.jpg', basename( $original ?: $file ) ) );
	$saved     = $editor->save( $directory . '/' . $jpeg_name, 'image/jpeg' );
	if ( is_wp_error( $saved ) || empty( $saved['path'] ) ) {
		return $metadata;
	}

	// Delete the PNG, its sizes and any scaled copy.
	$old_files = array( $file );
	if ( $original ) {
		$old_files[] = $original;
	}
	foreach ( (array) ( $metadata['sizes'] ?? array() ) as $size ) {
		if ( ! empty( $size['file'] ) ) {
			$old_files[] = $directory . '/' . $size['file'];
		}
	}
	foreach ( array_unique( $old_files ) as $old ) {
		if ( is_file( $old ) && $old !== $saved['path'] ) {
			wp_delete_file( $old );
		}
	}

	update_attached_file( $attachment_id, $saved['path'] );
	wp_update_post(
		array(
			'ID'             => $attachment_id,
			'post_mime_type' => 'image/jpeg',
		)
	);

	remove_filter( 'wp_generate_attachment_metadata', __NAMESPACE__ . '\\convert_png', 20 );
	$metadata = wp_generate_attachment_metadata( $attachment_id, $saved['path'] );
	add_filter( 'wp_generate_attachment_metadata', __NAMESPACE__ . '\\convert_png', 20, 2 );

	return is_array( $metadata ) ? $metadata : array();
}
add_filter( 'wp_generate_attachment_metadata', __NAMESPACE__ . '\\convert_png', 20, 2 );
