<?php
/**
 * Footer component (Figma 30:25): on the Inverse surface. Sign-off (logo,
 * the site tagline, the action button), up to three link columns and a legal
 * row with the year in the site's timezone.
 *
 *   echo Floe\component( 'footer' );
 */

namespace Floe\Components;

defined( 'ABSPATH' ) || exit;

function footer( array $args = array() ): string {
	$tagline = get_bloginfo( 'description' );
	$action  = \Floe\component( 'navigation', array( 'location' => 'action', 'as' => 'button', 'arrow' => true ) );

	$columns = '';
	foreach ( array( 'footer-1', 'footer-2', 'footer-3' ) as $location ) {
		$columns .= \Floe\component( 'navigation', array( 'location' => $location, 'heading' => true, 'depth' => 1, 'class' => 'site-footer__column' ) );
	}

	$legal = sprintf(
		/* translators: 1: year, 2: site name, 3: legal line from the Customizer. */
		__( '© %1$s %2$s. %3$s', 'floe' ),
		wp_date( 'Y' ),
		get_bloginfo( 'name' ),
		get_theme_mod( 'floe_footer_legal', __( 'All rights reserved.', 'floe' ) )
	);

	return sprintf(
		'<footer class="site-footer surface-inverse"><div class="site-footer__inner">'
		. '<div class="site-footer__top"><div class="site-footer__signoff">%1$s%2$s%3$s</div>%4$s</div>'
		. '<div class="site-footer__bottom"><p>%5$s</p><p>%6$s</p></div>'
		. '</div></footer>',
		\Floe\component( 'logo', array( 'class' => 'site-footer__logo' ) ),
		'' !== trim( $tagline ) ? '<p class="site-footer__tagline">' . esc_html( $tagline ) . '</p>' : '',
		$action ? '<div class="site-footer__action">' . $action . '</div>' : '',
		$columns ? '<div class="site-footer__columns">' . $columns . '</div>' : '',
		esc_html( $legal ),
		esc_html__( 'Built with Floe', 'floe' )
	);
}

/** Appearance → Customize → Footer: the legal line after "© <year> <site name>.". */
function footer_customizer( \WP_Customize_Manager $customizer ): void {
	$customizer->add_section(
		'floe_footer',
		array(
			'title'       => __( 'Footer', 'floe' ),
			'description' => __( 'The footer sign-off is the site tagline (Site Identity). Menus set the link columns and the button.', 'floe' ),
			'priority'    => 120,
		)
	);
	$customizer->add_setting(
		'floe_footer_legal',
		array(
			'default'           => __( 'All rights reserved.', 'floe' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$customizer->add_control(
		'floe_footer_legal',
		array(
			'label'       => __( 'Legal line', 'floe' ),
			'description' => __( 'Shown after “© year site name.”', 'floe' ),
			'section'     => 'floe_footer',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', __NAMESPACE__ . '\\footer_customizer' );
