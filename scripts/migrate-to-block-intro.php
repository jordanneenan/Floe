<?php
/**
 * One-off content migration to Block intros and Background blocks. Run once
 * per site after deploying the theme version that adds them:
 *
 *   wp eval-file scripts/migrate-to-block-intro.php               # dry run: lists changes
 *   wp eval-file scripts/migrate-to-block-intro.php apply         # saves them
 *   wp eval-file scripts/migrate-to-block-intro.php apply only=12,34
 *
 * It converts content from either earlier version:
 *
 * - Blocks that carried their own eyebrow, heading, intro and button (Cards,
 *   Posts, FAQ…) get a Block intro with those fields. FAQ and Document
 *   Download get the two-column layout, holding the block; the rest get a
 *   stacked Block intro followed by the block.
 * - An Intro block (floe/intro) becomes a Block intro: stacked, with its block
 *   moved out after it, or in two columns, holding its block.
 * - Blocks that had their own background colour (a surface, including the
 *   Subtle default of Steps and Testimonial and the Inverse default of Video),
 *   and Intros with one, go into a Background block of that colour. Next to
 *   each other, blocks of the same colour share one Background.
 *
 * Only heading fields a block used to show are moved (an intro saved on Posts,
 * which never displayed it, is dropped). Running it again changes nothing.
 * The banners, CTA panels and the Newsletter panel keep their colours.
 *
 * @package Floe
 */

defined( 'ABSPATH' ) || exit;

