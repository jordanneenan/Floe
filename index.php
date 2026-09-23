<?php
/** Main fallback template. */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main-content" class="floe-content">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<?php if ( ! is_singular() ) : ?>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php endif; ?>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
