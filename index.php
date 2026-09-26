<?php
/**
 * The one template. Pages and posts are built from blocks, so singular
 * content is printed as it is (with a title H1 only if no banner provides
 * one). Archives and search list their posts; a 404 says so.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main-content" class="site-main">
	<?php if ( is_singular() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			Floe\Includes\Templates\the_content();
		endwhile;
		?>
	<?php elseif ( is_404() ) : ?>
		<header class="page-title container">
			<h1><?php esc_html_e( 'Page not found', 'floe' ); ?></h1>
		</header>
		<div class="prose">
			<p><?php esc_html_e( 'The page you were looking for has moved or no longer exists.', 'floe' ); ?></p>
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the home page', 'floe' ); ?></a></p>
		</div>
	<?php else : ?>
		<header class="page-title container">
			<h1>
				<?php
				if ( is_search() ) {
					/* translators: %s: search terms. */
					printf( esc_html__( 'Search results for “%s”', 'floe' ), esc_html( get_search_query() ) );
				} elseif ( is_archive() ) {
					echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
				} else {
					esc_html_e( 'Latest posts', 'floe' );
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
	<?php endif; ?>
</main>
<?php
get_footer();
