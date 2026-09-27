<?php
/**
 * floe-sync: prints a JSON fingerprint of the site's content so two copies of
 * the site (floe.local and floewp.com) can be compared without copying either.
 *
 * Covers what a content sync moves: posts of every type with their meta,
 * terms (including menus) and settings. Leaves out what the sync never moves
 * or that changes by itself: users, enquiries, revisions, caches and cron.
 * The site's own address is replaced with {home} before hashing, so the same
 * content hashes the same on both sites.
 *
 * Run with: wp eval-file fingerprint.php
 */

global $wpdb;

$floe_host  = wp_parse_url( home_url(), PHP_URL_HOST );
$floe_regex = '#https?:(?://|\\\\/\\\\/)' . preg_quote( $floe_host, '#' ) . '#';

$floe_hash = static function ( $value ) use ( $floe_regex ): string {
	if ( is_string( $value ) && is_serialized( $value ) ) {
		$value = maybe_unserialize( $value );
	}
	if ( ! is_string( $value ) ) {
		$value = wp_json_encode( $value );
	}
	return md5( preg_replace( $floe_regex, '{home}', $value ) );
};

$floe_skip_types = array( 'revision', 'floe_enquiry', 'oembed_cache', 'customize_changeset', 'user_request' );
$floe_skip_meta  = array( '_edit_lock', '_edit_last', '_encloseme', '_pingme' );
$floe_skip_opts  = '/^(_transient_|_site_transient_|cron$|rewrite_rules$|recently_edited$|recovery_|auto_core_update|auto_updater|core_updater|db_upgraded$|can_compress_scripts$|https_detection_errors$|wp_force_deactivated_plugins$|_wp_suggested_policy_text_has_changed$|finished_updating_comment_type$|user_count$|admin_email_lifespan$|fresh_site$|theme_switched$|dismissed_update_core$|recently_activated$)/';

$floe_in = implode( ',', array_fill( 0, count( $floe_skip_types ), '%s' ) );

// phpcs:disable WordPress.DB
$floe_posts = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT ID, post_author, post_date_gmt, post_modified_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, post_parent, menu_order, post_type, post_mime_type
		FROM {$wpdb->posts} WHERE post_type NOT IN ($floe_in) AND post_status <> 'auto-draft' ORDER BY ID",
		$floe_skip_types
	),
	ARRAY_A
);

$floe_meta = array();
foreach ( $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} ORDER BY meta_id", ARRAY_A ) as $row ) {
	if ( ! in_array( $row['meta_key'], $floe_skip_meta, true ) ) {
		$floe_meta[ $row['post_id'] ][] = array( $row['meta_key'], $floe_hash( $row['meta_value'] ) );
	}
}

$floe_out = array(
	'home'    => home_url(),
	'taken'   => gmdate( 'Y-m-d H:i:s' ),
	'posts'   => array(),
	'terms'   => array(),
	'options' => array(),
);

foreach ( $floe_posts as $post ) {
	$id       = $post['ID'];
	$modified = $post['post_modified_gmt'];
	unset( $post['ID'], $post['post_modified_gmt'] );
	$meta = $floe_meta[ $id ] ?? array();
	sort( $meta );

	$floe_out['posts'][ $id ] = array(
		'type'     => $post['post_type'],
		'title'    => '' !== $post['post_title'] ? $post['post_title'] : '(no title)',
		'modified' => $modified,
		'hash'     => md5( $floe_hash( $post ) . wp_json_encode( $meta ) ),
	);
}

$floe_rel = array();
foreach ( $wpdb->get_results( "SELECT object_id, term_taxonomy_id FROM {$wpdb->term_relationships} ORDER BY object_id", ARRAY_A ) as $row ) {
	if ( isset( $floe_out['posts'][ $row['object_id'] ] ) ) {
		$floe_rel[ $row['term_taxonomy_id'] ][] = (int) $row['object_id'];
	}
}
$floe_termmeta = array();
foreach ( $wpdb->get_results( "SELECT term_id, meta_key, meta_value FROM {$wpdb->termmeta} ORDER BY meta_id", ARRAY_A ) as $row ) {
	$floe_termmeta[ $row['term_id'] ][] = array( $row['meta_key'], $floe_hash( $row['meta_value'] ) );
}
foreach ( $wpdb->get_results( "SELECT t.term_id, t.name, t.slug, tt.term_taxonomy_id, tt.taxonomy, tt.description, tt.parent FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id ORDER BY tt.term_taxonomy_id", ARRAY_A ) as $term ) {
	$objects = $floe_rel[ $term['term_taxonomy_id'] ] ?? array();
	sort( $objects );
	$floe_out['terms'][ $term['term_taxonomy_id'] ] = array(
		'type'  => $term['taxonomy'],
		'title' => $term['name'],
		'hash'  => md5( $floe_hash( $term ) . wp_json_encode( array( $objects, $floe_termmeta[ $term['term_id'] ] ?? array() ) ) ),
	);
}

foreach ( $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} ORDER BY option_name", ARRAY_A ) as $row ) {
	if ( ! preg_match( $floe_skip_opts, $row['option_name'] ) ) {
		$floe_out['options'][ $row['option_name'] ] = $floe_hash( $row['option_value'] );
	}
}
// phpcs:enable

echo wp_json_encode( $floe_out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), "\n";
