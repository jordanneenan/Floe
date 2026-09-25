<?php
/** Page not found. */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main-content" class="site-main">
	<header class="page-title container">
		<h1><?php esc_html_e( 'Page not found', 'floe' ); ?></h1>
	</header>
	<div class="prose">
		<p><?php esc_html_e( 'The page you were looking for has moved or no longer exists.', 'floe' ); ?></p>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the home page', 'floe' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
