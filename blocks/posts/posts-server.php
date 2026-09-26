<?php
/**
 * Posts block, server side: the query, the card markup and the REST route
 * that load more and the filters use (GET /wp-json/floe/v1/posts). Built in,
 * so no Ajax Load More plugin is needed. Only public, published content is
 * ever returned.
 */

namespace Floe\Blocks\Posts;

defined( 'ABSPATH' ) || exit;

const MAX_PER_PAGE = 24;
const MAX_ALL      = 100;

/** A post type visitors may list: public and viewable, not attachments. */
function valid_post_type( string $type ): bool {
	$object = get_post_type_object( $type );
	return $object && 'attachment' !== $type && is_post_type_viewable( $object );
}

/** A public taxonomy attached to the post type, or ''. */
function valid_taxonomy( string $taxonomy, string $type ): string {
	if ( '' === $taxonomy || ! taxonomy_exists( $taxonomy ) || ! is_object_in_taxonomy( $type, $taxonomy ) ) {
		return '';
	}
	$object = get_taxonomy( $taxonomy );
	return $object && $object->public ? $taxonomy : '';
}

/**
 * Run the listing query. Fetches one extra post to know whether there are
 * more, so no total count is needed (no_found_rows).
 *
 * @return array{0: \WP_Post[], 1: bool} Posts and whether more exist.
 */
function query( array $args ): array {
	$type     = valid_post_type( (string) ( $args['type'] ?? 'post' ) ) ? (string) $args['type'] : 'post';
	$all      = ! empty( $args['all'] );
	$per_page = $all ? MAX_ALL : max( 1, min( MAX_PER_PAGE, (int) ( $args['per_page'] ?? 3 ) ) );
	$page     = max( 1, (int) ( $args['page'] ?? 1 ) );

	$query_args = array(
		'post_type'           => $type,
		'post_status'         => 'publish',
		'posts_per_page'      => $per_page + 1,
		'offset'              => ( $page - 1 ) * $per_page,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'post__not_in'        => array_filter( array_map( 'absint', (array) ( $args['exclude'] ?? array() ) ) ),
	);

	$taxonomy = valid_taxonomy( (string) ( $args['taxonomy'] ?? '' ), $type );
	$term     = absint( $args['term'] ?? 0 );
	if ( $taxonomy && $term ) {
		$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => $taxonomy,
				'terms'    => $term,
			),
		);
	}

	$posts    = get_posts( $query_args );
	$has_more = ! $all && count( $posts ) > $per_page;
	return array( array_slice( $posts, 0, $per_page ), $has_more );
}

/** Top-level terms of a taxonomy that have content, for the filter buttons. */
function filter_terms( string $taxonomy ): array {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'parent'     => 0,
			'hide_empty' => true,
		)
	);
	return is_array( $terms ) ? $terms : array();
}

/** Card markup for a list of posts. */
function cards( array $posts, array $args ): string {
	$overrides = (array) ( $args['overrides'] ?? array() );
	$level     = max( 2, min( 4, (int) ( $args['level'] ?? 3 ) ) );
	$type      = (string) ( $args['type'] ?? 'post' );
	$taxonomy  = valid_taxonomy( (string) ( $args['taxonomy'] ?? '' ), $type );
	if ( ! $taxonomy && is_object_in_taxonomy( $type, 'category' ) ) {
		$taxonomy = 'category';
	}

	$html = '';
	foreach ( $posts as $post ) {
		$terms    = $taxonomy ? get_the_terms( $post, $taxonomy ) : false;
		$override = (array) ( $overrides[ $post->ID ] ?? array() );
		$html    .= \Floe\component(
			'card',
			array(
				'variant'       => 'post',
				'title'         => esc_html( get_the_title( $post ) ),
				'text'          => esc_html( wp_trim_words( get_the_excerpt( $post ), 22 ) ),
				'url'           => get_permalink( $post ),
				'media'         => ! empty( $override['id'] ) ? $override : array( 'id' => get_post_thumbnail_id( $post ) ),
				'category'      => is_array( $terms ) && $terms ? $terms[0]->name : '',
				'date'          => get_the_date( '', $post ),
				'datetime'      => get_the_date( 'c', $post ),
				'heading_level' => $level,
			)
		);
	}
	return $html;
}

function rest_response( \WP_REST_Request $request ): \WP_REST_Response {
	$args = array(
		'type'      => (string) $request['type'],
		'per_page'  => (int) $request['per_page'],
		'all'       => (bool) $request['all'],
		'page'      => (int) $request['page'],
		'taxonomy'  => (string) $request['taxonomy'],
		'term'      => (int) $request['term'],
		'exclude'   => (array) $request['exclude'],
		'level'     => (int) $request['level'],
		'overrides' => array(),
	);
	foreach ( (array) json_decode( (string) $request['overrides'], true ) as $id => $media ) {
		if ( is_array( $media ) && ! empty( $media['id'] ) ) {
			$args['overrides'][ absint( $id ) ] = array( 'id' => absint( $media['id'] ) );
		}
	}
	[ $posts, $has_more ] = query( $args );
	return new \WP_REST_Response(
		array(
			'html'    => cards( $posts, $args ),
			'count'   => count( $posts ),
			'hasMore' => $has_more,
		)
	);
}

function register_route(): void {
	register_rest_route(
		'floe/v1',
		'/posts',
		array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => __NAMESPACE__ . '\\rest_response',
			'permission_callback' => '__return_true',
			'args'                => array(
				'type'      => array( 'type' => 'string', 'default' => 'post', 'validate_callback' => static fn( $value ) => valid_post_type( (string) $value ) ),
				'per_page'  => array( 'type' => 'integer', 'default' => 3, 'minimum' => 1, 'maximum' => MAX_PER_PAGE ),
				'all'       => array( 'type' => 'boolean', 'default' => false ),
				'page'      => array( 'type' => 'integer', 'default' => 1, 'minimum' => 1, 'maximum' => 1000 ),
				'taxonomy'  => array( 'type' => 'string', 'default' => '' ),
				'term'      => array( 'type' => 'integer', 'default' => 0, 'minimum' => 0 ),
				'exclude'   => array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'integer' ) ),
				'level'     => array( 'type' => 'integer', 'default' => 3, 'minimum' => 2, 'maximum' => 4 ),
				'overrides' => array( 'type' => 'string', 'default' => '' ),
			),
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\\register_route' );