// Blocks that had a heading group: [ two columns?, the fields they showed ].
$floe_bi_headed = array(
	'floe/cards'             => array( false, array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/posts'             => array( false, array( 'eyebrow', 'heading', 'headingLevel', 'action' ) ),
	'floe/stats'             => array( false, array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/steps'             => array( false, array( 'eyebrow', 'heading', 'headingLevel', 'action' ) ),
	'floe/table'             => array( false, array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/team'              => array( false, array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/testimonials'      => array( false, array( 'eyebrow', 'heading', 'headingLevel' ) ),
	'floe/images'            => array( false, array( 'eyebrow', 'heading', 'headingLevel' ) ),
	'floe/video'             => array( false, array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
	'floe/faq'               => array( true, array( 'eyebrow', 'heading', 'headingLevel', 'intro', 'action' ) ),
	'floe/document-download' => array( true, array( 'eyebrow', 'heading', 'headingLevel', 'intro' ) ),
);
// Blocks that had a background colour, with their default.
$floe_bi_coloured = array(
	'floe/cards'       => 'base',
	'floe/article'     => 'base',
	'floe/image-copy'  => 'base',
	'floe/steps'       => 'subtle',
	'floe/testimonial' => 'subtle',
	'floe/video'       => 'inverse',
);
$floe_bi_fields = array( 'eyebrow', 'heading', 'headingLevel', 'intro', 'action' );

$floe_bi_args  = (array) ( $args ?? array() );
$floe_bi_apply = in_array( 'apply', $floe_bi_args, true );
$floe_bi_only  = array();
foreach ( $floe_bi_args as $floe_bi_arg ) {
	if ( str_starts_with( (string) $floe_bi_arg, 'only=' ) ) {
		$floe_bi_only = array_filter( array_map( 'absint', explode( ',', substr( $floe_bi_arg, 5 ) ) ) );
	}
}

foreach ( array( 'floe/block-intro', 'floe/background' ) as $floe_bi_name ) {
	if ( ! WP_Block_Type_Registry::get_instance()->is_registered( $floe_bi_name ) ) {
		WP_CLI::error( "$floe_bi_name is not registered. Deploy the theme first." );
	}
}

$floe_bi_block = static fn( string $name, array $attrs, array $inner = array() ): array => array(
	'blockName'    => $name,
	'attrs'        => $attrs,
	'innerBlocks'  => $inner,
	'innerHTML'    => '',
	'innerContent' => array_fill( 0, count( $inner ), null ),
);

// One top-level block → [ the blocks that replace it, its background colour
// ('' for none) ], or null to leave it as it is.
$floe_bi_convert = static function ( array $block ) use ( $floe_bi_headed, $floe_bi_coloured, $floe_bi_fields, $floe_bi_block ): ?array {
	$name  = (string) $block['blockName'];
	$attrs = $block['attrs'];

	if ( 'floe/intro' === $name ) {
		$header  = array_intersect_key( $attrs, array_flip( array_merge( array( 'anchor', 'hiddenFromVisitors' ), $floe_bi_fields ) ) );
		$columns = 'beside' === ( $attrs['layout'] ?? 'above' );
		$surface = (string) ( $attrs['surface'] ?? 'base' );
		$held    = array();
		foreach ( $block['innerBlocks'] as $inner ) {
			unset( $inner['attrs']['surface'] );
			// A hidden stacked Intro hid its block too; keep it hidden.
			if ( ! $columns && ! empty( $attrs['hiddenFromVisitors'] ) ) {
				$inner['attrs']['hiddenFromVisitors'] = true;
			}
			$held[] = $inner;
		}
	} elseif ( isset( $floe_bi_headed[ $name ] ) || isset( $floe_bi_coloured[ $name ] ) ) {
		// Attributes the block no longer has: its heading fields (only for
		// blocks that lost them) and its colour.
		$stale = array_intersect_key( $attrs, array_flip( isset( $floe_bi_headed[ $name ] ) ? array_merge( $floe_bi_fields, array( 'surface' ) ) : array( 'surface' ) ) );
		[ $columns, $shown ] = $floe_bi_headed[ $name ] ?? array( false, array() );
		$surface = isset( $floe_bi_coloured[ $name ] ) ? (string) ( $attrs['surface'] ?? $floe_bi_coloured[ $name ] ) : 'base';
		$header  = array();
		foreach ( $shown as $key ) {
			if ( isset( $attrs[ $key ] ) && '' !== $attrs[ $key ] && array() !== $attrs[ $key ] ) {
				$header[ $key ] = $attrs[ $key ];
			}
		}
		$has_text = static fn( $key ) => '' !== trim( wp_strip_all_tags( (string) ( $header[ $key ] ?? '' ) ) );
		if ( ! $has_text( 'eyebrow' ) && ! $has_text( 'heading' ) && ! $has_text( 'intro' ) && empty( $header['action']['url'] ) ) {
			$header = array();
		}
		if ( ! $header && 'base' === $surface && ! $stale ) {
			return null;
		}
		if ( $header && ! empty( $attrs['anchor'] ) ) {
			$header = array( 'anchor' => $attrs['anchor'] ) + $header;
			unset( $attrs['anchor'] );
		}
		$block['attrs'] = array_diff_key( $attrs, $stale );
		$held           = array( $block );
	} else {
		return null;
	}

	if ( isset( $header['headingLevel'] ) && 2 === (int) $header['headingLevel'] ) {
		unset( $header['headingLevel'] );
	}
	if ( ! $header ) {
		$out = $held;
	} elseif ( $columns ) {
		$out = array( $floe_bi_block( 'floe/block-intro', $header + array( 'layout' => 'columns' ), $held ) );
	} else {
		$out = array_merge( array( $floe_bi_block( 'floe/block-intro', $header ) ), $held );
	}
	return array( $out, 'base' === $surface ? '' : $surface );
};

$floe_bi_posts = get_posts(
	array(
		'post_type'      => array_values( array_diff( get_post_types(), array( 'revision', 'attachment', 'nav_menu_item' ) ) ),
		'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
		'posts_per_page' => -1,
		'post__in'       => $floe_bi_only,
		'no_found_rows'  => true,
	)
);

$floe_bi_count = 0;
foreach ( $floe_bi_posts as $floe_bi_post ) {
	if ( ! str_contains( $floe_bi_post->post_content, '<!-- wp:floe/' ) ) {
		continue;
	}
	$blocks  = array();
	$bands   = array(); // Indexes in $blocks of Backgrounds made in this run.
	$changes = array();
	foreach ( parse_blocks( $floe_bi_post->post_content ) as $block ) {
		$result = $floe_bi_convert( $block );
		if ( null === $result ) {
			$blocks[] = $block;
			continue;
		}
		[ $out, $surface ] = $result;
		$changes[]         = $block['blockName'] . ( $surface ? " (on $surface)" : '' );
		$last              = count( $blocks ) - 1;
		if ( '' === $surface ) {
			array_push( $blocks, ...$out );
		} elseif ( isset( $bands[ $last ] ) && $bands[ $last ] === $surface ) {
			// Right after a band of the same colour made in this run: join it.
			$blocks[ $last ]['innerBlocks']  = array_merge( $blocks[ $last ]['innerBlocks'], $out );
			$blocks[ $last ]['innerContent'] = array_fill( 0, count( $blocks[ $last ]['innerBlocks'] ), null );
		} else {
			$blocks[]           = $floe_bi_block( 'floe/background', 'subtle' === $surface ? array() : array( 'surface' => $surface ), $out );
			$bands[ $last + 1 ] = $surface;
		}
	}
	if ( ! $changes ) {
		continue;
	}

	++$floe_bi_count;
	WP_CLI::log( sprintf( '%s %d "%s": %s', $floe_bi_post->post_type, $floe_bi_post->ID, $floe_bi_post->post_title, implode( ', ', $changes ) ) );
	if ( $floe_bi_apply ) {
		wp_update_post(
			array(
				'ID'           => $floe_bi_post->ID,
				'post_content' => wp_slash( serialize_blocks( $blocks ) ),
			)
		);
	}
}

WP_CLI::success( sprintf( '%d %s %s.', $floe_bi_count, _n( 'post', 'posts', $floe_bi_count, 'floe' ), $floe_bi_apply ? 'updated' : 'would be updated (dry run; add "apply" to save)' ) );
