<?php
/** Shared site header. */

defined( 'ABSPATH' ) || exit;
?>
<header class="floe-site-header">
	<div class="floe-site-brand">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<?php $logo_path = get_template_directory() . '/Assets/Brand/floe-logo.svg'; ?>
			<a class="floe-site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( add_query_arg( 'ver', (string) filemtime( $logo_path ), get_template_directory_uri() . '/Assets/Brand/floe-logo.svg' ) ); ?>" width="208" height="64" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"></a>
		<?php endif; ?>
	</div>
	<nav class="floe-site-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'floe' ); ?>">
		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'fallback_cb' => 'wp_page_menu' ) ); ?>
	</nav>
</header>
