<?php
/**
 * Block patterns. WordPress registers each file in patterns/ from its header
 * (native). This file adds the "Floe pages" category and a helper that lets
 * a pattern point at a media-library image by its file name, so patterns
 * work on any site that has the preview imagery uploaded.
 */

namespace Floe\Config\Patterns;

defined( 'ABSPATH' ) || exit;

function categories(): void {
	register_block_pattern_category( 'floe-pages', array( 'label' => __( 'Floe pages', 'floe' ) ) );
}
add_action( 'init', __NAMESPACE__ . '\\categories' );

/**
 * The attachment ID for a media-library file by its name without extension
 * (e.g. "floe-hero"), or 0 if it hasn't been uploaded.
 */
function image_id( string $name ): int {
	static $cache = array();
	if ( isset( $cache[ $name ] ) ) {
		return $cache[ $name ];
	}
	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small preview lookup, cached.
				array(
					'key'     => '_wp_attached_file',
					'value'   => '/' . $name . '(-scaled)?\.[a-z0-9]+$',
					'compare' => 'REGEXP',
				),
			),
		)
	);
	if ( ! $found ) {
		return 0; // Not cached, so an upload later in the same request is found.
	}
	$cache[ $name ] = (int) $found[0];
	return $cache[ $name ];
}

/** A media attribute for a file name: array( 'id' => 123 ), or array() if missing. */
function media( string $name ): array {
	$id = image_id( $name );
	return $id ? array( 'id' => $id ) : array();
}

/** A link attribute ({ label, url }) for a path on this site, e.g. link( 'Contact', '/contact/' ). */
function link( string $label, string $path ): array {
	$url = preg_match( '#^(https?:|mailto:|tel:|\#)#', $path ) ? $path : home_url( $path );
	return array(
		'label' => $label,
		'url'   => $url,
	);
}

/**
 * Serialized block markup: block( 'floe/cta', array( … ) ) or, with inner
 * content, block( 'floe/cards', array( … ), $inner ).
 */
function block( string $name, array $attributes = array(), string $inner = '' ): string {
	$name  = str_starts_with( $name, 'core/' ) ? substr( $name, 5 ) : $name;
	$attrs = $attributes ? serialize_block_attributes( $attributes ) . ' ' : '';
	return '' === $inner
		? "<!-- wp:{$name} {$attrs}/-->\n"
		: "<!-- wp:{$name} {$attrs}-->\n{$inner}\n<!-- /wp:{$name} -->\n";
}

/** Core block markup that matches each block's saved HTML exactly. */
function heading( string $text, int $level = 2 ): string {
	return block( 'core/heading', 2 === $level ? array() : array( 'level' => $level ), "<h{$level} class=\"wp-block-heading\">{$text}</h{$level}>" );
}

function paragraph( string $text, string $style = '' ): string {
	return block( 'core/paragraph', $style ? array( 'className' => 'is-style-' . $style ) : array(), $style ? "<p class=\"is-style-{$style}\">{$text}</p>" : "<p>{$text}</p>" );
}

function items( array $items ): string {
	$html = '';
	foreach ( $items as $item ) {
		$html .= block( 'core/list-item', array(), "<li>{$item}</li>" );
	}
	return block( 'core/list', array(), "<ul class=\"wp-block-list\">{$html}</ul>" );
}

function pullquote( string $text, string $citation ): string {
	return block( 'core/pullquote', array(), "<figure class=\"wp-block-pullquote\"><blockquote><p>{$text}</p><cite>{$citation}</cite></blockquote></figure>" );
}

function details( string $question, string $answer, bool $open = false ): string {
	return block(
		'core/details',
		$open ? array( 'showContent' => true ) : array(),
		'<details class="wp-block-details"' . ( $open ? ' open' : '' ) . "><summary>{$question}</summary>" . paragraph( $answer ) . '</details>'
	);
}

/** A core Table: first row is the header. Cells are plain text (✓ and — become icons in the Floe Table). */
function table( array $rows ): string {
	$head = array_shift( $rows );
	$html = '<figure class="wp-block-table"><table><thead><tr>';
	foreach ( $head as $cell ) {
		$html .= '<th>' . $cell . '</th>';
	}
	$html .= '</tr></thead><tbody>';
	foreach ( $rows as $row ) {
		$html .= '<tr>';
		foreach ( $row as $cell ) {
			$html .= '<td>' . $cell . '</td>';
		}
		$html .= '</tr>';
	}
	return block( 'core/table', array( 'hasFixedLayout' => false ), $html . '</tbody></table></figure>' );
}
