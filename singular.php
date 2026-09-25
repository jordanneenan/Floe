<?php
/** Pages, posts and any other single item. */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main-content" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		Floe\Config\Templates\the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
