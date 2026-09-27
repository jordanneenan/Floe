<?php
/**
 * floe-sync: keeps form enquiries on the site that received them.
 *
 * Enquiries are personal data sent to the live site, so a push must not
 * overwrite them with the local copy and a pull doesn't bring them down.
 *
 *   wp eval-file enquiries.php export          JSON of every enquiry on stdout
 *   wp eval-file enquiries.php purge           delete every enquiry
 *   wp eval-file enquiries.php import <file>   re-create enquiries from export
 *
 * Import keeps each enquiry's ID where it's free and gives it a new one where
 * the imported content has since used that ID.
 */

$floe_type = 'floe_enquiry';
$floe_cmd  = $args[0] ?? '';

$floe_ids = static function () use ( $floe_type ): array {
	return get_posts(
		array(
			'post_type'        => $floe_type,
			'post_status'      => 'any',
			'numberposts'      => -1,
			'fields'           => 'ids',
			'suppress_filters' => true,
		)
	);
};

if ( 'export' === $floe_cmd ) {
	$out = array();
	foreach ( $floe_ids() as $id ) {
		$out[] = array(
			'post' => get_post( $id, ARRAY_A ),
			'meta' => get_post_meta( $id ),
		);
	}
	echo wp_json_encode( $out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), "\n";
} elseif ( 'purge' === $floe_cmd ) {
	$ids = $floe_ids();
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	WP_CLI::log( sprintf( 'Removed %d enquiries.', count( $ids ) ) );
} elseif ( 'import' === $floe_cmd && isset( $args[1] ) ) {
	$items = json_decode( (string) file_get_contents( $args[1] ), true );
	if ( ! is_array( $items ) ) {
		WP_CLI::error( "Couldn't read enquiries from {$args[1]}." );
	}
	$moved = 0;
	foreach ( $items as $item ) {
		$post              = $item['post'];
		$post['import_id'] = $post['ID'];
		unset( $post['ID'], $post['guid'], $post['filter'], $post['ancestors'], $post['page_template'], $post['post_category'], $post['tags_input'] );
		$id = wp_insert_post( wp_slash( $post ), true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id );
		}
		$moved += (int) ( (int) $item['post']['ID'] !== $id );
		foreach ( $item['meta'] as $key => $values ) {
			foreach ( $values as $value ) {
				add_post_meta( $id, $key, wp_slash( maybe_unserialize( $value ) ) );
			}
		}
	}
	WP_CLI::log( sprintf( 'Restored %d enquiries (%d with a new ID).', count( $items ), $moved ) );
} else {
	WP_CLI::error( 'Usage: wp eval-file enquiries.php export|purge|import <file>' );
}
