<?php
/** One downloadable document. */

defined( 'ABSPATH' ) || exit;

$id = absint( $attributes['fileId'] ?? 0 );
$url = $id ? wp_get_attachment_url( $id ) : false;
if ( ! $url ) {
	return;
}
$label = trim( wp_strip_all_tags( (string) ( $attributes['title'] ?? '' ) ) );
if ( ! $label ) {
	$label = get_the_title( $id );
}
$path = get_attached_file( $id );
$size = $path && is_file( $path ) ? size_format( filesize( $path ) ) : '';
$ext = strtoupper( pathinfo( $url, PATHINFO_EXTENSION ) );
$meta = trim( $ext . ( $size ? ' · ' . $size : '' ) );
echo '<a ' . get_block_wrapper_attributes( array( 'class' => 'floe-file-action' ) ) . ' href="' . esc_url( $url ) . '" download><span class="floe-file-action__type">' . esc_html( $ext ) . '</span><span class="floe-file-action__info"><strong>' . esc_html( $label ) . '</strong><small>' . esc_html( $meta ) . '</small></span><span class="floe-file-action__download">' . esc_html__( 'Download', 'floe' ) . ' ↓</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Values escaped above.
