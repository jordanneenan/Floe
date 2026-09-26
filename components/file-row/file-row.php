<?php
/**
 * File row component: one downloadable file. The whole row is the link. Type
 * and size come from the attachment's metadata, never from the file system.
 *
 *   Floe\component( 'file-row', array( 'id' => 123, 'title' => '' ) )
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function file_row_type_code( string $extension ): string {
	$codes = array(
		'docx' => 'DOC',
		'xlsx' => 'XLS',
		'pptx' => 'PPT',
		'jpeg' => 'JPG',
	);
	$extension = strtolower( $extension );
	return $codes[ $extension ] ?? strtoupper( substr( $extension, 0, 4 ) );
}

function file_row( array $args ): string {
	$id  = absint( $args['id'] ?? 0 );
	$url = $id ? wp_get_attachment_url( $id ) : '';
	if ( ! $url ) {
		return '';
	}

	$title     = trim( (string) ( $args['title'] ?? '' ) );
	$title     = '' !== $title ? $title : get_the_title( $id );
	$file      = (string) get_post_meta( $id, '_wp_attached_file', true );
	$extension = strtolower( (string) pathinfo( $file ?: $url, PATHINFO_EXTENSION ) );
	$metadata  = wp_get_attachment_metadata( $id );
	$bytes     = is_array( $metadata ) && ! empty( $metadata['filesize'] ) ? (int) $metadata['filesize'] : 0;
	$meta      = strtoupper( $extension ) . ( $bytes ? ' · ' . size_format( $bytes, $bytes >= MB_IN_BYTES ? 1 : 0 ) : '' );

	return sprintf(
		'<a class="file-row" href="%1$s" download>'
		. '<span class="file-row__type" aria-hidden="true">%2$s</span>'
		. '<span class="file-row__info"><span class="file-row__title">%3$s</span><span class="file-row__meta">%4$s</span></span>'
		. '<span class="file-row__action" aria-hidden="true">%5$s</span>'
		. '</a>',
		esc_url( $url ),
		esc_html( file_row_type_code( $extension ) ),
		esc_html( $title ),
		esc_html( $meta ),
		\Floe\icon( 'download' )
	);
}
