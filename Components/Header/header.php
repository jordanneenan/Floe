<?php
/** Shared site header. */

defined( 'ABSPATH' ) || exit;
?>
<header class="floe-site-header">
	<?php if ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php else : ?>
		<a class="floe-site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
	<?php endif; ?>
	<nav class="floe-site-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'floe' ); ?>">
		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'wp_page_menu' ) ); ?>
	</nav>
</header>
