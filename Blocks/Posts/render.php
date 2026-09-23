<?php
/** Dynamic Posts section. */

defined( 'ABSPATH' ) || exit;

$mode  = in_array( $attributes['mode'] ?? 'latest', array( 'latest', 'category', 'selected' ), true ) ? $attributes['mode'] : 'latest';
$count = max( 1, min( 12, absint( $attributes['count'] ?? 3 ) ) );
$args  = array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $count, 'ignore_sticky_posts' => true );
if ( 'category' === $mode && ! empty( $attributes['categoryId'] ) ) {
	$args['cat'] = absint( $attributes['categoryId'] );
}
if ( 'selected' === $mode ) {
	$ids = array_filter( array_map( 'absint', explode( ',', (string) ( $attributes['selectedIds'] ?? '' ) ) ) );
	$args['post__in'] = $ids ? array_slice( $ids, 0, 12 ) : array( 0 );
	$args['orderby']  = 'post__in';
}
$query = new WP_Query( $args );
if ( ! $query->have_posts() ) {
	return;
}
echo '<section ' . get_block_wrapper_attributes( array( 'class' => 'floe-section floe-posts' ) ) . '><div class="floe-section__inner">' . \Floe\Blocks\eyebrow( $attributes ) . \Floe\Blocks\heading( $attributes, 'heading' ) . '<div class="floe-posts__grid">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Helpers escape content.
while ( $query->have_posts() ) {
	$query->the_post();
	$overrides = isset( $attributes['mediaOverrides'] ) && is_array( $attributes['mediaOverrides'] ) ? $attributes['mediaOverrides'] : array();
	$override = isset( $overrides[ get_the_ID() ] ) && is_array( $overrides[ get_the_ID() ] ) ? $overrides[ get_the_ID() ] : array();
	$visual = ! empty( $override['mediaId'] ) ? \Floe\Blocks\media( $override ) : '';
	if ( ! $visual ) {
		$thumbnail = get_the_post_thumbnail( get_the_ID(), 'large', array( 'loading' => 'lazy', 'class' => 'floe-media__image' ) );
		$visual = $thumbnail ? '<div class="floe-media">' . $thumbnail . '</div>' : '<div class="floe-media floe-media--empty"></div>';
	}
	$meta = get_the_date();
	$excerpt = get_the_excerpt();
	echo \Floe\Blocks\card( $meta, esc_html( get_the_title() ), esc_html( $excerpt ), $visual, get_permalink(), __( 'Read post', 'floe' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared card renderer escapes content.
}
wp_reset_postdata();
echo '</div></div></section>';
