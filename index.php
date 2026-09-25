<?php
/** Lists of posts: the blog, archives and search results. */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main-content" class="site-main">
	<header class="page-title container">
		<h1>
			<?php
			if ( is_search() ) {
				/* translators: %s: search terms. */
				printf( esc_html__( 'Search results for “%s”', 'floe' ), esc_html( get_search_query() ) );
			} elseif ( is_archive() ) {
				echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
			} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
				echo esc_html( get_the_title( (int) get_option( 'page_for_posts' ) ) );
			} else {
				esc_html_e( 'Latest news', 'floe' );
			}
			?>
		</h1>
	</header>
	<div class="archive-list">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p class="text-small text-muted"><?php echo esc_html( get_the_date() ); ?></p>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'floe' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
