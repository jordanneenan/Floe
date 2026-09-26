<?php
/**
 * Posts: latest, category or hand-picked posts as Post cards (1–12). The
 * current post is always excluded.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$count = max( 1, min( 12, (int) $attributes['count'] ) );
$args  = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $count,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
	'post__not_in'        => array_filter( array( get_the_ID() ) ),
);

if ( 'manual' === $attributes['source'] ) {
	$ids = array_values( array_diff( array_map( 'absint', (array) $attributes['posts'] ), $args['post__not_in'] ) );
	if ( ! $ids ) {
		return;
	}
	$args['post__in']       = array_slice( $ids, 0, 12 );
	$args['orderby']        = 'post__in';
	$args['posts_per_page'] = count( $args['post__in'] );
	unset( $args['post__not_in'] );
} elseif ( 'category' === $attributes['source'] && $attributes['category'] ) {
	$args['cat'] = (int) $attributes['category'];
}

$query = new WP_Query( $args );
if ( ! $query->have_posts() ) {
	return;
}

$overrides = (array) $attributes['mediaOverrides'];
$level     = min( 4, (int) $attributes['headingLevel'] + 1 );
$cards     = '';
while ( $query->have_posts() ) {
	$query->the_post();
	$categories = get_the_category();
	$override   = $overrides[ get_the_ID() ] ?? array();
	$cards     .= Floe\component(
		'card',
		array(
			'variant'       => 'post',
			'title'         => esc_html( get_the_title() ),
			'text'          => esc_html( wp_trim_words( get_the_excerpt(), 22 ) ),
			'url'           => get_permalink(),
			'media'         => ! empty( $override['id'] ) ? (array) $override : array( 'id' => get_post_thumbnail_id() ),
			'category'      => $categories ? $categories[0]->name : '',
			'date'          => get_the_date(),
			'datetime'      => get_the_date( 'c' ),
			'heading_level' => $level,
		)
	);
}
wp_reset_postdata();
?>
<section <?php echo Floe\block_attributes( $block, array( 'surface' => 'base' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="posts__inner">
		<?php
		echo Floe\component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'section-header',
			array(
				'eyebrow'       => $attributes['eyebrow'],
				'heading'       => $attributes['heading'],
				'heading_level' => $attributes['headingLevel'],
				'action'        => Floe\Components\button_args_from_link( $attributes['action'], array( 'style' => 'secondary' ) ),
			)
		);
		?>
		<div class="posts__grid"><?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</div>
</section>
