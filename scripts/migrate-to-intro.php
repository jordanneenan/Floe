<?php
/**
 * One-off content migration for the Intro block (run once per site, after
 * deploying the theme version that adds Intro):
 *
 *   wp eval-file scripts/migrate-to-intro.php          # dry run: lists changes
 *   wp eval-file scripts/migrate-to-intro.php apply    # saves them
 *   wp eval-file scripts/migrate-to-intro.php apply only=12,34   # just these posts
 *
 * Blocks used to carry their own eyebrow, heading, intro and button. Each
 * top-level block that still has any of those is wrapped in an Intro that
 * takes them over, with the block's HTML anchor and surface. FAQ and Document
 * Download get the two-column layout they had; the rest get "heading above".
 * Blocks with no heading are left alone. Safe to run again: blocks already
 * inside an Intro aren't top level, so they're skipped.
 *
 * @package Floe
 */

defined( 'ABSPATH' ) || exit;

// Block => [ Intro layout, the block's default surface, the heading fields it
// had ]. Only fields the block used to show move to the Intro; anything else
// saved on it (an intro Posts never displayed) is dropped, so nothing new
// appears on the page.
$floe_intro_map = array(
	'floe/cards'             => array( 'above', 'base', array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/posts'             => array( 'above', 'base', array( 'eyebrow', 'heading', 'headingLevel', 'action' ) ),
	'floe/stats'             => array( 'above', 'base', array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/steps'             => array( 'above', 'subtle', array( 'eyebrow', 'heading', 'headingLevel', 'action' ) ),
	'floe/table'             => array( 'above', 'base', array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/team'              => array( 'above', 'base', array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/testimonials'      => array( 'above', 'base', array( 'eyebrow', 'heading', 'headingLevel' ) ),
	'floe/images'            => array( 'above', 'base', array( 'eyebrow', 'heading', 'headingLevel' ) ),
	'floe/video'             => array( 'above', 'inverse', array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/faq'               => array( 'beside', 'base', array( 'eyebrow', 'heading', 'headingLevel', 'intro', 'action' ) ),
	'floe/document-download' => array( 'beside', 'base', array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
);
$floe_intro_fields = array( 'eyebrow', 'heading', 'headingLevel', 'intro', 'action' );
$floe_intro_apply = in_array( 'apply', (array) ( $args ?? array() ), true );
$floe_intro_only  = array();
foreach ( (array) ( $args ?? array() ) as $floe_intro_arg ) {
	if ( str_starts_with( (string) $floe_intro_arg, 'only=' ) ) {
		$floe_intro_only = array_filter( array_map( 'absint', explode( ',', substr( $floe_intro_arg, 5 ) ) ) );
	}
}

if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'floe/intro' ) ) {
	WP_CLI::error( 'The Intro block (floe/intro) is not registered. Deploy the theme first.' );
}

$floe_intro_wrap = static function ( array $block ) use ( $floe_intro_map, $floe_intro_fields ): ?array {
	if ( ! isset( $floe_intro_map[ $block['blockName'] ] ) ) {
		return null;
	}
	[ $layout, $surface, $shown ] = $floe_intro_map[ $block['blockName'] ];

	$attrs = $block['attrs'];
	$has   = false;
	foreach ( array_intersect( array( 'eyebrow', 'heading', 'intro' ), $shown ) as $key ) {
		$has = $has || '' !== trim( wp_strip_all_tags( (string) ( $attrs[ $key ] ?? '' ) ) );
	}
	$has = $has || ( in_array( 'action', $shown, true ) && ! empty( $attrs['action']['url'] ) );
	if ( ! $has ) {
		return null;
	}

	$intro = array();
	foreach ( $floe_intro_fields as $key ) {
		if ( in_array( $key, $shown, true ) && isset( $attrs[ $key ] ) && '' !== $attrs[ $key ] && array() !== $attrs[ $key ] ) {
			$intro[ $key ] = $attrs[ $key ];
		}
		unset( $attrs[ $key ] );
	}
	if ( isset( $intro['headingLevel'] ) && 2 === (int) $intro['headingLevel'] ) {
		unset( $intro['headingLevel'] );
	}
	if ( ! empty( $attrs['anchor'] ) ) {
		$intro = array( 'anchor' => $attrs['anchor'] ) + $intro;
		unset( $attrs['anchor'] );
	}
	if ( 'beside' === $layout ) {
		$intro['layout'] = 'beside';
	}
	$surface = (string) ( $attrs['surface'] ?? $surface );
	if ( 'base' !== $surface ) {
		$intro['surface'] = $surface;
	}

	$block['attrs'] = $attrs;
	return array(
		'blockName'    => 'floe/intro',
		'attrs'        => $intro,
		'innerBlocks'  => array( $block ),
		'innerHTML'    => '',
		'innerContent' => array( null ),
	);
};

$floe_intro_posts = get_posts(
	array(
		'post_type'      => array_values( array_diff( get_post_types(), array( 'revision', 'attachment', 'nav_menu_item' ) ) ),
		'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
		'posts_per_page' => -1,
		'post__in'       => $floe_intro_only,
		'no_found_rows'  => true,
	)
);

$floe_intro_count = 0;
foreach ( $floe_intro_posts as $floe_intro_post ) {
	if ( ! str_contains( $floe_intro_post->post_content, '<!-- wp:floe/' ) ) {
		continue;
	}
	$blocks  = parse_blocks( $floe_intro_post->post_content );
	$changed = array();
	foreach ( $blocks as $index => $block ) {
		$wrapped = $floe_intro_wrap( $block );
		if ( $wrapped ) {
			$blocks[ $index ] = $wrapped;
			$changed[]        = $block['blockName'] . ( ! empty( $block['attrs']['heading'] ) ? ' "' . wp_strip_all_tags( $block['attrs']['heading'] ) . '"' : '' );
		}
	}
	if ( ! $changed ) {
		continue;
	}
	++$floe_intro_count;
	WP_CLI::log( sprintf( '%s %d "%s": %s', $floe_intro_post->post_type, $floe_intro_post->ID, $floe_intro_post->post_title, implode( ', ', $changed ) ) );
	if ( $floe_intro_apply ) {
		wp_update_post(
			array(
				'ID'           => $floe_intro_post->ID,
				'post_content' => wp_slash( serialize_blocks( $blocks ) ),
			)
		);
	}
}

WP_CLI::success( sprintf( '%d %s %s.', $floe_intro_count, _n( 'post', 'posts', $floe_intro_count, 'floe' ), $floe_intro_apply ? 'updated' : 'would be updated (dry run; add "apply" to save)' ) );
