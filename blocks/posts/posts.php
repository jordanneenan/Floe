<?php
/**
 * Posts: section header, optional filter buttons, a grid of Post cards and an
 * optional "Load more" (button or automatic on scroll). Sources: the latest
 * of any post type, hand-picked posts, or manual entries (child blocks).
 * Filtering and loading more swap the cards in place through the block's
 * REST route without reloading the page. The chosen filter is kept in the
 * address (?filter=<term-slug>) so the view can be shared or bookmarked.
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

use Floe\Blocks\Posts;

defined( 'ABSPATH' ) || exit;

$source  = in_array( $attributes['source'], array( 'latest', 'picker', 'manual' ), true ) ? $attributes['source'] : 'latest';
$type    = Posts\valid_post_type( (string) $attributes['postType'] ) ? (string) $attributes['postType'] : 'post';
$level   = min( 4, (int) $attributes['headingLevel'] + 1 );
$current = get_the_ID();
$cards   = '';
$more    = false;
$filters = '';
$config  = array();

if ( 'manual' === $source ) {
	$cards = $content;
} elseif ( 'picker' === $source ) {
	$ids = array_values( array_diff( array_map( 'absint', (array) $attributes['posts'] ), array( $current ) ) );
	if ( $ids ) {
		$picked = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'publish',
				'post__in'       => array_slice( $ids, 0, 24 ),
				'orderby'        => 'post__in',
				'posts_per_page' => 24,
				'no_found_rows'  => true,
			)
		);
		$cards  = Posts\cards(
			$picked,
			array(
				'type'      => $type,
				'level'     => $level,
				'overrides' => (array) $attributes['mediaOverrides'],
			)
		);
	}
} else {
	$taxonomy   = Posts\valid_taxonomy( (string) $attributes['taxonomy'], $type );
	$filters_on = $taxonomy && $attributes['showFilters'];
	$terms      = $filters_on ? Posts\filter_terms( $taxonomy ) : array();

	// A filter chosen earlier is in the address (?filter=<term-slug>), so a
	// shared or bookmarked link opens already filtered.
	$active = 0;
	$wanted = isset( $_GET['filter'] ) ? sanitize_title( wp_unslash( (string) $_GET['filter'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only filter.
	foreach ( $terms as $term ) {
		if ( '' !== $wanted && $term->slug === $wanted ) {
			$active = (int) $term->term_id;
		}
	}

	$config = array(
		'type'      => $type,
		'per_page'  => max( 1, min( Posts\MAX_PER_PAGE, (int) $attributes['count'] ) ),
		'all'       => (bool) $attributes['showAll'],
		'taxonomy'  => $taxonomy,
		'term'      => $filters_on ? $active : absint( $attributes['term'] ),
		'exclude'   => array_filter( array( $current ) ),
		'level'     => $level,
		'overrides' => (array) $attributes['mediaOverrides'],
	);
	[ $posts, $more ] = Posts\query( $config + array( 'page' => 1 ) );
	$cards = Posts\cards( $posts, $config );

	if ( $terms ) {
		$buttons = sprintf(
			'<li><button type="button" class="posts__filter" data-term="0" data-slug="" aria-pressed="%s">%s</button></li>',
			$active ? 'false' : 'true',
			esc_html__( 'All', 'floe' )
		);
		foreach ( $terms as $term ) {
			$buttons .= sprintf(
				'<li><button type="button" class="posts__filter" data-term="%d" data-slug="%s" aria-pressed="%s">%s</button></li>',
				(int) $term->term_id,
				esc_attr( $term->slug ),
				(int) $term->term_id === $active ? 'true' : 'false',
				esc_html( $term->name )
			);
		}
		$label   = get_taxonomy( $taxonomy )->labels->name ?? __( 'Categories', 'floe' );
		$filters = '<ul class="posts__filters" aria-label="' . esc_attr( sprintf( /* translators: %s: taxonomy name. */ __( 'Filter by %s', 'floe' ), $label ) ) . '">' . $buttons . '</ul>';
	}
}

if ( '' === trim( $cards ) ) {
	return;
}

$load_more = 'latest' === $source && ! $attributes['showAll'] && 'none' !== $attributes['more'];
$data      = 'latest' === $source && ( $load_more || $filters )
	? array_merge(
		$config,
		array(
			'endpoint'  => rest_url( 'floe/v1/posts' ),
			'more'      => $attributes['more'],
			'overrides' => wp_json_encode( $config['overrides'] ),
		)
	)
	: array();
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) + ( $data ? array( 'data-posts' => wp_json_encode( $data ) ) : array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="posts__inner">
		<?php
		echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'intro'         => $attributes['intro'],
				'action'        => Floe\Components\button_args_from_link( $attributes['action'], array( 'style' => 'secondary' ) ),
			)
		);
		echo $filters; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div class="posts__grid" aria-live="polite" aria-busy="false"><?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php if ( $load_more ) : ?>
			<div class="posts__more"<?php echo $more ? '' : ' hidden'; ?>>
				<button type="button" class="posts__more-button button button--secondary"><?php esc_html_e( 'Load more', 'floe' ); ?></button>
			</div>
		<?php endif; ?>
		<p class="posts__status screen-reader-text" role="status"></p>
	</div>
</section>
